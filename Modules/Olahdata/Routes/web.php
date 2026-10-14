<?php

use Illuminate\Support\Facades\Route;
use Modules\Olahdata\Http\Controllers\OlahdataController;

Route::get('/', [OlahdataController::class, 'index'])->name('index');
Route::get('/upload-dataset', [OlahdataController::class, 'create'])->name('create');
Route::post('/upload-dataset', [OlahdataController::class, 'store'])->name('store');
Route::get('/diagnosis', [OlahdataController::class, 'diagnosis'])->name('diagnosis');

// Mekanisme Pembayaran Olah Data
Route::get('/checkout/{id}', [OlahdataController::class, 'paymentCheckout'])->name('payment');
Route::post('/checkout/{id}', [OlahdataController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/instruction/{id}', [OlahdataController::class, 'paymentInstruction'])->name('payment.instruction');
Route::post('/payment/instruction/{id}/ganti-metode', [OlahdataController::class, 'gantiMetodeBayar'])->name('payment.ganti-metode');
Route::post('/payment/upload-bukti/{id}', [OlahdataController::class, 'uploadBuktiPembayaran'])->name('payment.upload-bukti');
Route::get('/payment/invoice/{id}', [OlahdataController::class, 'paymentInvoice'])->name('payment.invoice');

// Aksi Admin Verifikasi / Kelola Transaksi Olah Data
Route::post('/transaksi/{id}/verifikasi', [OlahdataController::class, 'verifikasiPembayaran'])->name('transaksi.verifikasi');
Route::post('/transaksi/{id}/selesaikan', [OlahdataController::class, 'selesaikanProject'])->name('transaksi.selesaikan');
Route::post('/transaksi/{id}/batalkan', [OlahdataController::class, 'batalkanPembayaran'])->name('transaksi.batalkan');

Route::get('/{id}', [OlahdataController::class, 'show'])->name('show');

