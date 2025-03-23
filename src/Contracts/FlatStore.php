<?php

namespace Alnaggar\Mujam\Contracts;

interface FlatStore extends Store
{
    /**
     * Retrieve all translations for the specified `locale`.
     * 
     * @param string|null $locale The locale for which to retrieve translations.
     *                            If `null`, the current application locale is used.
     * @param string|bool|null $fallback Controls fallback behavior:
     *                                    - If a locale is provided, fallback to it.
     *                                    - If `true` or `null`, fallback to application's fallback locale.
     *                                    - If `false`, no fallback will be applied (translations from 
     *                                      the specified `locale` only).
     * @return array An array of translations for the specified `locale`, including fallbacks if applicable.
     */
    public function getAll($locale = null, $fallback = false): array;

    /**
     * Retrieve all existing locales.
     * 
     * @return array An array of all available locales.
     */
    public function getLocales(): array;

    /**
     * Add translations for the specified `locale(s)`.
     *
     * @param array $translations An associative array of translations to add, where keys 
     *                            are the translation keys and values are the corresponding translation strings.
     * @param string|null $locale The locale(s) to which the translations should be added:
     *                             - If `null`, translations are added to the current application locale.
     *                             - If `*`, translations are added to all available locales.
     *                             - If a specific locale is provided, translations are added to that locale.
     *                             - Multiple locales can also be provided, separated by `|`.
     * @return static Returns the current instance for method chaining.
     */
    public function add(array $translations, $locale = null);

    /**
     * Update translations for the specified `locale(s)`.
     *
     * @param array $translations An associative array of translations to update, where keys 
     *                            are the translation keys and values are the updated translation strings.
     * @param string|null $locale The locale(s) for which the translations should be updated:
     *                             - If `null`, translations are updated for the current application locale.
     *                             - If `*`, translations are updated for all available locales.
     *                             - If a specific locale is provided, translations are updated for that locale.
     *                             - Multiple locales can also be provided, separated by `|`.
     * @return static Returns the current instance for method chaining.
     */
    public function update(array $translations, $locale = null);

    /**
     * Remove specific translations for the specified `locale(s)`.
     *
     * @param array $keys An array of translation keys to remove. Each key represents 
     *                    a specific translation that will be removed.
     * @param string|null $locale The locale(s) from which to remove the translations:
     *                             - If `null`, the translations are removed from the current application locale.
     *                             - If `*`, the translations are removed from all available locales.
     *                             - If a specific locale is provided, translations are removed from that locale.
     *                             - Multiple locales can also be provided, separated by `|`.
     * @return static Returns the current instance for method chaining.
     */
    public function remove(array $keys, $locale = null);

    /**
     * Remove all translations for the specified `locale(s)` or for all locales.
     * 
     * @param string|null $locale The locale(s) for which translations should be removed:
     *                             - If `null`, all translations for the current application locale are removed.
     *                             - If `*`, all translations for all available locales are removed.
     *                             - If a specific locale is provided, all translations are removed for that locale.
     *                             - Multiple locales can also be provided, separated by `|`.
     * @return static Returns the current instance for method chaining.
     */
    public function flush($locale = '*');
}
