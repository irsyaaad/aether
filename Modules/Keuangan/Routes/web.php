<?php

use Illuminate\Support\Facades\Route;
use Modules\Keuangan\Http\Controllers\KasBankController;

Route::group(['middleware' => ['auth']], function () {

    // Transaksi Kas & Bank Masuk
    Route::get('kas-bank-masuk', [KasBankController::class, 'indexMasuk'])->name('kas-bank-masuk.index');
    Route::get('kas-bank-masuk/data', [KasBankController::class, 'getDataMasuk'])->name('kas-bank-masuk.data');
    Route::get('kas-bank-masuk/stats', [KasBankController::class, 'getStatsMasuk'])->name('kas-bank-masuk.stats');
    Route::get('kas-bank-masuk/cetak', [KasBankController::class, 'cetakMasuk'])->name('kas-bank-masuk.cetak');
    Route::get('kas-bank-masuk/export-excel', [KasBankController::class, 'exportExcelMasuk'])->name('kas-bank-masuk.export-excel');
    Route::get('kas-bank-masuk/create', [KasBankController::class, 'createMasuk'])->name('kas-bank-masuk.create');
    Route::post('kas-bank-masuk', [KasBankController::class, 'storeMasuk'])->name('kas-bank-masuk.store');
    Route::get('kas-bank-masuk/{id}', [KasBankController::class, 'show'])->name('kas-bank-masuk.show');
    Route::get('kas-bank-masuk/{id}/edit', [KasBankController::class, 'editMasuk'])->name('kas-bank-masuk.edit');
    Route::put('kas-bank-masuk/{id}', [KasBankController::class, 'update'])->name('kas-bank-masuk.update');
    Route::delete('kas-bank-masuk/{id}', [KasBankController::class, 'destroy'])->name('kas-bank-masuk.destroy');

    // Transaksi Kas & Bank Keluar
    Route::get('kas-bank-keluar', [KasBankController::class, 'indexKeluar'])->name('kas-bank-keluar.index');
    Route::get('kas-bank-keluar/data', [KasBankController::class, 'getDataKeluar'])->name('kas-bank-keluar.data');
    Route::get('kas-bank-keluar/stats', [KasBankController::class, 'getStatsKeluar'])->name('kas-bank-keluar.stats');
    Route::get('kas-bank-keluar/cetak', [KasBankController::class, 'cetakKeluar'])->name('kas-bank-keluar.cetak');
    Route::get('kas-bank-keluar/export-excel', [KasBankController::class, 'exportExcelKeluar'])->name('kas-bank-keluar.export-excel');
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

    // Jurnal Umum
    Route::get('jurnal', [\Modules\Keuangan\Http\Controllers\JurnalController::class, 'index'])->name('jurnal.index');
    Route::get('jurnal/data', [\Modules\Keuangan\Http\Controllers\JurnalController::class, 'getData'])->name('jurnal.data');
    Route::get('jurnal/stats', [\Modules\Keuangan\Http\Controllers\JurnalController::class, 'getStats'])->name('jurnal.stats');
    Route::get('jurnal/select2/coa', [\Modules\Keuangan\Http\Controllers\JurnalController::class, 'select2Coa'])->name('jurnal.select2.coa');
    Route::get('jurnal/cetak', [\Modules\Keuangan\Http\Controllers\JurnalController::class, 'cetak'])->name('jurnal.cetak');
    Route::get('jurnal/export-excel', [\Modules\Keuangan\Http\Controllers\JurnalController::class, 'exportExcel'])->name('jurnal.export-excel');

    // Neraca Keuangan
    Route::get('neraca', [\Modules\Keuangan\Http\Controllers\NeracaController::class, 'index'])->name('neraca.index');
    Route::get('neraca/data', [\Modules\Keuangan\Http\Controllers\NeracaController::class, 'getData'])->name('neraca.data');
    Route::get('neraca/rincian/{coa_id}', [\Modules\Keuangan\Http\Controllers\NeracaController::class, 'rincian'])->name('neraca.rincian');
    Route::get('neraca/cetak', [\Modules\Keuangan\Http\Controllers\NeracaController::class, 'cetak'])->name('neraca.cetak');
    Route::get('neraca/export-excel', [\Modules\Keuangan\Http\Controllers\NeracaController::class, 'exportExcel'])->name('neraca.export-excel');

    // Laba Rugi
    Route::get('rugi-laba', [\Modules\Keuangan\Http\Controllers\RugiLabaController::class, 'index'])->name('rugi-laba.index');
    Route::get('rugi-laba/data', [\Modules\Keuangan\Http\Controllers\RugiLabaController::class, 'getData'])->name('rugi-laba.data');
    Route::get('rugi-laba/rincian/{coa_id}', [\Modules\Keuangan\Http\Controllers\RugiLabaController::class, 'rincian'])->name('rugi-laba.rincian');
    Route::get('rugi-laba/cetak', [\Modules\Keuangan\Http\Controllers\RugiLabaController::class, 'cetak'])->name('rugi-laba.cetak');
    Route::get('rugi-laba/export-excel', [\Modules\Keuangan\Http\Controllers\RugiLabaController::class, 'exportExcel'])->name('rugi-laba.export-excel');

    // Arus Kas (Cash Flow)
    Route::get('cashflow', [\Modules\Keuangan\Http\Controllers\CashFlowController::class, 'index'])->name('cashflow.index');
    Route::get('cashflow/data', [\Modules\Keuangan\Http\Controllers\CashFlowController::class, 'getData'])->name('cashflow.data');
    Route::get('cashflow/rincian/{coa_id}', [\Modules\Keuangan\Http\Controllers\CashFlowController::class, 'rincian'])->name('cashflow.rincian');
    Route::get('cashflow/cetak', [\Modules\Keuangan\Http\Controllers\CashFlowController::class, 'cetak'])->name('cashflow.cetak');
    Route::get('cashflow/export-excel', [\Modules\Keuangan\Http\Controllers\CashFlowController::class, 'exportExcel'])->name('cashflow.export-excel');
});

