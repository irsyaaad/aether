<?php

namespace Modules\Olahdata\Providers;

use Illuminate\Support\ServiceProvider;

class OlahdataServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerViews();
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    public function registerViews()
    {
        $viewPath = resource_path('views/modules/olahdata');
        $sourcePath = module_path('Olahdata', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/olahdata';
        }, \Config::get('view.paths')), [$sourcePath]), 'olahdata');
    }
}
