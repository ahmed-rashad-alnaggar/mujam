<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Mujam\Abstracts\FlatFileStore;
use Alnaggar\PhpTranslationFiles\Formats\Json\JsonFileDumper;
use Alnaggar\PhpTranslationFiles\Formats\Json\JsonFileLoader;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @property \Alnaggar\PhpTranslationFiles\Formats\Json\JsonFileLoader $loader Translations loader.
 * @property \Alnaggar\PhpTranslationFiles\Formats\Json\JsonFileDumper $dumper Translations dumper.
 * 
 * @link https://github.com/ahmed-rashad-alnaggar/php-translation-files?tab=readme-ov-file#json
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
     * @param array<string, string>|bool $cache
     * @return void
     */
    public function __construct($paths, int $flags = JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT, $cache = false)
    {
        $this->flags = $flags;

        parent::__construct($paths, $cache);
    }

    /**
     * Create new JsonFileLoader instance to handle loading JSON translation files.
     * 
     * @return \Alnaggar\PhpTranslationFiles\Formats\Json\JsonFileLoader
     */
    protected function constructLoader(): JsonFileLoader
    {
        return new JsonFileLoader;
    }

    /**
     * Create new JsonFileDumper instance to handle dumping JSON translation files.
     * 
     * @return \Alnaggar\PhpTranslationFiles\Formats\Json\JsonFileDumper
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
