<?php

namespace Alnaggar\Mujam\Contracts;

/**
 * @method array getAll() getAll(string|null $locale = null, string|bool|null $fallback = false) 
 * (**Flat Store Method**) Retrieve all translations for the specified `locale`, including fallbacks if applicable.
 * 
 * If `locale` is `null`, the current application locale is used. 
 * 
 * The `fallback` parameter controls fallback behavior:
 * - If a locale is provided, fallback to it.
 * - If `true` or `null`, fallback to application's fallback locale.
 * - If `false`, no fallback will be applied, using only translations from the specified `locale`.
 *
 * @method array getLocales() getLocales() 
 * (**Flat Store Method**) Retrieve an array of locales that have available translations.
 *
 * @method \Alnaggar\Mujam\Contracts\FlatStore add() add(array $translations, string|null $locale = null) 
 * (**Flat Store Method**) Add translations for the specified `locale(s)`. The translations are provided as an associative array,
 * where keys are the translation keys and values are the corresponding translation strings.
 * 
 * The `locale` parameter specifies the locale for which translations should be added:
 * - If `null`, translations are added to the current application locale.
 * - If `*`, translations are added to all available locales.
 * - If a specific locale is provided, translations are added to that locale.
 * - Multiple locales can also be specified, separated by `|`.
 * 
 * @method \Alnaggar\Mujam\Contracts\FlatStore update() update(array $translations, string|null $locale = null) 
 * (**Flat Store Method**) Update translations for the specified `locale(s)`. The translations are provided as an associative array,
 * where the keys represent the translation keys and the values are the updated translation strings.
 * 
 * The `locale` parameter specifies the locale for which translations should be updated:
 * - If `null`, translations are updated for the current application locale.
 * - If `*`, translations are updated for all available locales.
 * - If a specific locale is provided, translations are updated for that locale.
 * - Multiple locales can also be provided, separated by `|`.
 * 
 * @method \Alnaggar\Mujam\Contracts\FlatStore remove() remove(array $keys, string|null $locale = null) 
 * (**Flat Store Method**) Remove specific translations for the specified `locale(s)`. The translations are removed based on the provided array of keys,
 * where each key represents a specific translation that will be removed.
 * 
 * The `locale` parameter specifies the locale for which translations should be removed:
 * - If `null`, the translations are removed from the current application locale.
 * - If `*`, the translations are removed from all available locales.
 * - If a specific locale is provided, translations are removed from that locale.
 * - Multiple locales can also be provided, separated by `|`.
 * 
 * @method \Alnaggar\Mujam\Contracts\FlatStore flush() flush(string|null $locale = null) 
 * (**Flat Store Method**) Remove all translations for the specified `locale(s)` or for all locales.
 * 
 * The `locale` parameter specifies the locale for which translations should be removed:
 * - If `null`, all translations for the current application locale are removed.
 * - If `*`, all translations for all available locales are removed.
 * - If a specific locale is provided, all translations are removed for that locale.
 * - Multiple locales can also be provided, separated by `|`.
 * 
 * @method array getAll() getAll(string $group, string $namespace = '*', string|null $locale = null, string|bool|null $fallback = false)  
 * (**Structured Store Method**) Retrieve all translations for the specified `group`, `namespace`, and `locale`, including fallbacks if applicable.
 * 
 * The `namespace` can be set to `*` to indicate the default namespace.
 * 
 * If `locale` is `null`, the current application locale is used. 
 * 
 * The `fallback` parameter controls fallback behavior:
 * - If a locale is provided, fallback to it.
 * - If `true` or `null`, fallback to application's fallback locale.
 * - If `false`, no fallback will be applied, using only translations from the specified `locale`.
 * 
 * @method array getStructure() getStructure() 
 * (**Structured Store Method**) Retrieve the store structure as an associative array with namespaces as the top-level keys, locales as the second-level keys, and groups as the third-level values.
 * 
 * @method \Alnaggar\Mujam\Contracts\StructuredStore add() add(array $translations, string $group, string $namespace = '*', string|null $locale = null) 
 * (**Structured Store Method**) Add translations for the specified `group`, `namespace`, and `locale(s)`. The translations are provided as an associative array,
 * where keys are the translation keys and values are the corresponding translation strings.
 * 
 * The `namespace` can be set to `*` to indicate the default namespace.

 * The `locale` parameter specifies the locale for which translations should be added:
 * - If `null`, translations are added to the current application locale.
 * - If `*`, translations are added to all available locales.
 * - If a specific locale is provided, translations are added to that locale.
 * - Multiple locales can also be specified, separated by `|`.
 * 
 * @method \Alnaggar\Mujam\Contracts\StructuredStore update() update(array $translations, string $group, string $namespace = '*', string|null $locale = null) 
 * (**Structured Store Method**) Update translations for the specified `group`, `namespace`, and `locale(s)`. The translations are provided as an associative array,
 * where the keys represent the translation keys and the values are the updated translation strings.
 * 
 * The `namespace` can be set to `*` to indicate the default namespace.

 * The `locale` parameter specifies the locale for which translations should be updated:
 * - If `null`, translations are updated for the current application locale.
 * - If `*`, translations are updated for all available locales.
 * - If a specific locale is provided, translations are updated for that locale.
 * - Multiple locales can also be provided, separated by `|`.
 * 
 * @method \Alnaggar\Mujam\Contracts\StructuredStore remove() remove(array $items, string $group, string $namespace = '*', string|null $locale = null) 
 * (**Structured Store Method**) Remove specific translations for the specified `group`, `namespace`, and `locale(s)`. The translations are removed based on the provided array of keys,
 * where each key represents a specific translation that will be removed.
 * 
 * The `namespace` can be set to `*` to indicate the default namespace.
 * 
 * The `locale` parameter specifies the locale for which translations should be removed:
 * - If `null`, the translations are removed from the current application locale.
 * - If `*`, the translations are removed from all available locales.
 * - If a specific locale is provided, translations are removed from that locale.
 * - Multiple locales can also be provided, separated by `|`.
 * 
 * @method \Alnaggar\Mujam\Contracts\StructuredStore flush() flush(string $group = '*', string|null $namespace = null, string|null $locale = null) 
 * (**Structured Store Method**) Remove all translations for the specified `group(s)`, `namespace(s)`, and `locale(s)`.
 * 
 * The `group` can be set to `*` to indicate all groups.
 * 
 * The `namespace` parameter specifies the namespace for which translations should be removed:
 * - If `null`, all translations for all namespaces are removed.
 * - If `*`, all translations for the default namespace are removed.
 * - If a specific namespace is provided, all translations are removed for that namespace.
 * - Multiple namespaces can also be provided, separated by `|`.
 * 
 * The `locale` parameter specifies the locale for which translations should be removed:
 * - If `null`, the translations are removed from the current application locale.
 * - If `*`, the translations are removed from all available locales.
 * - If a specific locale is provided, translations are removed from that locale.
 * - Multiple locales can also be provided, separated by `|`.
 */
interface Store
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
     * @return string|null The translation for the specified `key` and `locale`, or `null` if not found.
     */
    public function get($key, $locale = null, $fallback = false);

    /**
     * Check if a translation exists.
     * 
     * @param string $key The key of the translation to check.
     * @param string|null $locale The locale of the translation. If `null`, the current application locale is used.
     * @return bool `true` if the translation exists, `false` otherwise.
     */
    public function has($key, $locale = null): bool;
}
