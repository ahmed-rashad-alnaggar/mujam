<?php

namespace Alnaggar\Mujam;

use Illuminate\Support\ServiceProvider;

class MujamServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * 
     * @return void
     */
    public function register(): void
    {
        $this->app->singleton('mujam', TranslationManager::class);

        $this->mergeConfigFrom(__DIR__.'/../config/mujam.php', 'mujam');
    }

    /**
     * Bootstrap any application services.
     * 
     * @return void
     */
    public function boot(): void
    {
        $this->registerPublishes();
    }

    /**
     * Register publishable resources.
     * 
     * @return void
     */
    public function registerPublishes(): void
    {
        $this->publishes([
            __DIR__.'/../config/mujam.php' => config_path('mujam.php')
        ], 'mujam-config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'mujam-migrations');
    }
}
