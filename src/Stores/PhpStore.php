<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Muhawil\Dumpers\PhpFileDumper;
use Alnaggar\Muhawil\Loaders\PhpFileLoader;
use Alnaggar\Mujam\Abstracts\StructuredFileStore;

/**
 * @link https://github.com/ahmed-rashad-alnaggar/muhawil?tab=readme-ov-file#php
 */
class PhpStore extends StructuredFileStore
{
    /**
     * Create new PhpFileLoader instance to handle loading PHP translation files.
     * 
     * @return \Alnaggar\Muhawil\Loaders\PhpFileLoader
     */
    protected function constructLoader() : PhpFileLoader
    {
        return new PhpFileLoader;
    }

    /**
     * Create new PhpFileDumper instance to handle dumping PHP translation files.
     * 
     * @return \Alnaggar\Muhawil\Dumpers\PhpFileDumper
     */
    protected function constructDumper() : PhpFileDumper
    {
        return new PhpFileDumper;
    }

    /**
     * {@inheritDoc}
     */
    public function extensions() : array
    {
        return ['php'];
    }
}
