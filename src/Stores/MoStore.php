<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Muhawil\Dumpers\MoFileDumper;
use Alnaggar\Muhawil\Loaders\MoFileLoader;
use Alnaggar\Mujam\Abstracts\FlatFileStore;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @property \Alnaggar\Muhawil\Loaders\MoFileLoader $loader Translations loader.
 * @property \Alnaggar\Muhawil\Dumpers\MoFileDumper $dumper Translations dumper.
 * 
 * @link https://github.com/ahmed-rashad-alnaggar/muhawil?tab=readme-ov-file#mo
 */
class MoStore extends FlatFileStore
{
    /**
     * The delimiter used to separate message context from message ID in the translation key.
     * If null, message context is not included in the translation key,
     * which may cause later message ID entries to override previous ones with the same value.
     * 
     * @var string|null
     */
    protected $contextDelimiter;

    /**
     * The delimiter used to separate plural strings in the translation key and value.
     * 
     * @var string
     */
    protected $pluralDelimiter;

    /**
     * An associative array to include additional information about the translation file,
     * such as language, authorship, or pluralization rules.
     * 
     * @var array<string, string>
     */
    protected $metadata;

    /**
     * Create a new instance.
     * 
     * @param array<string>|string $paths
     * @param string|null $contextDelimiter
     * @param string $pluralDelimiter
     * @param array $metadata
     */
    public function __construct($paths, ?string $contextDelimiter = '::', string $pluralDelimiter = '|', array $metadata = [])
    {
        $this->contextDelimiter = $contextDelimiter;
        $this->pluralDelimiter = $pluralDelimiter;
        $this->metadata = $metadata;

        parent::__construct($paths);
    }

    /**
     * Create new MoFileLoader instance to handle loading MO translation files.
     * 
     * @return \Alnaggar\Muhawil\Loaders\MoFileLoader
     */
    protected function constructLoader(): MoFileLoader
    {
        return new MoFileLoader($this->contextDelimiter, $this->pluralDelimiter);
    }

    /**
     * Create new MoFileDumper instance to handle dumping MO translation files.
     * 
     * @return \Alnaggar\Muhawil\Dumpers\MoFileDumper
     */
    protected function constructDumper(): MoFileDumper
    {
        return new MoFileDumper($this->contextDelimiter, $this->pluralDelimiter);
    }

    /**
     * {@inheritDoc}
     */
    protected function dumpTranslations(array $translations, SymfonySplFileInfo $file): void
    {
        $filepath = $file->getPathname();

        $locale = $file->getFilenameWithoutExtension();

        $metadata = ['Language' => $locale] + $this->metadata;

        $this->dumper->dump($translations, $filepath, $metadata);
    }

    /**
     * {@inheritDoc}
     */
    public function extensions(): array
    {
        return ['mo'];
    }
}
