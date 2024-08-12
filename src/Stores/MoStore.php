<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Muhawil\Dumpers\MoFileDumper;
use Alnaggar\Muhawil\Loaders\MoFileLoader;
use Alnaggar\Mujam\Abstracts\FlatFileStore;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @link https://github.com/ahmed-rashad-alnaggar/muhawil?tab=readme-ov-file#mo
 */
class MoStore extends FlatFileStore
{
    /**
     * Create new MoFileLoader instance to handle loading MO translation files.
     * 
     * @return \Alnaggar\Muhawil\Loaders\MoFileLoader
     */
    protected function constructLoader() : MoFileLoader
    {
        return new MoFileLoader;
    }

    /**
     * Create new MoFileDumper instance to handle dumping MO translation files.
     * 
     * @return \Alnaggar\Muhawil\Dumpers\MoFileDumper
     */
    protected function constructDumper() : MoFileDumper
    {
        return new MoFileDumper;
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
        return ['mo'];
    }
}
