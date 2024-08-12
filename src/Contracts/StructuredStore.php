<?php

namespace Alnaggar\Mujam\Contracts;

interface StructuredStore extends Store
{
    /**
     * Retrieve all translations for the specified `group`, `namespace`, and `locale`.
     * If `fallback` is set, include translations from the fallback locale that are not overridden by the specified `locale`.
     * 
     * @param string $group The group to retrieve translations for.
     * @param string $namespace The namespace to retrieve translations for. Use `*` to indicate the default namespace.
     * @param string|null $locale The locale to retrieve translations for. If `null`, the current locale is used.
     * @param string|bool|null $fallback Determines the fallback behavior:
     *                                    - If a `string`, fallback to the specified locale.
     *                                    - If `true` or `null`, fallback to the fallback locale as defined by Laravel Translator.
     *                                    - If `false`, do not use any fallback.
     * @return array An array of translations for the specified `group`, `namespace`, and `locale`, including fallbacks if applicable.
     */
    public function getAll($group, $namespace = '*', $locale = null, $fallback = false) : array;

    /**
     * Get the store structure.
     * 
     * The structure returned is an associative array with namespaces as the top-level keys, locales as the second-level keys, and groups as the third-level values.
     * 
     * @return array The structured array of translations organized by namespaces, locales, and groups.
     */
    public function getStructure() : array;

    /**
     * Add translations for the specified group, namespace, and locale(s).
     *
     * @param array $translations The translations to add.
     * @param string $group The group to add translations to.
     * @param string $namespace The namespace to add translations to. Use `*` to indicate the default namespace.
     * @param string|null $locale The locale to add translations for.
     *                             - If `null`, translations are added to the current locale.
     *                             - If `*`, translations are added to all locales.
     *                             - Can also be multiple locales separated by `|`.
     * @return static
     */
    public function add(array $translations, $group, $namespace = '*', $locale = null);

    /**
     * Update translations for the specified group, namespace, and locale(s).
     *
     * @param array $translations The translations to update.
     * @param string $group The group to update translations for.
     * @param string $namespace The namespace to update translations for. Use `*` to indicate the default namespace.
     * @param string|null $locale The locale to update translations for.
     *                             - If `null`, translations are updated for the current locale.
     *                             - If `*`, translations are updated for all locales.
     *                             - Can also be multiple locales separated by `|`.
     * @return static
     */
    public function update(array $translations, $group, $namespace = '*', $locale = null);

    /**
     * Remove specific translations from the specified group, namespace, and locale(s).
     *
     * @param array $items The keys of the translations to remove.
     * @param string $group The group to remove translations from.
     * @param string $namespace The namespace to remove translations from. Use `*` to indicate the default namespace.
     * @param string|null $locale The locale to remove translations from.
     *                             - If `null`, items are removed from the current locale.
     *                             - If `*`, items are removed from all locales.
     *                             - Can also be multiple locales separated by `|`.
     * @return static
     */
    public function remove(array $items, $group, $namespace = '*', $locale = null);

    /**
     * Remove all translations for the specified group, namespace(s), and locale(s).
     * 
     * @param string $group The group to remove translations for. Use '*' to indicate all groups.
     * @param string|null $namespace The namespace to remove translations for.
     *                                - If `null`, all translations for all namespaces are removed.
     *                                - If `*`, all translations for the default namespace are removed.
     *                                - Can also be multiple namespaces separated by `|`.
     * @param string|null $locale The locale to remove translations for.
     *                             - If `null`, all translations for the current locale are removed.
     *                             - If `*`, all translations for all locales are removed.
     *                             - Can also be multiple locales separated by `|`.
     * @return static
     */
    public function flush($group = '*', $namespace = null, $locale = '*');
}
