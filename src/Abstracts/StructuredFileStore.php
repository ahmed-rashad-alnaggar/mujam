<?php

namespace Alnaggar\Mujam\Abstracts;

use Alnaggar\Mujam\Contracts\StructuredStore;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

abstract class StructuredFileStore extends FileStore implements StructuredStore
{
    /**
     * {@inheritDoc}
     */
    public function get($key, $locale = null, $fallback = null)
    {
        [$namespace, $group, $item] = $this->translator->parseKey($key);

        $translations = $this->getAll($group, $namespace, $locale, false);

        $translation = Arr::get($translations, $item);

        // For empty translation file nullify the translation.
        if ($translation === []) {
            if ($translations === []) {
                if (is_null($item)) {
                    $translation = null;
                }
            }
        }

        if (is_null($translation)) {
            if ($fallback !== false) {
                $fallback = is_string($fallback) ? $fallback : $this->translator->getFallback();

                if ($locale !== $fallback) {
                    $translation = $this->get($key, $fallback, false);
                }
            }
        }

        return $translation;
    }

    /**
     * {@inheritDoc}
     */
    public function getAll($group, $namespace = '*', $locale = null, $fallback = null): array
    {
        $locale = $locale ?? $this->translator->getLocale();

        $translations = $this->remember($group, $namespace, $locale, function () use ($group, $namespace, $locale): array {
            $translations = [];

            $files = $this->getFiles($group, $namespace, $locale);

            foreach ($files as $file) {
                $fileTranslations = Arr::dot($this->loadTranslations($file));
                $translations = array_replace($translations, $fileTranslations);
            }

            return $translations;
        });

        if ($fallback !== false) {
            $fallback = is_string($fallback) ? $fallback : $this->translator->getFallback();

            if ($locale !== $fallback) {
                $fallbackTranslations = $this->getAll($group, $namespace, $fallback, false);
                $translations = array_replace($fallbackTranslations, $translations);
            }
        }

        return $translations;
    }

    /**
     * {@inheritDoc}
     */
    public function getStructure(): array
    {
        $structure = [];

        $files = $this->getFiles('*', null, '*');

        $shiftSegment = static function (string &$path) {
            $separatorPosition = strpos($path, DIRECTORY_SEPARATOR);

            $segment = substr($path, 0, $separatorPosition);
            $path = substr($path, $separatorPosition + 1);

            return $segment;
        };

        foreach ($files as $file) {
            $namespace = '*';
            $group = $locale = '';
            $relativePathname = $file->getRelativePathname();

            if (strncmp('vendor', $relativePathname, 6) === 0) {
                $shiftSegment($relativePathname); // Skip the 'vendor' segment
                $namespace = $shiftSegment($relativePathname);
            }

            $locale = $shiftSegment($relativePathname);
            $group = strstr($relativePathname, '.', true);
            // Replace directory separators in the group with '/' to follow Laravel's convention.
            $group = str_replace(DIRECTORY_SEPARATOR, '/', $group);

            if (! in_array($group, $structure[$namespace][$locale] ?? [])) {
                $structure[$namespace][$locale][] = $group;
            }
        }

        return $structure;
    }

    /**
     * {@inheritDoc}
     */
    public function add(array $translations, $group, $namespace = '*', $locale = null)
    {
        return $this->update($translations, $group, $namespace, $locale);
    }

