<?php

namespace Modules\Task\Providers;

use Illuminate\Support\ServiceProvider;

class TaskServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path('Task', 'Database/Migrations'));
    }

    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
        $this->commands([
            \Modules\Task\Console\GenerateRecurringTasksCommand::class,
        ]);
    }

    protected function registerConfig()
    {
        $this->publishes([
            module_path('Task', 'Config/config.php') => config_path('task.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Task', 'Config/config.php'), 'task'
        );
    }

    public function registerViews()
    {
        $viewPath = resource_path('views/modules/task');
        $sourcePath = module_path('Task', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/task';
        }, \Config::get('view.paths')), [$sourcePath]), 'task');
    }

    public function provides()
    {
        return [];
    }
}
