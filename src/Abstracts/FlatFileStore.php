<?php

namespace Alnaggar\Mujam\Abstracts;

use Alnaggar\Mujam\Contracts\FlatStore;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Symfony\Component\Finder\Glob as SymfonyGlob;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

abstract class FlatFileStore extends FileStore implements FlatStore
{
    /**
     * {@inheritDoc}
     */
    public function get($key, $locale = null, $fallback = null): ?string
    {
        $translation = $this->getAll($locale, false)[$key] ?? null;

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
    public function getAll($locale = null, $fallback = null): array
    {
        $locale = $locale ?? $this->translator->getLocale();

        $translations = $this->remember($locale, function () use ($locale): array {
            $translations = [];

            $files = $this->getFiles($locale);

            foreach ($files as $file) {
                $fileTranslations = $this->loadTranslations($file);
                $translations = array_replace($translations, $fileTranslations);
            }

            return $translations;
        });

        if ($fallback !== false) {
            $fallback = is_string($fallback) ? $fallback : $this->translator->getFallback();

            if ($locale !== $fallback) {
                $fallbackTranslations = $this->getAll($fallback, false);
                $translations = array_replace($fallbackTranslations, Arr::whereNotNull($translations));
            }
        }

        return $translations;
    }

    /**
     * {@inheritDoc}
     */
    public function getLocales(): array
    {
        $locales = [];

        $files = $this->getFiles('*');

        foreach ($files as $file) {
            $locale = $file->getFilenameWithoutExtension();

            if (! in_array($locale, $locales)) {
                $locales[] = $locale;
            }
        }

        return $locales;
    }

    /**
     * {@inheritDoc}
     */
    public function add(array $translations, $locale = null)
    {
        return $this->update($translations, $locale);
    }

    /**
     * {@inheritDoc}
     */
    public function update(array $translations, $locale = null)
    {
        $locale = $locale ?? $this->translator->getLocale();

        $files = $this->getFilesForUpsert($locale);

        foreach ($files as $file) {
            $oldTranslations = $this->loadTranslations($file);
            $newTranslations = array_replace($oldTranslations, $translations);

            $this->dumpTranslations($newTranslations, $file);
        }

        // Clear cached translations.
        $this->forget($locale);
        $this->translator->setLoaded([]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function remove(array $keys, $locale = null)
    {
        $keys = array_flip($keys);

        $locale = $locale ?? $this->translator->getLocale();

        $files = $this->getFiles($locale);

        foreach ($files as $file) {
            $oldTranslations = $this->loadTranslations($file);
            $newTranslations = array_diff_key($oldTranslations, $keys);

            if (empty($newTranslations)) {
                $this->deleteFile($file->getPathname());
            } else {
                $this->dumpTranslations($newTranslations, $file);
            }
        }

        // Clear cached translations.
        $this->forget($locale);
        $this->translator->setLoaded([]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function flush($locale = '*')
    {
        $locale = $locale ?? $this->translator->getLocale();

        $files = $this->getFiles($locale);

        foreach ($files as $file) {
            $this->deleteFile($file->getPathname());
        }

        // Clear cached translations.
        $this->forget($locale);
        $this->translator->setLoaded([]);

        return $this;
    }

    /**
     * Retrieve and cache translations for the given locale.
     *
     * @param string $locale
     * @param \Closure $callback
     * @return array
     */
    protected function remember(string $locale, \Closure $callback): array
    {
        if ($this->cacheEnabled) {
            if (! Str::contains($locale, ['*', '|'])) {
                return Cache::store($this->cacheStore)
                    ->remember("{$this->cachePrefix}.{$locale}", $this->cacheLifetime, $callback);
            }
        }

        return $callback();
    }

    /**
     * Forget cached translations for the given locale(s).
     *
     * @param string $locale
     * @return void
     */
    protected function forget(string $locale): void
    {
        if ($this->cacheEnabled) {
            $locales = $locale === '*' ? $this->getLocales() : explode('|', $locale);

            foreach ($locales as $targetLocale) {
                Cache::store($this->cacheStore)
                    ->forget("{$this->cachePrefix}.{$targetLocale}");
            }
        }
    }

    /**
     * Get translation files for upsert process. 
     * 
     * @param string $locale
     * @return array<\Symfony\Component\Finder\SplFileInfo>
     */
    protected function getFilesForUpsert(string $locale): array
    {
        $files = [];

        $locales = explode('|', $locale);

        $basePath = $this->getPaths()[0];
        $extension = $this->extensions()[0];

        foreach ($locales as $locale) {
            $resolvedFiles = $this->getFiles($locale);

            // If translations are being added for a newly supported locale
            // and the corresponding files do not exist,
            // construct the file path so that the dumper can create the appropriate file.
            if (empty($resolvedFiles)) {
                // Ensure this is not a mass operation.
                if ($locale !== '*') {
                    $subPathname = "{$locale}.{$extension}";

                    $resolvedFiles[] = new SymfonySplFileInfo("{$basePath}/{$subPathname}", '', $subPathname);
                }
            }

            $files = array_merge($files, $resolvedFiles);
        }

        return $files;
    }

    /**
     * Get translation files.
     * 
     * @param string $locale
     * @return array<\Symfony\Component\Finder\SplFileInfo>
     */
    protected function getFiles(string $locale): array
    {
        // Glob patterns.
        $localePattern = '{'.str_replace('|', ',', $locale).'}';
        $extensionPattern = '{'.implode(',', $this->extensions()).'}';

        // Convert to regex here because Symfony interprets glob patterns 
        // that start and end with {} as regex, preventing proper conversion to regex.
        $namePattern = SymfonyGlob::toRegex("{$localePattern}.{$extensionPattern}");

        $files = $this->createFinder()
            ->files()
            ->name($namePattern)
            ->depth(0);

        return iterator_to_array($files, false);
    }
}
