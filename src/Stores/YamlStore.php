<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Muhawil\Dumpers\YamlFileDumper;
use Alnaggar\Muhawil\Loaders\YamlFileLoader;
use Alnaggar\Mujam\Abstracts\StructuredFileStore;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @property \Alnaggar\Muhawil\Loaders\YamlFileLoader $loader Translations loader.
 * @property \Alnaggar\Muhawil\Dumpers\YamlFileDumper $dumper Translations dumper.
 * 
 * @link https://github.com/ahmed-rashad-alnaggar/muhawil?tab=readme-ov-file#yaml
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
     * @return void
     */
    public function __construct($paths, bool $dry = true)
    {
        $this->dry = $dry;

        parent::__construct($paths);
    }

    /**
     * Create new YamlFileLoader instance to handle loading YAML translation files.
     * 
     * @return \Alnaggar\Muhawil\Loaders\YamlFileLoader
     */
    protected function constructLoader() : YamlFileLoader
    {
        return new YamlFileLoader;
    }

    /**
     * Create new YamlFileDumper instance to handle dumping YAML translation files.
     * 
     * @return \Alnaggar\Muhawil\Dumpers\YamlFileDumper
     */
    protected function constructDumper() : YamlFileDumper
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
    public function extensions() : array
    {
        return ['yaml', 'yml'];
    }
}
