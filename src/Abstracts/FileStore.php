<?php

namespace Alnaggar\Mujam\Abstracts;

use Alnaggar\Mujam\Contracts\Store;
use Alnaggar\PhpTranslationFiles\Abstracts\TranslationFileDumper;
use Alnaggar\PhpTranslationFiles\Abstracts\TranslationFileLoader;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

abstract class FileStore implements Store
{
    /**
     * Laravel Translator instance.
     * 
     * @var \Illuminate\Translation\Translator
     */
    protected $translator;

    /**
     * Translations loader.
     * 
     * @var \Alnaggar\PhpTranslationFiles\Abstracts\TranslationFileLoader
     */
    protected $loader;

    /**
     * Translations dumper.
     * 
     * @var \Alnaggar\PhpTranslationFiles\Abstracts\TranslationFileDumper
     */
    protected $dumper;

    /**
     * Translation paths.
     * 
     * @var array<string>
     */
    protected $paths;

    /**
     * Indicates whether caching is enabled for translations.
     *
     * @var bool
     */
    protected $cacheEnabled;

    /**
     * The cache store used to store translation data.
     *
     * @var string|null
     */
    protected $cacheStore;

    /**
     * The prefix applied to all cache keys for translations.
     *
     * @var string
     */
    protected $cachePrefix;

    /**
     * The cache lifetime in seconds before translation data is invalidated.
     *
     * @var int|null
     */
    protected $cacheLifetime;

    /**
     * Create a new instance.
     * 
     * @param array<string>|string $paths
     * @param array<string, string>|bool $cache
     * @return void
     */
    public function __construct($paths, $cache = false)
    {
        $this->translator = app('translator');

        $this->setPaths((array) $paths)
            ->setLoader($this->constructLoader())
            ->setDumper($this->constructDumper())
            ->setCache($cache);
    }

    /**
     * Get translation paths.
     * 
     * @return array<string>
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Set translation paths.
     * 
     * @param array $paths
     * @return static
     */
    public function setPaths(array $paths)
    {
        $this->paths = $paths;

        return $this;
    }

    /**
     * Create new Loader instance to handle loading translations.
     * 
     * @return \Alnaggar\PhpTranslationFiles\Abstracts\TranslationFileLoader
     */
    abstract protected function constructLoader(): TranslationFileLoader;

    /**
     * Create new Dumper instance to handle dumping translations.
     * 
     * @return \Alnaggar\PhpTranslationFiles\Abstracts\TranslationFileDumper
     */
    abstract protected function constructDumper(): TranslationFileDumper;

    /**
     * Get the Loader instance being used.
     * 
     * @return \Alnaggar\PhpTranslationFiles\Abstracts\TranslationFileLoader
     */
    public function getLoader(): TranslationFileLoader
    {
        return $this->loader;
    }

    /**
     * Get the Dumper instance being used.
     * 
     * @return \Alnaggar\PhpTranslationFiles\Abstracts\TranslationFileDumper
     */
    public function getDumper(): TranslationFileDumper
    {
        return $this->dumper;
    }

    /**
     * Set the Loader to use.
     * 
     * @param \Alnaggar\PhpTranslationFiles\Abstracts\TranslationFileLoader $loader
     * @return static
     */
    public function setLoader(TranslationFileLoader $loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * Set the Dumper to use.
     * 
     * @param \Alnaggar\PhpTranslationFiles\Abstracts\TranslationFileDumper $dumper
     * @return static
     */
    public function setDumper(TranslationFileDumper $dumper)
    {
        $this->dumper = $dumper;

        return $this;
    }

    /**
     * Configure cache settings for translations.
     *
     * @param array|bool $cache
     * @return static
     */
    public function setCache($cache)
    {
        if ($cache) {
            $cache = (array) $cache;

            $this->cacheEnabled = $cache['enabled'] ?? $this->cacheEnabled ?? true;
            $this->cacheStore = $cache['store'] ?? $this->cacheStore ?? null;
            $this->cachePrefix = $cache['prefix'] ?? $this->cachePrefix ?? static::class;
            $this->cacheLifetime = $cache['lifetime'] ?? $this->cacheLifetime ?? 9999999999;
        } else {
            $this->cacheEnabled = false;
        }

        return $this;
    }

    /**
     * Create a Symfony Finder instance and search only the existing paths from the provided ones.
     * 
     * @return \Symfony\Component\Finder\Finder
     */
    protected function createFinder(): SymfonyFinder
    {
        $finder = SymfonyFinder::create();

        $paths = array_filter($this->getPaths(), static function (string $dir): bool {
            return is_dir($dir);
        });

        if (empty($paths)) {
            $dir = $this->getPaths()[0] ?? lang_path();
            File::ensureDirectoryExists($dir);

            $paths[] = $dir;
        }

        foreach ($paths as $path) {
            $finder->in($path);
        }

        return $finder;
    }

    /**
     * Load translations.
     * 
     * @param \Symfony\Component\Finder\SplFileInfo $file
     * @return array
     */
    protected function loadTranslations(SymfonySplFileInfo $file): array
    {
        $translations = [];
        $filepath = $file->getRealPath();

        // Check if the file exists.
        if ($filepath !== false) {
            $translations = $this->loader->load($filepath);
        }

        return $translations;
    }

    /**
     * Dump translations.
     * 
     * @param array $translations
     * @param \Symfony\Component\Finder\SplFileInfo $file
     * @return void
     */
    abstract protected function dumpTranslations(array $translations, SymfonySplFileInfo $file): void;

    /**
     * Return all supported file extensions.
     * 
     * @return array<string>
     */
    abstract public function extensions(): array;

    /**
     * {@inheritDoc}
     */
    public function has($key, $locale = null): bool
    {
        return ! is_null($this->get($key, $locale, false));
    }

    /**
     * Delete file.
     * 
     * @param string $path
     * @return void
     */
    protected function deleteFile(string $path): void
    {
        if (@unlink($path)) {
            clearstatcache(false, $path);
        }
    }
}
