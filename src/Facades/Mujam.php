<?php

namespace Alnaggar\Mujam\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Alnaggar\Mujam\Contracts\Store store() store(string|null $name = null) 
 * Retrieve a translation store instance by its name. If no name is provided, returns the default store.
 *
 * @method static string getDefaultStore() getDefaultStore() 
 * Get the name of the default translation store.
 *
 * @method static \Alnaggar\Mujam\TranslationManager setDefaultStore() setDefaultStore(string $name) 
 * Set the default translation store.
 *
 * @method static array<string, \Alnaggar\Mujam\Contracts\Store> getStores() getStores() 
 * Retrieve all resolved store instances.
 *
 * @method static \Alnaggar\Mujam\TranslationManager forgetStore() forgetStore(string $name) 
 * Forget the store associated with the given `name`.
 *
 * @method static \Alnaggar\Mujam\TranslationManager forgetStores() forgetStores() 
 * Forget all resolved store instances.
 *
 * @method static \Alnaggar\Mujam\TranslationManager extend() extend(string $driver, callable $resolver) 
 * Register a custom driver resolver.
 *
 * @method static \Illuminate\Contracts\Foundation\Application getApplication() getApplication() 
 * Get the application instance used by the translation manager.
 *
 * @method static \Alnaggar\Mujam\TranslationManager setApplication() setApplication(\Illuminate\Contracts\Foundation\Application $app) 
 * Set the application instance to be used by the translation manager.
 *
 * @method static array|string|null get() get(string $key, string|null $locale = null, string|bool|null $fallback = null) 
 * Retrieve the translation for the specified `key` and `locale`, or `null` if not found.
 * 
 * If `locale` is `null`, the current application locale is used.
 * 
 * The `fallback` parameter controls fallback behavior:
 * - If a locale is provided, fallback to it.
 * - If `true` or `null`, fallback to application's fallback locale.
 * - If `false`, no fallback will be applied, using only translations from the specified `locale`.
 * 
 * @method static bool has() has(string $key, string|null $locale = null) 
 * Check if a translation exists for the specified `key` and `locale`.
 * 
 * If `locale` is `null`, the current application locale is used.
 * 
 * @method static array getAll() getAll(string|null $locale = null, string|bool|null $fallback = null) 
 * (**Flat Store Method**) Retrieve all translations for the specified `locale`, including fallbacks if applicable.
 * 
 * If `locale` is `null`, the current application locale is used.
 * 
 * The `fallback` parameter controls fallback behavior:
 * - If a locale is provided, fallback to it.
 * - If `true` or `null`, fallback to application's fallback locale.
 * - If `false`, no fallback will be applied, using only translations from the specified `locale`.
 *
 * @method static array getLocales() getLocales() 
 * (**Flat Store Method**) Retrieve an array of locales that have available translations.
 *
 * @method static \Alnaggar\Mujam\Contracts\FlatStore add() add(array $translations, string|null $locale = null) 
 * (**Flat Store Method**) Add translations for the specified `locale(s)`. The translations are provided as an associative array,
 * where keys are the translation keys and values are the corresponding translation strings.
 * 
 * The `locale` parameter specifies the locale for which translations should be added:
 * - If `null`, translations are added to the current application locale.
 * - If `*`, translations are added to all available locales.
 * - If a specific locale is provided, translations are added to that locale.
 * - Multiple locales can also be specified, separated by `|`.
 * 
 * @method static \Alnaggar\Mujam\Contracts\FlatStore update() update(array $translations, string|null $locale = null) 
 * (**Flat Store Method**) Update translations for the specified `locale(s)`. The translations are provided as an associative array,
 * where the keys represent the translation keys and the values are the updated translation strings.
 * 
 * The `locale` parameter specifies the locale for which translations should be updated:
 * - If `null`, translations are updated for the current application locale.
 * - If `*`, translations are updated for all available locales.
 * - If a specific locale is provided, translations are updated for that locale.
 * - Multiple locales can also be provided, separated by `|`.
 * 
 * @method static \Alnaggar\Mujam\Contracts\FlatStore remove() remove(array $keys, string|null $locale = null) 
 * (**Flat Store Method**) Remove specific translations for the specified `locale(s)`. The translations are removed based on the provided array of keys,
 * where each key represents a specific translation that will be removed.
 * 
 * The `locale` parameter specifies the locale for which translations should be removed:
 * - If `null`, the translations are removed from the current application locale.
 * - If `*`, the translations are removed from all available locales.
 * - If a specific locale is provided, translations are removed from that locale.
 * - Multiple locales can also be provided, separated by `|`.
 * 
 * @method static \Alnaggar\Mujam\Contracts\FlatStore flush() flush(string|null $locale = null) 
 * (**Flat Store Method**) Remove all translations for the specified `locale(s)` or for all locales.
 * 
 * The `locale` parameter specifies the locale for which translations should be removed:
 * - If `null`, all translations for the current application locale are removed.
 * - If `*`, all translations for all available locales are removed.
 * - If a specific locale is provided, all translations are removed for that locale.
 * - Multiple locales can also be provided, separated by `|`.
 * 
 * @method static array getAll() getAll(string $group, string $namespace = '*', string|null $locale = null, string|bool|null $fallback = null)  
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
 * @method static array getStructure() getStructure() 
 * (**Structured Store Method**) Retrieve the store structure as an associative array with namespaces as the top-level keys, locales as the second-level keys, and groups as the third-level values.
 * 
 * @method static \Alnaggar\Mujam\Contracts\StructuredStore add() add(array $translations, string $group, string $namespace = '*', string|null $locale = null) 
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
 * @method static \Alnaggar\Mujam\Contracts\StructuredStore update() update(array $translations, string $group, string $namespace = '*', string|null $locale = null) 
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
 * @method static \Alnaggar\Mujam\Contracts\StructuredStore remove() remove(array $items, string $group, string $namespace = '*', string|null $locale = null) 
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
 * @method static \Alnaggar\Mujam\Contracts\StructuredStore flush() flush(string $group = '*', string|null $namespace = null, string|null $locale = null) 
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
 *
 * @method static void macro() macro(string $name, object|callable $macro) 
 * Register a custom macro to extend the functionality of the class.
 *
 * @method static void mixin() mixin(object $mixin, bool $replace = true) 
 * Mix another object into the class. Optionally, replace existing methods.
 *
 * @method static bool hasMacro() hasMacro(string $name) 
 * Check if a macro with the given `name` has been registered.
 *
 * @method static void flushMacros() flushMacros() 
 * Flush all registered macros, removing them from the class.
 * 
 * @see \Alnaggar\Mujam\Contracts\Store
 * @see \Alnaggar\Mujam\TranslationManager
 */
class Mujam extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'mujam';
    }
}
