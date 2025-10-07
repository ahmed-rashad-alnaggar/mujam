<?php

namespace Alnaggar\Mujam;

use Alnaggar\Mujam\Contracts\Factory as TranslationManager;
use Alnaggar\Mujam\Contracts\FlatStore;
use Alnaggar\Mujam\Contracts\StructuredStore;
use Illuminate\Contracts\Translation\Loader;

class TranslationLoader implements Loader
{
    /**
     * Translation manager instance.
     * 
     * @var \Alnaggar\Mujam\Contracts\Factory
     */
    protected $translationManager;

    /**
     * Paths to registered packages' JSON translation files.
     *
     * @var array<string>
     */
    protected $jsonPaths = [];

    /**
     * Paths to registered packages' PHP translation files.
     *
     * @var array<string, string>
     */
    protected $phpPaths = [];

    /**
     * Create a new instance.
     * 
     * @param \Alnaggar\Mujam\Contracts\Factory $translationManager
     */
    public function __construct(TranslationManager $translationManager)
    {
        $this->translationManager = $translationManager;
    }

    /**
     * Load the messages for the given locale.
     *
     * @param string $locale
     * @param string $group
     * @param string|null $namespace
     * @return array
     */
    public function load($locale, $group, $namespace = null): array
    {
        $namespace = $namespace ?? '*';

        if ($group === '*' && $namespace === '*') {
            return $this->loadFlat($locale);
        }

        return $this->loadStructured($locale, $group, $namespace);
    }

    /**
     * Load translations from the flat stores.
     * 
     * @param string $locale
     * @return array
     */
    protected function loadFlat($locale): array
    {
        $loaded = $this->loadPackagesJson($locale);
        $stores = $this->translationManager->getStores();

        foreach ($stores as $store) {
            if ($store instanceof FlatStore) {
                $translations = $store->getAll($locale, false);
                $loaded = array_replace($loaded, $translations);
            }
        }

        return $loaded;
    }

    /**
     * Load packages' JSON translations.
     * 
     * @param string $locale
     * @return array<string, string>
     */
    protected function loadPackagesJson(string $locale): array
    {
        $loaded = [];

        foreach ($this->jsonPaths as $path) {
            $filePath = "{$path}/{$locale}.json";

            if (file_exists($filePath)) {
                $content = file_get_contents($filePath);

                if ($content) {
                    $translations = json_decode($content, true);
                    $loaded = array_replace($loaded, $translations);
                }
            }
        }

        return $loaded;
    }

    /**
     * Load translations from the structured stores.
     * 
     * @param string $locale
     * @param string $group
     * @param string $namespace
     * @return array
     */
    protected function loadStructured($locale, $group, $namespace): array
    {
        $loaded = $this->loadPackagesPhp($locale, $group, $namespace);
        $stores = $this->translationManager->getStores();

        foreach ($stores as $store) {
            if ($store instanceof StructuredStore) {
                $translations = $store->getAll($group, $namespace, $locale, false);
                $loaded = array_replace_recursive($loaded, $translations);
            }
        }

        return $loaded;
    }

    /**
     * Load packages' PHP translations.
     * 
     * @param string $locale
     * @param string $group
     * @param string $namespace
     * @return array
     */
    protected function loadPackagesPhp(string $locale, string $group, string $namespace): array
    {
        $loaded = [];

        $path = $this->phpPaths[$namespace] ?? null;

        if (! is_null($path)) {
            $filePath = "{$path}/{$locale}/{$group}.php";

            if (file_exists($filePath)) {
                $loaded = require $filePath;
            }
        }

        return $loaded;
    }

    /**
     * Add a package's PHP language files to the loader under its corresponding namespace.
     *
     * @param string $namespace
     * @param string $path
     * @return void
     */
    public function addNamespace($namespace, $path): void
    {
        $this->phpPaths[$namespace] = $path;
    }

    /**
     * Adds a package's JSON path to the loader.
     *
     * @param string $path
     * @return void
     */
    public function addJsonPath($path): void
    {
        $this->jsonPaths[] = $path;
    }

    /**
     * Dummy for the interface implementation.
     *
     * @return array
     */
    public function namespaces(): array
    {
        return $this->phpPaths;
    }
}
