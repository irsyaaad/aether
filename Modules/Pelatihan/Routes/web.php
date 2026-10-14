<?php

use Illuminate\Support\Facades\Route;
use Modules\Pelatihan\Http\Controllers\PelatihanController;

Route::get('/', [PelatihanController::class, 'index'])->name('index');
Route::get('/katalog', [PelatihanController::class, 'katalog'])->name('katalog');
Route::get('/kelas-saya', [PelatihanController::class, 'kelasSaya'])->name('kelas-saya');
Route::get('/sertifikat', [PelatihanController::class, 'sertifikat'])->name('sertifikat');

// Mekanisme Pembayaran Pelatihan
Route::get('/kelas/{id}/payment', [PelatihanController::class, 'payment'])->name('payment');
Route::post('/kelas/{id}/payment', [PelatihanController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/instruction/{id}', [PelatihanController::class, 'paymentInstruction'])->name('payment.instruction');
Route::post('/payment/instruction/{id}/ganti-metode', [PelatihanController::class, 'gantiMetodeBayar'])->name('payment.ganti-metode');
Route::post('/payment/upload-bukti/{id}', [PelatihanController::class, 'uploadBuktiPembayaran'])->name('payment.upload-bukti');
Route::get('/payment/invoice/{id}', [PelatihanController::class, 'paymentInvoice'])->name('payment.invoice');

// Aksi Admin Verifikasi / Kelola Transaksi Pelatihan
Route::post('/transaksi/{id}/verifikasi', [PelatihanController::class, 'verifikasiPembayaran'])->name('transaksi.verifikasi');
Route::post('/transaksi/{id}/selesaikan', [PelatihanController::class, 'selesaikanPelatihan'])->name('transaksi.selesaikan');
Route::post('/transaksi/{id}/batalkan', [PelatihanController::class, 'batalkanPembayaran'])->name('transaksi.batalkan');

Route::get('/{id}', [PelatihanController::class, 'show'])->name('show');
Route::post('/{id}/daftar', [PelatihanController::class, 'daftar'])->name('daftar');

