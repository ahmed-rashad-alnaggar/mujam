<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Muhawil\Dumpers\PoFileDumper;
use Alnaggar\Muhawil\Loaders\PoFileLoader;
use Alnaggar\Mujam\Abstracts\FlatFileStore;
use Symfony\Component\Finder\SplFileInfo as SymfonySplFileInfo;

/**
 * @property \Alnaggar\Muhawil\Loaders\PoFileLoader $loader Translations loader.
 * @property \Alnaggar\Muhawil\Dumpers\PoFileDumper $dumper Translations dumper.
 * 
 * @link https://github.com/ahmed-rashad-alnaggar/muhawil?tab=readme-ov-file#po
 */
class PoStore extends FlatFileStore
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
     * @param array<string, string>|bool $cache
     * @param array $metadata
     */
    public function __construct($paths, ?string $contextDelimiter = '::', string $pluralDelimiter = '|', array $metadata = [], $cache = false)
    {
        $this->contextDelimiter = $contextDelimiter;
        $this->pluralDelimiter = $pluralDelimiter;
        $this->metadata = $metadata;

        parent::__construct($paths, $cache);
    }

    /**
     * Create new PoFileLoader instance to handle loading PO translation files.
     * 
     * @return \Alnaggar\Muhawil\Loaders\PoFileLoader
     */
    protected function constructLoader(): PoFileLoader
    {
        return new PoFileLoader($this->contextDelimiter, $this->pluralDelimiter);
    }

    /**
     * Create new PoFileDumper instance to handle dumping PO translation files.
     * 
     * @return \Alnaggar\Muhawil\Dumpers\PoFileDumper
     */
    protected function constructDumper(): PoFileDumper
    {
        return new PoFileDumper($this->contextDelimiter, $this->pluralDelimiter);
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
        return ['po'];
    }
}
