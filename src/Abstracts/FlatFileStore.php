<?php

namespace Alnaggar\Mujam\Abstracts;

use Alnaggar\Mujam\Contracts\FlatStore;
use Symfony\Component\Finder\Glob as SymfonyGlob;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

abstract class FlatFileStore extends FileStore implements FlatStore
{
    /**
     * {@inheritDoc}
     */
    public function get($key, $locale = null, $fallback = false): ?string
    {
        $translation = $this->getAll($locale)[$key] ?? null;

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
    public function getAll($locale = null, $fallback = false): array
    {
        $translations = [];

        $locale = $locale ?? $this->translator->getLocale();

        $files = $this->getFiles($locale);

        foreach ($files as $file) {
            $fileTranslations = $this->loadTranslations($file);
            $translations = array_replace($translations, $fileTranslations);
        }

        if ($fallback !== false) {
            $fallback = is_string($fallback) ? $fallback : $this->translator->getFallback();

            $fallbackTranslations = $this->getAll($fallback);
            $translations = array_replace($fallbackTranslations, $translations);
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
        $this->translator->setLoaded([]);

        return $this;
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
