<?php

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\SettingPerusahaanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['prefix' => '/', 'middleware' => ['auth']], function () {
    Route::get('/cache', function () {
        Artisan::call('optimize');
        return 'Cached!';
    });
});

Route::group(['prefix' => 'setting', 'middleware' => ['auth']], function () {
    Route::get('/perusahaan', [SettingPerusahaanController::class, 'edit'])->name('setting.perusahaan.edit');
    Route::put('/perusahaan', [SettingPerusahaanController::class, 'update'])->name('setting.perusahaan.update');
});
