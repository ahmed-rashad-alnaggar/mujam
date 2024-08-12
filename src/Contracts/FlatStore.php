<?php

namespace Alnaggar\Mujam\Contracts;

interface FlatStore extends Store
{
    /**
     * Retrieve all translations for the specified `locale`.
     * If `fallback` is set, include translations from the fallback locale that are not overridden by the specified `locale`.
     * 
     * @param string|null $locale The locale to retrieve translations for. If `null`, the current locale is used.
     * @param string|bool|null $fallback Determines the fallback behavior:
     *                                    - If a `string`, fallback to the specified locale.
     *                                    - If `true` or `null`, fallback to the fallback locale as defined by Laravel Translator.
     *                                    - If `false`, do not use any fallback.
     * @return array An array of translations for the specified `locale`, including fallbacks if applicable.
     */
    public function getAll($locale = null, $fallback = false) : array;

    /**
     * Retrieve all existing locales.
     * 
     * @return array An array of all available locales.
     */
    public function getLocales() : array;

    /**
     * Add translations for the specified locale(s).
     *
     * @param array $translations The translations to add.
     * @param string|null $locale The locale to which the translations should be added.
     *                             - If `null`, translations are added to the current locale.
     *                             - If `*`, translations are added to all locales.
     *                             - Can also be multiple locales separated by `|`.
     * @return static
     */
    public function add(array $translations, $locale = null);

    /**
     * Update translations for the specified locale(s).
     *
     * @param array $translations The translations to update.
     * @param string|null $locale The locale to which the translations should be updated.
     *                             - If `null`, translations are updated for the current locale.
     *                             - If `*`, translations are updated for all locales.
     *                             - Can also be multiple locales separated by `|`.
     * @return static
     */
    public function update(array $translations, $locale = null);

    /**
     * Remove specific translations for the specified locale(s).
     *
     * @param array $keys The keys of the translations to remove.
     * @param string|null $locale The locale from which to remove the translations.
     *                             - If `null`, keys are removed from the current locale.
     *                             - If `*`, keys are removed from all locales.
     *                             - Can also be multiple locales separated by `|`.
     * @return static
     */
    public function remove(array $keys, $locale = null);

    /**
     * Remove all translations for the specified locale(s) or for all locales.
     * 
     * @param string|null $locale The locale for which translations should be removed.
     *                             - If `null`, all translations for the current locale are removed.
     *                             - If `*`, all translations for all locales are removed.
     *                             - Can also be multiple locales separated by `|`.
     * @return static
     */
    public function flush($locale = '*');
}
