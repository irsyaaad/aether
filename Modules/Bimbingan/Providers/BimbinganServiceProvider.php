<?php

namespace Modules\Bimbingan\Providers;

use Illuminate\Support\ServiceProvider;

class BimbinganServiceProvider extends ServiceProvider
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
        $viewPath = resource_path('views/modules/bimbingan');
        $sourcePath = module_path('Bimbingan', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/bimbingan';
        }, \Config::get('view.paths')), [$sourcePath]), 'bimbingan');
    }
}
