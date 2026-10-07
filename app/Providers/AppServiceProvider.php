<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('phone_number', function($attribute, $value, $parameters)
        {
            return substr($value, 0, 2) == '01';
        });
        Validator::extend('alpha_spaces', function ($attribute, $value) {
            return preg_match('/^[\pL\s]+$/u', $value); 
        });
        
        Validator::extend('alphanum_spaces', function ($attribute, $value) {
            return preg_match('/^[a-zA-Z0-9 ]*$/u', $value); 
        });
        
        Validator::extend('gelar', function ($attribute, $value) {
            return preg_match('/^[a-zA-Z0-9 ,.(.*?)]*$/u', $value); 
        });

        // Load helper files
        require_once app_path('Helpers/KasBankHelper.php');

        // Auto-check academic database tables
        if (!\Illuminate\Support\Facades\Schema::hasTable('master_kelas')) {
            try {
                $migration = require database_path('migrations/2026_10_07_000001_create_academic_master_and_transaction_tables.php');
                $migration->up();
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning('Academic migration run notice: ' . $e->getMessage());
            }
        }
    }
}
