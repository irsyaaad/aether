<?php

namespace Modules\Pelatihan\Providers;

use Illuminate\Support\ServiceProvider;

class PelatihanServiceProvider extends ServiceProvider
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
        $viewPath = resource_path('views/modules/pelatihan');
        $sourcePath = module_path('Pelatihan', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/pelatihan';
        }, \Config::get('view.paths')), [$sourcePath]), 'pelatihan');
    }
}
