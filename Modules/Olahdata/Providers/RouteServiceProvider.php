<?php

namespace Modules\Olahdata\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'Modules\Olahdata\Http\Controllers';

    public function boot()
    {
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => ['web', 'auth'],
            'namespace' => $this->namespace,
            'prefix' => 'olahdata',
            'as' => 'olahdata.',
        ], function ($router) {
            require module_path('Olahdata', 'Routes/web.php');
        });
    }
}
