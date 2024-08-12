<?php

namespace Alnaggar\Mujam\Contracts;

/**
 * @method array getAll() getAll(string|null $locale = null, string|bool|null $fallback = false) Retrieve all translations for the specified locale. If `fallback` is set, include translations from the fallback locale that are not overridden by the specified `locale`.
 * @method array getAll() getAll(string $group, string $namespace = '*', string|null $locale = null, string|bool|null $fallback = false)  Retrieve all translations for the specified `group`, `namespace`, and `locale`. If `fallback` is set, include translations from the fallback locale that are not overridden by the specified `locale`.
 * @method array getLocales() getLocales() Retrieve all existing locales for the **Flat** store.
 * @method array getStructure() getStructure() Get the **Structured** store structure. The structure returned is an associative array with namespaces as the top-level keys, locales as the second-level keys, and groups as the third-level values.
 * @method $this add() add(array $translations, string|null $locale = null) Add translations for the specified locale(s).
 * @method $this add() add(array $translations, string $group, string $namespace = '*', string|null $locale = null) Add translations for the specified group, namespace, and locale(s).
 * @method $this update() update(array $translations, string|null $locale = null) Update translations for the specified locale(s).
 * @method $this update() update(array $translations, string $group, string $namespace = '*', string|null $locale = null) Update translations for the specified group, namespace, and locale(s).
 * @method $this remove() remove(array $keys, string|null $locale = null) Remove specific translations for the specified locale(s)
 * @method $this remove() remove(array $items, string $group, string $namespace = '*', string|null $locale = null) Remove specific translations from the specified group, namespace, and locale(s).
 * @method $this flush() flush(string|null $locale = null) Remove all translations for the specified locale(s) or for all locales.
 * @method $this flush() flush(string $group = '*', string|null $namespace = null, string|null $locale = null) Remove all translations for the specified group, namespace(s), and locale(s).
 */
interface Store
{
    /**
     * Get a translation.
     * If `fallback` is set, fallback to the fallback locale if the translation for the specified `key` is not found in the given `locale`.
     * 
     * @param string $key The key of the translation to retrieve.
     * @param string|null $locale The locale of the translation. If `null`, the current locale is used.
     * @param string|bool|null $fallback Determines fallback behavior:
     *                                    - If a `string`, fallback to the specified locale.
     *                                    - If `true` or `null`, fallback to the fallback locale as defined by Laravel Translator.
     *                                    - If `false`, do not use any fallback.
     * @return array|string|null The translation for the specified `key` and `locale`, or `null` if not found.
     */
    public function get($key, $locale = null, $fallback = false);

    /**
     * Check if a translation exists.
     * 
     * @param string $key The key of the translation to check.
     * @param string|null $locale The locale of the translation. If `null`, the current locale is used.
     * @return bool `true` if the translation exists, `false` otherwise.
     */
    public function has($key, $locale = null) : bool;
}
