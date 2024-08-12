<?php

namespace Alnaggar\Mujam;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Illuminate\Translation\Translator as LaravelTranslator;

class TranslationServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() : void
    {
        $this->registerLoader();

        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            // When registering the translator component, we'll need to set the default
            // locale as well as the fallback locale. So, we'll grab the application
            // configuration so we can easily get both of these values from there.
            $locale = $app->getLocale();
            $fallback = $app->getFallbackLocale();

            $translator = new LaravelTranslator($loader, $locale);
            $translator->setFallback($fallback);

            return $translator;
        });
    }

    /**
     * Register the translations loader.
     *
     * @return void
     */
    protected function registerLoader() : void
    {
        $this->app->singleton('translation.loader', function ($app) {
            return new TranslationLoader($app['mujam']);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() : array
    {
        return ['translator', 'translation.loader'];
    }
}
