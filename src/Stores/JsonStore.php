<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Muhawil\Dumpers\JsonFileDumper;
use Alnaggar\Muhawil\Loaders\JsonFileLoader;
use Alnaggar\Mujam\Abstracts\FlatFileStore;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @property \Alnaggar\Muhawil\Loaders\JsonFileLoader $loader Translations loader.
 * @property \Alnaggar\Muhawil\Dumpers\JsonFileDumper $dumper Translations dumper.
 * 
 * @link https://github.com/ahmed-rashad-alnaggar/muhawil?tab=readme-ov-file#json
 */
class JsonStore extends FlatFileStore
{
    /**
     * A bitmask that controls the behavior of the JSON encoding process.
     * 
     * @var int
     */
    protected $flags;

    /**
     * Create a new instance.
     * 
     * @param array<string>|string $paths
     * @param int $flags
     * @return void
     */
    public function __construct($paths, int $flags = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
    {
        $this->flags = $flags;

        parent::__construct($paths);
    }

    /**
     * Create new JsonFileLoader instance to handle loading JSON translation files.
     * 
     * @return \Alnaggar\Muhawil\Loaders\JsonFileLoader
     */
    protected function constructLoader(): JsonFileLoader
    {
        return new JsonFileLoader;
    }

    /**
     * Create new JsonFileDumper instance to handle dumping JSON translation files.
     * 
     * @return \Alnaggar\Muhawil\Dumpers\JsonFileDumper
     */
    protected function constructDumper(): JsonFileDumper
    {
        return new JsonFileDumper;
    }

    /**
     * {@inheritDoc}
     */
    protected function dumpTranslations(array $translations, SymfonySplFileInfo $file): void
    {
        $filepath = $file->getPathname();

        $this->dumper->dump($translations, $filepath, $this->flags);
    }

    /**
     * {@inheritDoc}
     */
    public function extensions(): array
    {
        return ['json'];
    }
}
