<?php

namespace Modules\Keuangan\Providers;

use Illuminate\Support\ServiceProvider;

class KeuanganServiceProvider extends ServiceProvider
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
        $viewPath = resource_path('views/modules/keuangan');
        $sourcePath = module_path('Keuangan', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/keuangan';
        }, config('view.paths')), [$sourcePath]), 'keuangan');
    }

    public function provides()
    {
        return [];
    }
}
