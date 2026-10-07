<?php

namespace Modules\Chat\Providers;

use Illuminate\Support\ServiceProvider;
class ChatServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerFactories();
        $this->loadMigrationsFrom(module_path('Chat', 'Database/Migrations'));
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->publishes([
            module_path('Chat', 'Config/config.php') => config_path('chat.php'),
        ], 'config');
        $this->mergeConfigFrom(
            module_path('Chat', 'Config/config.php'), 'chat'
        );
    }

    /**
     * Register views.
     *
     * @return void
     */
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/chat');

        $sourcePath = module_path('Chat', 'Resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ],'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/chat';
        }, \Config::get('view.paths')), [$sourcePath]), 'chat');
    }

    /**
     * Register translations.
     *
     * @return void
     */
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/chat');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'chat');
        } else {
            $this->loadTranslationsFrom(module_path('Chat', 'Resources/lang'), 'chat');
        }
    }

    /**
     * Register an additional directory of factories.
     *
     * @return void
     */
   
    public function registerFactories()
    {
        if (!app()->environment('production') && $this->app->runningInConsole()) {
            // Menggunakan loadFactoriesFrom untuk factory berbasis class di Laravel 8
            $this->loadFactoriesFrom(module_path('Chat', 'Database/factories'));
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
