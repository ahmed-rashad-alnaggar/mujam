<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Muhawil\Dumpers\JsonFileDumper;
use Alnaggar\Muhawil\Loaders\JsonFileLoader;
use Alnaggar\Mujam\Abstracts\FlatFileStore;

/**
 * @link https://github.com/ahmed-rashad-alnaggar/muhawil?tab=readme-ov-file#json
 */
class JsonStore extends FlatFileStore
{
    /**
     * Create new JsonFileLoader instance to handle loading JSON translation files.
     * 
     * @return \Alnaggar\Muhawil\Loaders\JsonFileLoader
     */
    protected function constructLoader() : JsonFileLoader
    {
        return new JsonFileLoader;
    }

    /**
     * Create new JsonFileDumper instance to handle dumping JSON translation files.
     * 
     * @return \Alnaggar\Muhawil\Dumpers\JsonFileDumper
     */
    protected function constructDumper() : JsonFileDumper
    {
        return new JsonFileDumper;
    }

    /**
     * {@inheritDoc}
     */
    public function extensions() : array
    {
        return ['json'];
    }
}
