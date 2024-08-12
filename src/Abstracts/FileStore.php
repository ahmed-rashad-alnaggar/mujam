<?php

namespace Alnaggar\Mujam\Abstracts;

use Alnaggar\Muhawil\Interfaces\Dumper;
use Alnaggar\Muhawil\Interfaces\Loader;
use Alnaggar\Mujam\Contracts\Store;
use Symfony\Component\Finder\Exception\DirectoryNotFoundException as SymfonyDirectoryNotFoundException;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

abstract class FileStore implements Store
{
    /**
     * Laravel Translator instance.
     * 
     * @var \Illuminate\Contracts\Translation\Translator
     */
    protected $translator;

    /**
     * Translations loader.
     * 
     * @var \Alnaggar\Muhawil\Interfaces\Loader
     */
    protected $loader;

    /**
     * Translations dumper.
     * 
     * @var \Alnaggar\Muhawil\Interfaces\Dumper
     */
    protected $dumper;

    /**
     * Translation paths.
     * 
     * @var array<string>
     */
    protected $paths;

    /**
     * Metadata for the file.
     * 
     * @var array<string, string>
     */
    protected $metadata;

    /**
     * Create new instance.
     * 
     * @param array<string>|string $paths
     * @param array<string, string> $metadata
     * @return void
     */
    public function __construct($paths, array $metadata = [])
    {
        $this->translator = app('translator');
        $this->metadata = $metadata;

        $this->setPaths((array) $paths);
        $this->setLoader($this->constructLoader());
        $this->setDumper($this->constructDumper());
    }

    /**
     * Get translation paths.
     * 
     * @return array<string>
     */
    public function getPaths() : array
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
     * @return \Alnaggar\Muhawil\Interfaces\Loader
     */
    abstract protected function constructLoader() : Loader;

    /**
     * Create new Dumper instance to handle dumping translations.
     * 
     * @return \Alnaggar\Muhawil\Interfaces\Dumper
     */
    abstract protected function constructDumper() : Dumper;

    /**
     * Get the Loader instance being used.
     * 
     * @return \Alnaggar\Muhawil\Interfaces\Loader
     */
    public function getLoader() : Loader
    {
        return $this->loader;
    }

    /**
     * Get the Dumper instance being used.
     * 
     * @return \Alnaggar\Muhawil\Interfaces\Dumper
     */
    public function getDumper() : Dumper
    {
        return $this->dumper;
    }

    /**
     * Set the Loader to use.
     * 
     * @param \Alnaggar\Muhawil\Interfaces\Loader $loader
     * @return static
     */
    public function setLoader(Loader $loader)
    {
        $this->loader = $loader;

        return $this;
    }

    /**
     * Set the Dumper to use.
     * 
     * @param \Alnaggar\Muhawil\Interfaces\Dumper $dumper
     * @return static
     */
    public function setDumper(Dumper $dumper)
    {
        $this->dumper = $dumper;

        return $this;
    }

    /**
     * Create a Symfony Finder instance and search only the existing paths from the provided ones.
     * 
     * @return \Symfony\Component\Finder\Finder
     */
    protected function createFinder() : SymfonyFinder
    {
        $finder = SymfonyFinder::create();

        foreach ($this->getPaths() as $path) {
            try {
                $finder->in($path);
            } catch (SymfonyDirectoryNotFoundException $e) {
                continue;
            }
        }

        return $finder;
    }

    /**
     * Load translations.
     * 
     * @param \Symfony\Component\Finder\SplFileInfo $file
     * @return array
     */
    protected function loadTranslations(SymfonySplFileInfo $file) : array
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
    protected function dumpTranslations(array $translations, SymfonySplFileInfo $file) : void
    {
        $filepath = $file->getPathname();

        $this->dumper->dump($translations, $filepath, $this->metadata);
    }

    /**
     * Return all supported file extensions.
     * 
     * @return array<string>
     */
    abstract public function extensions() : array;

    /**
     * {@inheritDoc}
     */
    public function has($key, $locale = null) : bool
    {
        return ! is_null($this->get($key, $locale));
    }

    /**
     * Delete file.
     * 
     * @param string $path
     * @return void
     */
    protected function deleteFile(string $path) : void
    {
        if (@unlink($path)) {
            clearstatcache(false, $path);
        }
    }
}
