<?php

namespace Modules\Bimbingan\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'Modules\Bimbingan\Http\Controllers';

    public function boot()
    {
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => ['web', 'auth'],
            'namespace' => $this->namespace,
            'prefix' => 'bimbingan',
            'as' => 'bimbingan.',
        ], function ($router) {
            require module_path('Bimbingan', 'Routes/web.php');
        });
    }
}
