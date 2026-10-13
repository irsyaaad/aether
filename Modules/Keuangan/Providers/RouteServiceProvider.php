<?php

namespace Modules\Keuangan\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    protected $namespace = 'Modules\Keuangan\Http\Controllers';

    public function boot()
    {
        $this->mapWebRoutes();
    }

    protected function mapWebRoutes()
    {
        Route::group([
            'middleware' => ['web', 'auth'],
            'namespace' => $this->namespace,
        ], function ($router) {
            require module_path('Keuangan', 'Routes/web.php');
        });
    }
}
