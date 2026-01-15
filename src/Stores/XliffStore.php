<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Mujam\Abstracts\FlatFileStore;
use Alnaggar\PhpTranslationFiles\Formats\Xliff\XliffFileDumper;
use Alnaggar\PhpTranslationFiles\Formats\Xliff\XliffFileLoader;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @property \Alnaggar\PhpTranslationFiles\Formats\Xliff\XliffFileLoader $loader Translations loader.
 * @property \Alnaggar\PhpTranslationFiles\Formats\Xliff\XliffFileDumper $dumper Translations dumper.
 * 
 * @link https://github.com/ahmed-rashad-alnaggar/php-translation-files?tab=readme-ov-file#xliff
 */
class XliffStore extends FlatFileStore
{
    /**
     * The source language of the translations.
     * 
     * @var string
     */
    protected $sourceLocale;

    /**
     * Determines whether to confrom to XLIFF 1.2 (true) or XLIFF 2.0 (false).
     * 
     * @var bool
     */
    protected $legacy;

    /**
     * Create a new instance.
     * 
     * @param array<string>|string $paths
     * @param string $sourceLocale
     * @param bool $legacy
     * @param array<string, string>|bool $cache
     * @return void
     */
    public function __construct($paths, string $sourceLocale = 'en', bool $legacy = false, $cache = false)
    {
        $this->sourceLocale = $sourceLocale;
        $this->legacy = $legacy;

        parent::__construct($paths, $cache);
    }

    /**
     * Create new XliffFileLoader instance to handle loading XLIFF translation files.
     * 
     * @return \Alnaggar\PhpTranslationFiles\Formats\Xliff\XliffFileLoader
     */
    protected function constructLoader(): XliffFileLoader
    {
        return new XliffFileLoader;
    }

    /**
     * Create new XliffFileDumper instance to handle dumping XLIFF translation files.
     * 
     * @return \Alnaggar\PhpTranslationFiles\Formats\Xliff\XliffFileDumper
     */
    protected function constructDumper(): XliffFileDumper
    {
        return new XliffFileDumper;
    }

    /**
     * {@inheritDoc}
     */
    protected function dumpTranslations(array $translations, SymfonySplFileInfo $file): void
    {
        $filepath = $file->getPathname();

        $targetLocale = $file->getFilenameWithoutExtension();

        $this->dumper->dump($translations, $filepath, $this->sourceLocale, $targetLocale, $this->legacy, $targetLocale);
    }

    /**
     * {@inheritDoc}
     */
    public function extensions(): array
    {
        return ['xliff', 'xlf'];
    }
}
