<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Muhawil\Dumpers\YamlFileDumper;
use Alnaggar\Muhawil\Loaders\YamlFileLoader;
use Alnaggar\Mujam\Abstracts\StructuredFileStore;

/**
 * @link https://github.com/ahmed-rashad-alnaggar/muhawil?tab=readme-ov-file#yaml
 */
class YamlStore extends StructuredFileStore
{
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
    public function extensions() : array
    {
        return ['yaml', 'yml'];
    }
}
