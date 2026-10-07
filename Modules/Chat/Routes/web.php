<?php
use Modules\Chat\Http\Controllers\MutasiController;
use Modules\Chat\Http\Controllers\ChatController;
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

Route::group(['prefix' => '/', 'middleware' => ['auth', 'role']], function () {
    Route::get('/chat', 'ChatController@index');
    Route::get('/tes-api', [MutasiController::class, 'tes_api']);
    Route::post('/tes-api', [MutasiController::class, 'tes_api']);
    Route::get('/sync-mutasi', [MutasiController::class, 'syncMutasi']);
    Route::get('/dashboard-pad', [MutasiController::class, 'dashboard_pad']);
    Route::get('dashboard-pad/getCalculation', [MutasiController::class, 'calculation'])->name('dashboard.getCalculation');
    Route::get('/list-mutasi', [MutasiController::class, 'index']);
    Route::get('list-mutasi/data', [MutasiController::class, 'get_data'])->name('list-mutasi.data');
    Route::post('list-mutasi/changeType', [MutasiController::class, 'changeType']);
    Route::get('/cache', function () {
        Artisan::call('optimize');
        return 'Cached!';
    });
});

Route::group(['prefix' => '/', 'middleware' => ['auth', 'pad']], function () {
    Route::post('/tes-api', [MutasiController::class, 'tes_api']);
    Route::get('/sync-mutasi', [MutasiController::class, 'syncMutasi']);
    Route::get('/dashboard-pad', [MutasiController::class, 'dashboard_pad']);
    Route::get('dashboard-pad/getCalculation', [MutasiController::class, 'calculation'])->name('dashboard.getCalculation');
    Route::get('/list-mutasi', [MutasiController::class, 'index']);
    Route::get('list-mutasi/data', [MutasiController::class, 'get_data'])->name('list-mutasi.data');
    Route::post('list-mutasi/changeType', [MutasiController::class, 'changeType']);
});

Route::group(['prefix' => '/', 'middleware' => ['auth']], function () {
    Route::get('/chats', [ChatController::class, 'index']);
    Route::post('/chats', [ChatController::class, 'store']);
    Route::get('admin-chat', [ChatController::class, 'adminChat']);
    Route::get('/chat-users', [ChatController::class, 'users']);
    // Ambil chat per user
    Route::get('/chats/{userId}', [ChatController::class, 'userChats']);
    // Admin balas chat
    Route::post('/chats/{userId}/reply', [ChatController::class, 'adminReply']);
});

