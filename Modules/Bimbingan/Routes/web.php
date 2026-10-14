<?php

use Illuminate\Support\Facades\Route;
use Modules\Bimbingan\Http\Controllers\BimbinganController;
use Modules\Bimbingan\Http\Controllers\ProgramInvoiceController;
use Modules\Bimbingan\Http\Controllers\ProgramPembayaranController;

Route::get('/', [BimbinganController::class, 'index'])->name('index');
Route::get('/programs', [BimbinganController::class, 'programs'])->name('programs');
Route::get('/programs/{id}/payment', [BimbinganController::class, 'payment'])->name('payment');
Route::get('/payment/{id}', [BimbinganController::class, 'payment']);
Route::post('/programs/{id}/payment', [BimbinganController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/instruction/{id}', [BimbinganController::class, 'paymentInstruction'])->name('payment.instruction');
Route::post('/payment/instruction/{id}/ganti-metode', [BimbinganController::class, 'gantiMetodeBayar'])->name('payment.ganti-metode');
Route::post('/payment/upload-bukti/{id}', [BimbinganController::class, 'uploadBuktiPembayaran'])->name('payment.upload-bukti');
Route::get('/payment/invoice/{id}', [BimbinganController::class, 'paymentSuccess'])->name('payment.invoice');
Route::post('/programs/{id}/beli', [BimbinganController::class, 'beliProgram'])->name('programs.beli');
Route::get('/katalog', [BimbinganController::class, 'programs'])->name('katalog');
Route::get('/pengajuan', [BimbinganController::class, 'create'])->name('create');
Route::post('/pengajuan', [BimbinganController::class, 'store'])->name('store');
Route::get('/jadwal', [BimbinganController::class, 'jadwal'])->name('jadwal');
Route::get('/riwayat', [BimbinganController::class, 'riwayat'])->name('riwayat');

// Rekap Invoice Program
Route::get('/invoices', [ProgramInvoiceController::class, 'index'])->name('invoices.index');
Route::get('/invoices/{id}', [ProgramInvoiceController::class, 'show'])->name('invoices.show');
Route::get('/invoices/{id}/print', [ProgramInvoiceController::class, 'print'])->name('invoices.print');

// List & Monitoring Pembayaran Program
Route::get('/pembayaran', [ProgramPembayaranController::class, 'index'])->name('pembayaran.index');
Route::get('/pembayaran/{id}', [ProgramPembayaranController::class, 'show'])->name('pembayaran.show');
Route::post('/pembayaran/{id}/status', [ProgramPembayaranController::class, 'updateStatus'])->name('pembayaran.update-status');

Route::get('/{id}', [BimbinganController::class, 'show'])->name('show');