    /**
     * {@inheritDoc}
     */
    public function update(array $translations, $group, $namespace = '*', $locale = null)
    {
        [$translations, $translationsToRemove] = collect(Arr::dot($translations))->partition(
            static function ($translation): bool {
                return ! is_null($translation);
            }
        );

        $locale = $locale ?? $this->translator->getLocale();

        $files = $this->getFilesForUpsert($group, $namespace, $locale);

        foreach ($files as $file) {
            $oldTranslations = Arr::dot($this->loadTranslations($file));
            $newTranslations = array_replace($oldTranslations, $translations->toArray());

            $this->dumpTranslations($newTranslations, $file);
        }

        if ($translationsToRemove->isNotEmpty()) {
            return $this->remove($translationsToRemove->keys()->toArray(), $group, $namespace, $locale);
        }

        // Clear cached translations.
        $this->forget($group, $namespace, $locale);
        $this->translator->setLoaded([]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function remove(array $items, $group, $namespace = '*', $locale = null)
    {
        $items = array_flip($items);

        $locale = $locale ?? $this->translator->getLocale();

        $files = $this->getFiles($group, $namespace, $locale);

        foreach ($files as $file) {
            $oldTranslations = Arr::dot($this->loadTranslations($file));
            $newTranslations = array_diff_key($oldTranslations, $items);

            if (empty($newTranslations)) {
                $this->deleteFile($file->getPathname());
            } else {
                $this->dumpTranslations($newTranslations, $file);
            }
        }

        // Clear cached translations.
        $this->forget($group, $namespace, $locale);
        $this->translator->setLoaded([]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function flush($group = '*', $namespace = null, $locale = '*')
    {
        $localeDirectories = [];

        $locale = $locale ?? $this->translator->getLocale();

        $files = $this->getFiles($group, $namespace, $locale);

        foreach ($files as $file) {
            $filepath = $file->getPathname();
            $relativePathname = $file->getRelativePathname();

            // Get base path.
            $localeDirectory = strstr($filepath, $relativePathname, true);

            // For simpler regex pattern.
            $relativePathname = str_replace(DIRECTORY_SEPARATOR, '/', $relativePathname);

            $pattern = strncmp('vendor', $relativePathname, 6) === 0
                ? '#^((?:[^/]+/){2}[^/]+).*#'
                : '#^([^/]+).*#';

            // Add locale path.
            $localeDirectory .= preg_replace($pattern, '$1', $relativePathname);

            if (! in_array($localeDirectory, $localeDirectories)) {
                $localeDirectories[] = $localeDirectory;
            }

            $this->deleteFile($filepath);
        }

        foreach ($localeDirectories as $localeDirectory) {
            $this->deleteEmptySubDirectories($localeDirectory);
        }

        // Clear cached translations.
        $this->forget($group, $namespace, $locale);
        $this->translator->setLoaded([]);

        return $this;
    }

    /**
     * Retrieve and cache translations for the given namespace, group, and locale.
     *
     * @param string $group
     * @param string|null $namespace
     * @param string $locale
     * @param \Closure $callback
     * @return array
     */
    protected function remember(string $group, ?string $namespace, string $locale, \Closure $callback): array
    {
        if ($this->cacheEnabled) {
            if (! is_null($namespace)) {
                if (! Str::contains($namespace, '|')) {
                    if (! Str::contains($locale, ['*', '|'])) {
                        if (! Str::contains($group, '*')) {
                            return Cache::store($this->cacheStore)
                                ->remember("{$this->cachePrefix}.{$namespace}.{$locale}.{$group}", $this->cacheLifetime, $callback);
                        }
                    }
                }
            }
        }

        return $callback();
    }

    /**
     * Forget cached translations for the given namespace(s), group(s), and locale(s).
     *
     * @param string $group
     * @param string|null $namespace
     * @param string $locale
     * @return void
     */
    protected function forget(string $group, ?string $namespace, string $locale): void
    {
        if (! $this->cacheEnabled) {
            return;
        }

        if (! is_null($namespace)) {
            if (! Str::contains($namespace, '|')) {
                if (! Str::contains($locale, ['*', '|'])) {
                    if (! Str::contains($group, '*')) {
                        Cache::store($this->cacheStore)
                            ->forget("{$this->cachePrefix}.{$namespace}.{$locale}.{$group}");

                        return;
                    }
                }
            }
        }

        $keyCombinations = [];
        $storeStructure = $this->getStructure();

        $targetNamespaces = is_null($namespace)
            ? array_keys($storeStructure)
            : explode('|', $namespace);

        $targetLocales = ($locale === '*')
            ? null // A null value will signify a wildcard match later
            : explode('|', $locale);

        foreach ($storeStructure as $currentNamespace => $locales) {
            if (! in_array($currentNamespace, $targetNamespaces)) {
                continue;
            }

            foreach ($locales as $currentLocale => $groups) {
                if (! is_null($targetLocales) && ! in_array($currentLocale, $targetLocales)) {
                    continue;
                }

                foreach ($groups as $currentGroup) {
                    if ($group === '*' || $currentGroup === $group) {
                        $keyCombinations[] = "{$currentNamespace}.{$currentLocale}.{$currentGroup}";
                    }
                }
            }
        }

        foreach ($keyCombinations as $keyCombination) {
            Cache::store($this->cacheStore)
                ->forget("{$this->cachePrefix}.{$keyCombination}");
        }
    }

    /**
     * Get translation files for upsert process. 
     * 
     * @param string $group
     * @param string $namespace
     * @param string $locale
     * @return array<\Symfony\Component\Finder\SplFileInfo>
     */
    protected function getFilesForUpsert(string $group, string $namespace, string $locale): array
    {
        $files = [];

        $namespaces = explode('|', $namespace);
        $locales = explode('|', $locale);

        $basePath = $this->getPaths()[0];
        [$filename, $fileSubPath] = $this->parseGroup($group);
        $extension = $this->extensions()[0];

        foreach ($namespaces as $targetNamespace) {
            $namespacePath = $targetNamespace === '*' ? '' : "vendor/{$targetNamespace}/";

            foreach ($locales as $targetLocale) {
                $resolvedFiles = $this->getFiles($group, $targetNamespace, $targetLocale);

                // If translations are being added for a newly supported namespace/locale
                // and the corresponding files do not exist,
                // construct the file path so that the dumper can create the appropriate file.
                if (empty($resolvedFiles)) {
                    // Ensure this is not a mass operation.
                    if ($group !== '*' && $targetLocale !== '*') {
                        $subPath = "{$namespacePath}{$targetLocale}/{$fileSubPath}";
                        $subPathname = "{$subPath}/{$filename}.{$extension}";

                        $resolvedFiles[] = new SymfonySplFileInfo("{$basePath}/{$subPathname}", $subPath, $subPathname);
                    }
                }

                $files = array_merge($files, $resolvedFiles);
            }
        }

        return $files;
    }

    /**
     * Get translation files matching the passed namespace(s), group, and locale(s).
     * 
     * @param string $group
     * @param string|null $namespace
     * @param string $locale
     * @return array<\Symfony\Component\Finder\SplFileInfo>
     */
    protected function getFiles(string $group, ?string $namespace, string $locale): array
    {
        [$filename, $fileSubPath] = $this->parseGroup($group);

        // Glob patterns.
        $extensionPattern = '{'.implode(',', $this->extensions()).'}';
        $namePattern = "{$filename}.{$extensionPattern}";

        // Regex patterns.
        if (! is_null($namespace)) {
            $namespaces = explode('|', $namespace);

            array_walk($namespaces, function (&$value): void {
                if ($value === '*') {
                    $value = '^(?!vendor/)';
                } else {
                    $value = "^vendor/{$value}/";
                }
            });

            $namespacePattern = '('.implode('|', $namespaces).')';
        } else {
            $namespacePattern = '';
        }

        $localePattern = $locale === '*' ? '.+' : "({$locale})";
        $pathPattern = "#{$namespacePattern}{$localePattern}/{$fileSubPath}#";

        $files = $this->createFinder()
            ->files()
            ->path($pathPattern)
            ->name($namePattern);

        return iterator_to_array($files, false);
    }

    /**
     * Parse group into filename and fileSubPath.
     * 
     * @param string $group
     * @return array
     */
    protected function parseGroup(string $group): array
    {
        $directories = explode('/', $group);

        $filename = array_pop($directories);
        $fileSubPath = implode('/', $directories);

        return [$filename, $fileSubPath];
    }

    /**
     * Recursively deletes empty subdirectories.
     * 
     * This function checks if a given directory is empty. If the directory contains subdirectories,
     * it recursively checks and deletes those subdirectories if they are empty. If the top-level
     * directory is found to be empty after this process, it deletes the top-level directory as well.
     *
     * @param string $path
     * @return void
     */
    protected function deleteEmptySubDirectories(string $path): void
    {
        if (! is_dir($path)) {
            return;
        }

        $nodes = glob("{$path}/*");

        foreach ($nodes as $node) {
            $this->deleteEmptySubDirectories($node);
        }

        @rmdir($path);
    }
}
