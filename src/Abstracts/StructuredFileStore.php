<?php

namespace Alnaggar\Mujam\Abstracts;

use Alnaggar\Mujam\Contracts\StructuredStore;
use Illuminate\Support\Arr;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

abstract class StructuredFileStore extends FileStore implements StructuredStore
{
    /**
     * {@inheritDoc}
     */
    public function get($key, $locale = null, $fallback = false)
    {
        [$namespace, $group, $item] = $this->translator->parseKey($key);

        $translations = $this->getAll($group, $namespace, $locale);

        $translation = Arr::get($translations, $item);

        // For empty translation file return null.
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

                $translation = $this->get($key, $fallback);
            }
        }

        return $translation;
    }

    /**
     * {@inheritDoc}
     */
    public function getAll($group, $namespace = '*', $locale = null, $fallback = false): array
    {
        $translations = [];

        $locale = $locale ?? $this->translator->getLocale();

        $files = $this->getFiles($group, $namespace, $locale);

        foreach ($files as $file) {
            $fileTranslations = $this->loadTranslations($file);
            $translations = array_replace_recursive($translations, $fileTranslations);
        }

        if ($fallback !== false) {
            $fallback = is_string($fallback) ? $fallback : $this->translator->getFallback();

            $fallbackTranslations = $this->getAll($group, $namespace, $fallback);
            $translations = array_replace_recursive($fallbackTranslations, $translations);
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
        $translations = Arr::undot($translations);

        $locale = $locale ?? $this->translator->getLocale();

        $files = $this->getFilesForUpsert($group, $namespace, $locale);

        foreach ($files as $file) {
            $oldTranslations = $this->loadTranslations($file);
            $newTranslations = array_replace_recursive($oldTranslations, $translations);

            $this->dumpTranslations($newTranslations, $file);
        }

        // Clear cached translations.
        $this->translator->setLoaded([]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function remove(array $items, $group, $namespace = '*', $locale = null)
    {
        $locale = $locale ?? $this->translator->getLocale();

        $files = $this->getFiles($group, $namespace, $locale);

        foreach ($files as $file) {
            $translations = $this->loadTranslations($file);
            Arr::forget($translations, $items);

            if (empty($translations)) {
                $this->deleteFile($file->getPathname());
            } else {
                $this->dumpTranslations($translations, $file);
            }
        }

        // Clear cached translations.
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
        $this->translator->setLoaded([]);

        return $this;
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

        foreach ($namespaces as $namespace) {
            $namespacePath = $namespace === '*' ? '' : "vendor/{$namespace}/";

            foreach ($locales as $locale) {
                $resolvedFiles = $this->getFiles($group, $namespace, $locale);

                // If translations are being added for a newly supported namespace/locale
                // and the corresponding files do not exist,
                // construct the file path so that the dumper can create the appropriate file.
                if (empty($resolvedFiles)) {
                    // Ensure this is not a mass operation.
                    if ($group !== '*' && $locale !== '*') {
                        $subPath = "{$namespacePath}{$locale}/{$fileSubPath}";
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

            array_walk($namespaces, function (&$value) {
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
