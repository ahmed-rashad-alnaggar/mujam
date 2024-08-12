<?php

namespace Alnaggar\Mujam\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Alnaggar\Mujam\TranslationManager addStore() addStore(string $name, \Alnaggar\Mujam\Contracts\Store $store) Add translation store.
 * @method static \Alnaggar\Mujam\Contracts\Store store() store(string|null $name = null) Retrieve a translation store instance by its name.
 * @method static string getDefaultStore() getDefaultStore() Get the default translation store name.
 * @method static \Alnaggar\Mujam\TranslationManager setDefaultStore() setDefaultStore(string $name) Set the default translation store.
 * @method static array<string, \Alnaggar\Mujam\Contracts\Store> getStores() getStores() Retrieve all resolved store instances.
 * @method static \Alnaggar\Mujam\TranslationManager setStores() setStores(array $stores) Set the store instances.
 * @method static \Alnaggar\Mujam\TranslationManager forgetStore() forgetStore(string $name) Forget the store associated with the given `name`.
 * @method static \Alnaggar\Mujam\TranslationManager forgetStores() forgetStores() Forget all of the resolved store instances.
 * @method static \Alnaggar\Mujam\TranslationManager extend() extend(string $driver, callable $callback) Register a custom driver creator Closure.
 * @method static \Illuminate\Contracts\Foundation\Application getApplication() getApplication() Get the application instance used by the manager.
 * @method static \Alnaggar\Mujam\TranslationManager setApplication() setApplication(\Illuminate\Contracts\Foundation\Application $app) Set the application instance to be used by the manager.
 * @method static array|string|null get() get(string $key, string|null $locale = null, string|bool|null $fallback = false) Get a translation. If `fallback` is set, fallback to the fallback locale if the translation for the specified `key` is not found in the given `locale`.
 * @method static array getAll() getAll(string|null $locale = null, string|bool|null $fallback = false) Retrieve all translations for the specified locale. If `fallback` is set, include translations from the fallback locale that are not overridden by the specified `locale`.
 * @method static array getAll() getAll(string $group, string $namespace = '*', string|null $locale = null, string|bool|null $fallback = false)  Retrieve all translations for the specified `group`, `namespace`, and `locale`. If `fallback` is set, include translations from the fallback locale that are not overridden by the specified `locale`.
 * @method static array getLocales() getLocales() Retrieve all existing locales for the **Flat** store.
 * @method static array getStructure() getStructure() Get the **Structured** store structure. The structure returned is an associative array with namespaces as the top-level keys, locales as the second-level keys, and groups as the third-level values.
 * @method static \Alnaggar\Mujam\Contracts\FlatStore add() add(array $translations, string|null $locale = null) Add translations for the specified locale(s).
 * @method static \Alnaggar\Mujam\Contracts\StructuredStore add() add(array $translations, string $group, string $namespace = '*', string|null $locale = null) Add translations for the specified group, namespace, and locale(s).
 * @method static \Alnaggar\Mujam\Contracts\FlatStore update() update(array $translations, string|null $locale = null) Update translations for the specified locale(s).
 * @method static \Alnaggar\Mujam\Contracts\StructuredStore update() update(array $translations, string $group, string $namespace = '*', string|null $locale = null) Update translations for the specified group, namespace, and locale(s).
 * @method static \Alnaggar\Mujam\Contracts\FlatStore remove() remove(array $keys, string|null $locale = null) Remove specific translations for the specified locale(s)
 * @method static \Alnaggar\Mujam\Contracts\StructuredStore remove() remove(array $items, string $group, string $namespace = '*', string|null $locale = null) Remove specific translations from the specified group, namespace, and locale(s).
 * @method static \Alnaggar\Mujam\Contracts\FlatStore flush() flush(string|null $locale = null) Remove all translations for the specified locale(s) or for all locales.
 * @method static \Alnaggar\Mujam\Contracts\StructuredStore flush() flush(string $group = '*', string|null $namespace = null, string|null $locale = null) Remove all translations for the specified group, namespace(s), and locale(s).
 * @method static bool has() has(string $key, string|null $locale = null) Check if a translation exists.
 * @method static void macro() macro(string $name, object|callable $macro) Register a custom macro.
 * @method static void mixin() mixin(object $mixin, bool $replace = true) Mix another object into the class.
 * @method static bool hasMacro() hasMacro(string $name) Checks if macro is registered.
 * @method static void flushMacros() flushMacros() Flush the existing macros.
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
