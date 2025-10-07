<?php

namespace Alnaggar\Mujam\Concerns\Eloquent;

use Alnaggar\Mujam\Contracts\StructuredStore;
use Alnaggar\Mujam\Facades\Mujam;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasTranslations
{
    /**
     * Model structured translations store.
     * 
     * @var string
     */
    protected $translationStore;

    /**
     * A dot notation array of the translatable properties.
     * 
     * @var array<string>
     */
    protected $translatables;

    /**
     * Cached translations to avoid fetching them from the store on every retrieval call.
     * 
     * @var array<string, array<string, string>>
     */
    protected $translations = [];

    /**
     * Cached translations to update when saving the model.
     * 
     * @var array<string, array<string, string>>
     */
    protected $translationsToUpdate = [];

    /**
     * Cached translations to delete when saving the model.
     * 
     * @var array<string, array<string>>
     */
    protected $translationsToDelete = [];

    /**
     * Boot the HasTranslations trait. 
     * 
     * @return void
     */
    public static function bootHasTranslations(): void
    {
        // Defer saving/deleting translations until the model is saved.
        static::saved(static function (/** @var \Alnaggar\Mujam\Concerns\Eloquent\HasTranslations $model */ $model): void {
            $model->handleTranslationsToUpdate();
            $model->handleTranslationsToDelete();
        });

        // Flush all related translation when the model is deleted.
        static::deleted(static function (/** @var \Alnaggar\Mujam\Concerns\Eloquent\HasTranslations $model */ $model): void {
            $model->getTranslationStore()
                ->flush($model->getKey(), static::class, '*');
        });
    }

    /**
     * Initalize The HasTranslations trait.
     * 
     * @return void
     */
    public function initializeHasTranslations(): void
    {
        if (! isset($this->translationStore)) {
            $this->translationStore = config('mujam.model_translations_store');
        }
    }

    /**
     * Updates cached to update/add translations in/to the store.
     * 
     * @return void
     */
    protected function handleTranslationsToUpdate(): void
    {
        foreach ($this->translationsToUpdate as $locale => $translations) {
            $this->getTranslationStore()
                ->update($translations, $this->getKey(), static::class, $locale);
        }

        $this->translations = array_replace_recursive($this->translationsToUpdate, $this->translations);

        // Clear the cache.
        $this->translationsToUpdate = [];
    }

    /**
     * Removes cached to remove translations from the store.
     * 
     * @return void
     */
    protected function handleTranslationsToDelete(): void
    {
        foreach ($this->translationsToDelete as $locale => $keys) {
            $this->getTranslationStore()
                ->remove($keys, $this->getKey(), static::class, $locale);

            foreach ($keys as $key) {
                unset($this->translations[$locale][$key]);
            }
        }

        // Clear the cache.
        $this->translationsToDelete = [];
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributeValue($key)
    {
        if ($key !== $this->getKeyName()) {
            if ($this->isTranslatableAttribute($key)) {
                return $this->getTranslatableAttributeValue($key, null, null);
            }

            if ($this->isAttributeNestingTranslatableAttribute($key)) {
                return $this->getAttributeNestingTranslatableAttributeValue($key, null, null);
            }
        }

        return parent::getAttributeValue($key);
    }

    /**
     * Retrieve the value of a translatable attribute.
     * 
     * @param string $key
     * @param string|null $locale
     * @param string|bool|null $fallback
     * @return mixed
     */
    public function getTranslation(string $key, ?string $locale = null, $fallback = null)
    {
        return $this->getTranslatableAttributeValue($key, $locale, $fallback);
    }

    /**
     * Retrieves the translation of a listed translatable attribute.
     * 
     * @param string $key
     * @param string|null $locale
     * @param string|bool|null $fallback
     * @return string|null
     */
    protected function getTranslatableAttributeValue(string $key, ?string $locale, $fallback): ?string
    {
        $locale = $locale ?? app()->currentLocale();

        if (is_null($this->translations[$locale][$key] ?? null)) {
            $this->translations[$locale] = $this->getTranslations($locale);
        }

        $translation = null;

        if (! in_array($key, $this->translationsToDelete[$locale] ?? [])) {
            $translation = $this->translationsToUpdate[$locale][$key]
                ?? $this->translations[$locale][$key]
                ?? null;
        }

        // Handle the falling back here as we can not use the store `getAll()` function
        // due to it may be a to update/add translation.
        if (is_null($translation)) {
            if ($fallback !== false) {
                $fallback = is_string($fallback) ? $fallback : app()->getFallbackLocale();

                if ($locale !== $fallback) {
                    $translation = $this->getTranslatableAttributeValue($key, $fallback, false);
                }
            }
        }

        return $translation;
    }

    /**
     * Retrieves the root attribute with all nested translatable values injected.
     * 
     * @param string $key
     * @param string|null $locale
     * @param string|bool|null $fallback
     * @return mixed
     */
    protected function getAttributeNestingTranslatableAttributeValue(string $key, ?string $locale, $fallback)
    {
        $attribute = parent::getAttributeValue($key);

        collect($this->translatables())
            ->filter(function (string $translatableKey) use ($key): bool {
                return Str::startsWith($translatableKey, $key.'.');
            })
            ->each(function (string $translatableKey) use (&$attribute, $locale, $fallback): void {
                $translation = $this->getTranslatableAttributeValue($translatableKey, $locale, $fallback);
                $nestedKey = Str::after($translatableKey, '.');

                data_set($attribute, $nestedKey, $translation);
            });

        return $attribute;
    }

    /**
     * {@inheritDoc}
     */
    public function attributesToArray()
    {
        $attributes = parent::attributesToArray();

        foreach ($this->translatables() as $key) {
            data_set($attributes, $key, $this->getTranslatableAttributeValue($key, null, null));
        }

        return $attributes;
    }

    /**
     * {@inheritDoc}
     */
    public function setAttribute($key, $value)
    {
        $normalizedKey = str_replace('->', '.', $key);

        if ($this->isTranslatableAttribute($normalizedKey)) {
            return $this->setTranslatableAttributeValue($key, $value, null);
        }

        if ($this->isAttributeNestingTranslatableAttribute($normalizedKey)) {
            return $this->setAttributeNestingTranslatableAttributeValue($key, $value, null);
        }

        return parent::setAttribute($key, $value);
    }

    /**
     * Set/Add a translation(s) for a translatable attribute.
     * 
     * @param string $key
     * @param mixed $value
     * @param string|null $locale
     * @return static
     */
    public function setTranslation(string $key, $value, ?string $locale = null)
    {
        return $this->setTranslatableAttributeValue($key, $value, $locale);
    }

    /**
     * Sets/Adds the translation(s) for a listed translatable attribute.
     * 
     * @param string $key
     * @param array<string, string>|string $value
     * @param string|null $locale
     * @return static
     */
    protected function setTranslatableAttributeValue(string $key, $value, ?string $locale)
    {
        $locale = $locale ?? app()->currentLocale();

        if (! is_array($value)) {
            $value = [$locale => $value];
        }

        foreach ($value as $translationLocale => $translation) {
            if (! is_null($translation)) {
                $this->translationsToUpdate[$translationLocale][$key] = $translation;

                if (isset($this->translatables)) {
                    if (! in_array($key, $this->translatables)) {
                        $this->translatables[] = $key;
                    }
                }

                if (array_key_exists($locale, $this->translationsToDelete)) {
                    $translationKeyIndex = array_search($key, $this->translationsToDelete[$locale]);

                    if ($translationKeyIndex !== false) {
                        unset($this->translationsToDelete[$locale][$translationKeyIndex]);
                    }
                }
            } else {
                $this->removeTranslation($key, $translationLocale);
            }
        }

        // Intentionally skipping parent::setAttribute to avoid modifying the original attribute value,
        // which should remain null or an empty string since the actual content is handled via translations.

        return $this;
    }

    /**
     * Sets an attribute while handling its nested translatable attributes.
     * 
     * @param string $key
     * @param mixed $value
     * @param string|null $locale
     * @return static
     */
    protected function setAttributeNestingTranslatableAttributeValue(string $key, $value, ?string $locale)
    {
        collect($this->translatables())
            ->filter(function (string $translatableKey) use ($key): bool {
                return Str::startsWith($translatableKey, $key.'.');
            })
            ->each(function (string $translatableKey) use (&$value, $locale): void {
                $nestedKey = Str::after($translatableKey, '.');
                $translation = data_get($value, $nestedKey);
                $this->setTranslatableAttributeValue($translatableKey, $translation, $locale);

                // Setting the nested translatable attribute to null as it should be presented in database.
                data_set($value, $nestedKey, null);
            });

        return parent::setAttribute($key, $value);
    }

    /**
     * Remove an (?nested) translatable attribute translation.
     * 
     * @param string $key
     * @param string|null $locale
     */
    public function removeTranslation(string $key, ?string $locale = null)
    {
        $locale = $locale ?? app()->currentLocale();

        $this->translationsToDelete[$locale][] = $key;

        unset($this->translationsToUpdate[$locale][$key]);

        return $this;
    }

    /**
     * Determine if the given **listed translatable attribute** has a translation for the specified locale.
     * 
     * @param string $key
     * @param string|null $locale
     * @return bool
     */
    public function hasTranslation(string $key, ?string $locale = null): bool
    {
        return ! is_null($this->getTranslatableAttributeValue($key, $locale, false));
    }

    /**
     * Check if the attribute is translatable.
     * 
     * @param string $key
     * @return bool
     */
    public function isTranslatableAttribute(string $key): bool
    {
        return in_array($key, $this->translatables());
    }

    /**
     * Checks if the attribute contains any translatable sub-attributes.

     * @param string $key
     * @return bool
     */
    protected function isAttributeNestingTranslatableAttribute(string $key): bool
    {
        foreach ($this->translatables() as $translatable) {
            if (Str::startsWith($translatable, $key.'.')) {
                return true;
            }
        }

        return false;
    }

    /**
     * A dot notation array of the translatable properties.
     * 
     * @return array
     */
    protected function translatables(): array
    {
        if (isset($this->translatables)) {
            return $this->translatables;
        }

        return $this->translatables = array_unique(array_merge(
            array_keys($this->getTranslations('*')),
            Arr::collapse($this->translationsToUpdate)
        ));
    }

    /**
     * Helper method that retrieves translations for the given locale,
     * gracefully handling the case where the model does not exist in the database yet.
     *
     * @param string $locale
     * @return array
     */
    protected function getTranslations(string $locale): array
    {
        if ($this->exists) {
            return Arr::dot(
                $this->getTranslationStore()
                    ->getAll($this->getKey(), static::class, $locale, false)
            );
        }

        return [];
    }

    /**
     * Gets translations **structured** store.
     * 
     * @return \Alnaggar\Mujam\Contracts\StructuredStore
     */
    protected function getTranslationStore(): StructuredStore
    {
        return Mujam::store($this->translationStore);
    }
}
