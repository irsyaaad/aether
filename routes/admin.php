<?php
// route for administrator
use App\Http\Controllers\LayananController;
use App\Http\Controllers\ListTugasController;
use App\Http\Controllers\P_role;
use App\Http\Controllers\P_rolemenu;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => '/', 'middleware' => ['auth']], function () {
    Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
    Route::get('/dashboard/employee-performance-data', 'DashboardController@getEmployeePerformanceData')->name('dashboard.employee-performance-data');
    Route::get('/dashboard/recent-submissions-data', 'DashboardController@getRecentSubmissionsData')->name('dashboard.recent-submissions-data');
    Route::get('/switch-app/{appId}', [UserController::class, 'switchApp'])->name('app.switch');
    Route::get('/change-password', 'DashboardController@change_password')->name('change-password');
    Route::post('/change-password', 'DashboardController@save_change_password')->name('save_change_password');
    Route::get('/list-permohonan', 'DashboardController@list_permohonan')->name('admin.module');
    Route::resource('layanan', LayananController::class);

    // Arsip Dokumentasi routes
    Route::resource('arsip-dokumentasi', \App\Http\Controllers\ArsipDokumentasiController::class)->names('arsip-dokumentasi')->except('show');
    Route::get('arsip-dokumentasi/{id}/detail', [\App\Http\Controllers\ArsipDokumentasiController::class, 'show'])->name('arsip-dokumentasi.show');
    Route::get('arsip-dokumentasi/{id}/download', [\App\Http\Controllers\ArsipDokumentasiController::class, 'download'])->name('arsip-dokumentasi.download');
    Route::get('arsip-dokumentasi/get-data', [\App\Http\Controllers\ArsipDokumentasiController::class, 'getData'])->name('arsip-dokumentasi.get-data');
    Route::get('arsip-dokumentasi-debug', [\App\Http\Controllers\ArsipDokumentasiController::class, 'debug'])->name('arsip-dokumentasi.debug');

    Route::get('user/get-data', [UserController::class, 'get_data'])->name('user.get-data');
    Route::post('user/generate-users', [UserController::class, 'generateUsers'])->name('user.generate');
    Route::post('user/{id}/roles', [UserController::class, 'addRole'])->name('user.add-role');
    Route::resource('user', UserController::class)->names('users');
    Route::resource('role-menu', P_rolemenu::class);
    Route::resource('roles', P_role::class)->names('roles')->except('show');
    Route::get('roles/data', [P_role::class, 'get_data'])->name('roles.data');
    Route::get('roles/detail/{id}', [P_role::class, 'show'])->name('roles.show');
    Route::post('roles/duplicate/{id}', [P_role::class, 'duplicate'])->name('roles.duplicate');
    Route::post('roles/{id}/kas-bank', [P_role::class, 'updateKasBank'])->name('roles.update-kas-bank');

    Route::get('list-tugas', [ListTugasController::class, 'index'])->name('list-tugas.index');
    Route::post('list-tugas/board', [ListTugasController::class, 'storeBoard'])->name('list-tugas.board.store');
    Route::post('list-tugas/board/{boardId}/columns', [ListTugasController::class, 'storeColumn'])->name('list-tugas.columns.store');
    Route::put('list-tugas/columns/{columnId}', [ListTugasController::class, 'updateColumn'])->name('list-tugas.columns.update');
    Route::delete('list-tugas/columns/{columnId}', [ListTugasController::class, 'destroyColumn'])->name('list-tugas.columns.destroy');
    Route::post('list-tugas/board/{boardId}/cards', [ListTugasController::class, 'storeCard'])->name('list-tugas.cards.store');
    Route::get('list-tugas/cards/{cardId}/detail', [ListTugasController::class, 'showCard'])->name('list-tugas.cards.show');
    Route::put('list-tugas/cards/{cardId}', [ListTugasController::class, 'updateCard'])->name('list-tugas.cards.update');
    Route::delete('list-tugas/cards/{cardId}', [ListTugasController::class, 'destroyCard'])->name('list-tugas.cards.destroy');
    Route::post('list-tugas/cards/{cardId}/move', [ListTugasController::class, 'moveCard'])->name('list-tugas.cards.move');
    Route::post('list-tugas/cards/{cardId}/checklist', [ListTugasController::class, 'storeChecklist'])->name('list-tugas.checklist.store');
    Route::patch('list-tugas/checklist/{itemId}/toggle', [ListTugasController::class, 'toggleChecklist'])->name('list-tugas.checklist.toggle');
    Route::delete('list-tugas/checklist/{itemId}', [ListTugasController::class, 'destroyChecklist'])->name('list-tugas.checklist.destroy');
    Route::post('list-tugas/cards/{cardId}/comments', [ListTugasController::class, 'storeComment'])->name('list-tugas.comments.store');
    Route::delete('list-tugas/comments/{commentId}', [ListTugasController::class, 'destroyComment'])->name('list-tugas.comments.destroy');
    Route::post('list-tugas/cards/{cardId}/attachments', [ListTugasController::class, 'storeAttachment'])->name('list-tugas.attachments.store');
    Route::delete('list-tugas/attachments/{attachmentId}', [ListTugasController::class, 'destroyAttachment'])->name('list-tugas.attachments.destroy');

    Route::post('/ai-chat/send', 'AIChatController@send')->name('ai-chat.send');
});
Route::get('/debug-permission', function () {
    return [
        'user'        => auth()->user()->username ?? null,
        'roles'       => auth()->user()->getRoleNames(),
        'permissions' => auth()->user()->getPermissionNames(),
        'can_index'   => auth()->user()->can('pembelian.index'),
    ];
});
Route::get('/test-assign', function () {
    $user = auth()->user();
    $user->assignRole('admin');
    return [
        $user->getRoleNames(),
        $user->getPermissionNames(),
    ];
});
Route::get('/test-permission', function () {
    return [
        'auth'       => auth()->check(),
        'can'        => auth()->user()->can('purchase.index'),
        'middleware' => app(\Spatie\Permission\Middlewares\PermissionMiddleware::class),
    ];
});
