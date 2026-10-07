<?php

namespace Modules\Sdm\Providers;

use Illuminate\Support\ServiceProvider;
class SdmServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('Sdm', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('Sdm', 'Config/config.php') => config_path('sdm.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Sdm', 'Config/config.php'), 'sdm'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/sdm');

        $sourcePath = module_path('Sdm', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/sdm';
        }, \Config::get('view.paths')), [$sourcePath]), 'sdm');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/sdm');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'sdm');
        } else {
            $this->loadTranslationsFrom(module_path('Sdm', 'Resources/lang'), 'sdm');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */

    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            // Menggunakan loadFactoriesFrom untuk factory berbasis class di Laravel 8
            $this->loadFactoriesFrom(module_path('Sdm', 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
