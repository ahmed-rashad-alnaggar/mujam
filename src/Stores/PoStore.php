<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Muhawil\Dumpers\PoFileDumper;
use Alnaggar\Muhawil\Loaders\PoFileLoader;
use Alnaggar\Mujam\Abstracts\FlatFileStore;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @link https://github.com/ahmed-rashad-alnaggar/muhawil?tab=readme-ov-file#po
 */
class PoStore extends FlatFileStore
{
    /**
     * Create new PoFileLoader instance to handle loading PO translation files.
     * 
     * @return \Alnaggar\Muhawil\Loaders\PoFileLoader
     */
    protected function constructLoader() : PoFileLoader
    {
        return new PoFileLoader;
    }

    /**
     * Create new PoFileDumper instance to handle dumping PO translation files.
     * 
     * @return \Alnaggar\Muhawil\Dumpers\PoFileDumper
     */
    protected function constructDumper() : PoFileDumper
    {
        return new PoFileDumper;
    }

    /**
     * {@inheritDoc}
     */
    protected function dumpTranslations(array $translations, SymfonySplFileInfo $file) : void
    {
        $filepath = $file->getPathname();

        $locale = $file->getFilenameWithoutExtension();

        $metadata = [
            'Language' => $locale
        ] + $this->metadata;

        $this->dumper->dump($translations, $filepath, $metadata);
    }

    /**
     * {@inheritDoc}
     */
    public function extensions() : array
    {
        return ['po'];
    }
}
