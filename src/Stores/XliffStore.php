<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Muhawil\Dumpers\XliffFileDumper;
use Alnaggar\Muhawil\Loaders\XliffFileLoader;
use Alnaggar\Mujam\Abstracts\FlatFileStore;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @link https://github.com/ahmed-rashad-alnaggar/muhawil?tab=readme-ov-file#xliff
 */
class XliffStore extends FlatFileStore
{
    /**
     * Create new instance.
     * 
     * @param array<string>|string $paths
     * @param array<string, string> $metadata
     * @return void
     */
    public function __construct($paths, array $metadata = [])
    {
        parent::__construct($paths);

        $this->metadata['source_locale'] = $metadata['source_locale'] ?? $this->translator->getFallback();
    }

    /**
     * Create new XliffFileLoader instance to handle loading XLIFF translation files.
     * 
     * @return \Alnaggar\Muhawil\Loaders\XliffFileLoader
     */
    protected function constructLoader() : XliffFileLoader
    {
        return new XliffFileLoader;
    }

    /**
     * Create new XliffFileDumper instance to handle dumping XLIFF translation files.
     * 
     * @return \Alnaggar\Muhawil\Dumpers\XliffFileDumper
     */
    protected function constructDumper() : XliffFileDumper
    {
        return new XliffFileDumper;
    }

    /**
     * {@inheritDoc}
     */
    protected function dumpTranslations(array $translations, SymfonySplFileInfo $file) : void
    {
        $filepath = $file->getPathname();

        $targetLocale = $file->getFilenameWithoutExtension();

        $arguments = [
            'target_locale' => $targetLocale
        ] + $this->metadata;

        $this->dumper->dump($translations, $filepath, $arguments);
    }

    /**
     * {@inheritDoc}
     */
    public function extensions() : array
    {
        return ['xliff', 'xlf'];
    }
}
