<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Mujam\Abstracts\StructuredFileStore;
use Alnaggar\PhpTranslationFiles\Formats\Php\PhpFileDumper;
use Alnaggar\PhpTranslationFiles\Formats\Php\PhpFileLoader;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @property \Alnaggar\PhpTranslationFiles\Formats\Php\PhpFileLoader $loader Translations loader.
 * @property \Alnaggar\PhpTranslationFiles\Formats\Php\PhpFileDumper $dumper Translations dumper.
 * 
 * @link https://github.com/ahmed-rashad-alnaggar/php-translation-files?tab=readme-ov-file#php
 */
class PhpStore extends StructuredFileStore
{
    /**
     * Create new PhpFileLoader instance to handle loading PHP translation files.
     * 
     * @return \Alnaggar\PhpTranslationFiles\Formats\Php\PhpFileLoader
     */
    protected function constructLoader(): PhpFileLoader
    {
        return new PhpFileLoader;
    }

    /**
     * Create new PhpFileDumper instance to handle dumping PHP translation files.
     * 
     * @return \Alnaggar\PhpTranslationFiles\Formats\Php\PhpFileDumper
     */
    protected function constructDumper(): PhpFileDumper
    {
        return new PhpFileDumper;
    }

    /**
     * {@inheritDoc}
     */
    protected function dumpTranslations(array $translations, SymfonySplFileInfo $file): void
    {
        $filepath = $file->getPathname();

        $this->dumper->dump($translations, $filepath);
    }

    /**
     * {@inheritDoc}
     */
    public function extensions(): array
    {
        return ['php'];
    }
}
