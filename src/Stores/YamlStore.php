<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Mujam\Abstracts\StructuredFileStore;
use Alnaggar\PhpTranslationFiles\Formats\Yaml\YamlFileDumper;
use Alnaggar\PhpTranslationFiles\Formats\Yaml\YamlFileLoader;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @property \Alnaggar\PhpTranslationFiles\Formats\Yaml\YamlFileLoader $loader Translations loader.
 * @property \Alnaggar\PhpTranslationFiles\Formats\Yaml\YamlFileDumper $dumper Translations dumper.
 * 
 * @link https://github.com/ahmed-rashad-alnaggar/php-translation-files?tab=readme-ov-file#yaml
 */
class YamlStore extends StructuredFileStore
{
    /**
     * Determines whether to generate anchors and aliases for similar mappings in the YAML structure.
     * 
     * @var bool
     */
    protected $dry;

    /**
     * Create a new instance.
     * 
     * @param array<string>|string $paths
     * @param bool $dry
     * @param array<string, string>|bool $cache
     * @return void
     */
    public function __construct($paths, bool $dry = true, $cache)
    {
        $this->dry = $dry;

        parent::__construct($paths, $cache);
    }

    /**
     * Create new YamlFileLoader instance to handle loading YAML translation files.
     * 
     * @return \Alnaggar\PhpTranslationFiles\Formats\Yaml\YamlFileLoader
     */
    protected function constructLoader(): YamlFileLoader
    {
        return new YamlFileLoader;
    }

    /**
     * Create new YamlFileDumper instance to handle dumping YAML translation files.
     * 
     * @return \Alnaggar\PhpTranslationFiles\Formats\Yaml\YamlFileDumper
     */
    protected function constructDumper(): YamlFileDumper
    {
        return new YamlFileDumper;
    }

    /**
     * {@inheritDoc}
     */
    protected function dumpTranslations(array $translations, SymfonySplFileInfo $file): void
    {
        $filepath = $file->getPathname();

        $this->dumper->dump($translations, $filepath, $this->dry);
    }

    /**
     * {@inheritDoc}
     */
    public function extensions(): array
    {
        return ['yaml', 'yml'];
    }
}
