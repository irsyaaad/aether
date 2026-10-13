<?php

use Illuminate\Support\Facades\Route;
use Modules\Keuangan\Http\Controllers\KasBankController;

Route::group(['middleware' => ['auth']], function () {

    // Transaksi Kas & Bank Masuk
    Route::get('kas-bank-masuk', [KasBankController::class, 'indexMasuk'])->name('kas-bank-masuk.index');
    Route::get('kas-bank-masuk/data', [KasBankController::class, 'getDataMasuk'])->name('kas-bank-masuk.data');
    Route::get('kas-bank-masuk/create', [KasBankController::class, 'createMasuk'])->name('kas-bank-masuk.create');
    Route::post('kas-bank-masuk', [KasBankController::class, 'storeMasuk'])->name('kas-bank-masuk.store');
    Route::get('kas-bank-masuk/{id}', [KasBankController::class, 'show'])->name('kas-bank-masuk.show');
    Route::get('kas-bank-masuk/{id}/edit', [KasBankController::class, 'editMasuk'])->name('kas-bank-masuk.edit');
    Route::put('kas-bank-masuk/{id}', [KasBankController::class, 'update'])->name('kas-bank-masuk.update');
    Route::delete('kas-bank-masuk/{id}', [KasBankController::class, 'destroy'])->name('kas-bank-masuk.destroy');

    // Transaksi Kas & Bank Keluar
    Route::get('kas-bank-keluar', [KasBankController::class, 'indexKeluar'])->name('kas-bank-keluar.index');
    Route::get('kas-bank-keluar/data', [KasBankController::class, 'getDataKeluar'])->name('kas-bank-keluar.data');
    Route::get('kas-bank-keluar/create', [KasBankController::class, 'createKeluar'])->name('kas-bank-keluar.create');
    Route::post('kas-bank-keluar', [KasBankController::class, 'storeKeluar'])->name('kas-bank-keluar.store');
    Route::get('kas-bank-keluar/{id}', [KasBankController::class, 'show'])->name('kas-bank-keluar.show');
    Route::get('kas-bank-keluar/{id}/edit', [KasBankController::class, 'editKeluar'])->name('kas-bank-keluar.edit');
    Route::put('kas-bank-keluar/{id}', [KasBankController::class, 'update'])->name('kas-bank-keluar.update');
    Route::delete('kas-bank-keluar/{id}', [KasBankController::class, 'destroy'])->name('kas-bank-keluar.destroy');

    // Helper API & Cetak Lembar Voucher
    Route::get('kas-bank/next-nomor', [KasBankController::class, 'getNextNomor'])->name('kas-bank.next-nomor');
    Route::get('kas-bank/voucher/{id}', [KasBankController::class, 'voucher'])->name('kas-bank.voucher');

    // Select2 Ajax Endpoints
    Route::get('kas-bank/select2/kas-bank', [KasBankController::class, 'select2KasBank'])->name('kas-bank.select2.kas-bank');
    Route::get('kas-bank/select2/lawan', [KasBankController::class, 'select2Lawan'])->name('kas-bank.select2.lawan');
});
