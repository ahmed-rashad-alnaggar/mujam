<?php

namespace Alnaggar\Mujam\Stores;

use Alnaggar\Mujam\Contracts\StructuredStore;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Arr;

class DatabaseStore implements StructuredStore
{
    /**
     * Laravel Translator instance.
     * 
     * @var \Illuminate\Translation\Translator
     */
    protected $translator;

    /**
     * The database connection instance.
     *
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * The name of the translations table.
     *
     * @var string
     */
    protected $table;

    /**
     * Create new instance.
     * 
     * @param \Illuminate\Database\ConnectionInterface $connection
     * @param string $table
     */
    public function __construct(ConnectionInterface $connection, string $table)
    {
        $this->translator = app('translator');
        $this->table = $table;

        $this->setConnection($connection);
    }

    /**
     * {@inheritDoc}
     */
    public function get($key, $locale = null, $fallback = false)
    {
        [$namespace, $group, $item] = $this->translator->parseKey($key);

        $translations = $this->getAll($group, $namespace, $locale);

        $translation = Arr::get($translations, $item);

        if ($translation === []) {
            if ($translations === []) {
                if (is_null($item)) {
                    $translation = null;
                }
            }
        }

        if (is_null($translation)) {
            if ($fallback !== false) {
                $fallback = is_string($fallback) ? $fallback : $this->translator->getFallback();

                $translation = $this->get($key, $fallback);
            }
        }

        return $translation;
    }

    /**
     * {@inheritDoc}
     */
    public function getAll($group, $namespace = '*', $locale = null, $fallback = false) : array
    {
        $translations = [];

        $locale = $locale ?? $this->translator->getLocale();

        $values = $this->getRecords($group, $namespace, $locale)->get('value')->toArray();

        foreach ($values as $value) {
            $dbTranslations = json_decode($value->value, true);
            $translations = array_replace_recursive($translations, $dbTranslations);
        }

        if ($fallback !== false) {
            $fallback = is_string($fallback) ? $fallback : $this->translator->getFallback();

            $fallbackTranslations = $this->getAll($group, $namespace, $fallback);
            $translations = array_replace_recursive($fallbackTranslations, $translations);
        }

        return $translations;
    }

    /**
     * {@inheritDoc}
     */
    public function getStructure() : array
    {
        $structure = [];

        $records = $this->getRecords('*', null, '*')
            ->get(['namespace', 'group', 'locale'])
            ->toArray();

        foreach ($records as $record) {
            $namespace = $record->namespace;
            $group = $record->group;
            $locale = $record->locale;

            $structure[$namespace][$locale][] = $group;
        }

        return $structure;
    }

    /**
     * {@inheritDoc}
     */
    public function add(array $translations, $group, $namespace = '*', $locale = null)
    {
        return $this->update($translations, $group, $namespace, $locale);
    }

    /**
     * {@inheritDoc}
     */
    public function update(array $translations, $group, $namespace = '*', $locale = null)
    {
        $translations = Arr::undot($translations);

        $locale = $locale ?? $this->translator->getLocale();

        $records = $this->getRecordsForUpsert($group, $namespace, $locale);
        $records = array_map(function ($record) {
            return get_object_vars($record);
        }, $records);

        array_walk($records, function (&$record) use ($translations) {
            $oldTranslations = json_decode($record['value'], true);
            $newTranslations = array_replace_recursive($oldTranslations, $translations);

            $record['value'] = json_encode($newTranslations, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        });

        $this->getRecords('*', null, '*') // To get the table.
            ->upsert(
                $records,
                ['namespace', 'group', 'locale'],
                ['value']
            );

        $this->translator->setLoaded([]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function remove(array $items, $group, $namespace = '*', $locale = null)
    {
        $locale = $locale ?? $this->translator->getLocale();

        $records = $this->getRecords($group, $namespace, $locale)->get()->toArray();

        foreach ($records as $record) {
            $translations = json_decode($record->value, true);
            Arr::forget($translations, $items);

            $row = $this->getRecords($record->group, $record->namespace, $record->locale);

            if (empty($translations)) {
                $row->delete();
            } else {
                $row->update(['value' => json_encode($translations, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)]);
            }
        }

        $this->translator->setLoaded([]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function flush($group = '*', $namespace = null, $locale = '*')
    {
        $locale = $locale ?? $this->translator->getLocale();

        $this->getRecords($group, $namespace, $locale)->delete();

        $this->translator->setLoaded([]);

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function has($key, $locale = null) : bool
    {
        return ! is_null($this->get($key, $locale));
    }

    /**
     * Get translation records for upsert process. 
     * 
     * @param string $group
     * @param string $namespace
     * @param string $locale
     * @return array<array<string, string>>
     */
    protected function getRecordsForUpsert(string $group, string $namespace, string $locale) : array
    {
        $records = [];

        $namespaces = explode('|', $namespace);
        $locales = explode('|', $locale);

        foreach ($namespaces as $namespace) {
            foreach ($locales as $locale) {
                $resolvedRecords = $this->getRecords($group, $namespace, $locale)->get()->toArray();

                // Format a new record if there are no records matching the passed namespace, group, and locale,
                // so that the Builder's upsert function can create it.
                if (empty($resolvedRecords)) {
                    // Ensure this is not a mass operation.
                    if ($group !== '*' && $locale !== '*') {
                        $record = new \stdClass;
                        $record->namespace = $namespace;
                        $record->group = $group;
                        $record->locale = $locale;
                        $record->value = '{}';

                        $resolvedRecords[] = $record;
                    }
                }

                $records = array_merge($records, $resolvedRecords);
            }
        }

        return $records;
    }

    /**
     * Build a "where" query to get the matching records for the passed namespace(s), group, and locale(s).
     * 
     * @param string $group
     * @param string|null $namespace
     * @param string $locale
     * @return \Illuminate\Database\Query\Builder
     */
    protected function getRecords(string $group, ?string $namespace, string $locale) : Builder
    {
        $query = $this->getConnection()->table($this->table);

        if (! is_null($namespace)) {
            $query->whereIn('namespace', explode('|', $namespace));
        }

        if ($group !== '*') {
            $query->where('group', '=', $group);
        }

        if ($locale !== '*') {
            $query->whereIn('locale', explode('|', $locale));
        }

        return $query;
    }

    /**
     * Get store used connection.
     * 
     * @return \Illuminate\Database\ConnectionInterface
     */
    public function getConnection() : ConnectionInterface
    {
        return $this->connection;
    }

    /**
     * Set store to be used connection.
     * 
     * @param \Illuminate\Database\ConnectionInterface $connection
     * @return static
     */
    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;

        return $this;
    }
}
