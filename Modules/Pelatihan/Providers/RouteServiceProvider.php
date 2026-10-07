<?php

namespace Modules\Pelatihan\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'Modules\Pelatihan\Http\Controllers';

    public function boot()
    {
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => ['web', 'auth'],
            'namespace' => $this->namespace,
            'prefix' => 'pelatihan',
            'as' => 'pelatihan.',
        ], function ($router) {
            require module_path('Pelatihan', 'Routes/web.php');
        });
    }
}
