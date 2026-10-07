<?php

namespace Modules\Master\Providers;

use Illuminate\Support\ServiceProvider;

class MasterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('Master', 'Database/Migrations'));
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    protected function registerConfig()
    {
        $this->publishes([
            module_path('Master', 'Config/config.php') => config_path('master.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Master', 'Config/config.php'), 'master'
        );
    }

    public function registerViews()
    {
        $viewPath = resource_path('views/modules/master');
        $sourcePath = module_path('Master', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/master';
        }, \Config::get('view.paths')), [$sourcePath]), 'master');
    }

    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/master');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'master');
        } else {
            $this->loadTranslationsFrom(module_path('Master', 'Resources/lang'), 'master');
        }
    }

    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            $this->loadFactoriesFrom(module_path('Master', 'Database/factories'));
        }
    }

    public function provides()
    {
        return [];
    }
}
