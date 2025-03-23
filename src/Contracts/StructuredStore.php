<?php

namespace Alnaggar\Mujam\Contracts;

interface StructuredStore extends Store
{
    /**
     * Retrieve the translation for the specified `key` and `locale`.
     * 
     * @param string $key The key of the translation to retrieve.
     * @param string|null $locale The locale of the translation. If `null`, the current application locale is used.
     * @param string|bool|null $fallback Controls fallback behavior:
     *                                    - If a locale is provided, fallback to it.
     *                                    - If `true` or `null`, fallback to application's fallback locale.
     *                                    - If `false`, no fallback will be applied (translations from 
     *                                      the specified `locale` only).
     * @return array|string|null The translation for the specified `key` and `locale`, or `null` if not found.
     */
    public function get($key, $locale = null, $fallback = false);

    /**
     * Retrieve all translations for the specified `group`, `namespace`, and `locale`.
     * 
     * @param string $group The group to retrieve translations for.
     * @param string $namespace The namespace to retrieve translations for. Use `*` to indicate the default namespace.
     * @param string|null $locale The locale to retrieve translations for. If `null`, the current application locale is used.
     * @param string|bool|null $fallback Controls fallback behavior:
     *                                    - If a locale is provided, fallback to it.
     *                                    - If `true` or `null`, fallback to application's fallback locale.
     *                                    - If `false`, no fallback will be applied (translations from 
     *                                      the specified `locale` only).
     * @return array An array of translations for the specified `group`, `namespace`, and `locale`, including fallbacks if applicable.
     */
    public function getAll($group, $namespace = '*', $locale = null, $fallback = false) : array;

    /**
     * Get the store structure as an associative array with namespaces as the top-level keys, locales as the second-level keys, and groups as the third-level values.
     * 
     * @return array The structured array of translations organized by namespaces, locales, and groups.
     */
    public function getStructure() : array;

    /**
     * Add translations for the specified `group`, `namespace`, and `locale(s)`.
     *
     * @param array $translations An associative array of translations to add, where keys 
     *                            are the translation keys and values are the corresponding translation strings.
     * @param string $group The group to add translations to.
     * @param string $namespace The namespace to add translations to. Use `*` to indicate the default namespace.
     * @param string|null $locale The locale(s) to which the translations should be added:
     *                             - If `null`, translations are added to the current application locale.
     *                             - If `*`, translations are added to all available locales.
     *                             - If a specific locale is provided, translations are added to that locale.
     *                             - Multiple locales can also be provided, separated by `|`.
     * @return static Returns the current instance for method chaining.
     */
    public function add(array $translations, $group, $namespace = '*', $locale = null);

    /**
     * Update translations for the specified `group`, `namespace`, and `locale(s)`.
     *
     * @param array $translations An associative array of translations to update, where keys 
     *                            are the translation keys and values are the updated translation strings.
     * @param string $group The group to update translations for.
     * @param string $namespace The namespace to update translations for. Use `*` to indicate the default namespace.
     * @param string|null $locale The locale(s) for which the translations should be updated:
     *                             - If `null`, translations are updated for the current application locale.
     *                             - If `*`, translations are updated for all available locales.
     *                             - If a specific locale is provided, translations are updated for that locale.
     *                             - Multiple locales can also be provided, separated by `|`.
     * @return static Returns the current instance for method chaining.
     */
    public function update(array $translations, $group, $namespace = '*', $locale = null);

    /**
     * Remove specific translations from the specified `group`, `namespace`, and `locale(s)`.
     *
     * @param array $items An array of translation keys to remove. Each key represents 
     *                    a specific translation that will be removed.
     * @param string $group The group to remove translations from.
     * @param string $namespace The namespace to remove translations from. Use `*` to indicate the default namespace.
     * @param string|null $locale The locale(s) from which to remove the translations:
     *                             - If `null`, the translations are removed from the current application locale.
     *                             - If `*`, the translations are removed from all available locales.
     *                             - If a specific locale is provided, translations are removed from that locale.
     *                             - Multiple locales can also be provided, separated by `|`.
     * @return static Returns the current instance for method chaining.
     */
    public function remove(array $items, $group, $namespace = '*', $locale = null);

    /**
     * Remove all translations for the specified `group(s)`, `namespace(s)`, and `locale(s)`.
     * 
     * @param string $group The group to remove translations for. Use `*` to indicate all groups.
     * @param string|null $namespace The namespace to remove translations for.
     *                                - If `null`, all translations for all namespaces are removed.
     *                                - If `*`, all translations for the default namespace are removed.
     *                                - If a specific namespace is provided, all translations are removed for that namespace.
     *                                - Multiple namespaces can also be provided, separated by `|`.
     * @param string|null $locale The locale(s) for which translations should be removed:
     *                             - If `null`, all translations for the current application locale are removed.
     *                             - If `*`, all translations for all available locales are removed.
     *                             - If a specific locale is provided, all translations are removed for that locale.
     *                             - Multiple locales can also be provided, separated by `|`.
     * @return static Returns the current instance for method chaining.
     */
    public function flush($group = '*', $namespace = null, $locale = '*');
}
