<?php

use Illuminate\Support\Facades\Route;
use Modules\Bimbingan\Http\Controllers\BimbinganController;

Route::get('/', [BimbinganController::class, 'index'])->name('index');
Route::get('/pengajuan', [BimbinganController::class, 'create'])->name('create');
Route::post('/pengajuan', [BimbinganController::class, 'store'])->name('store');
Route::get('/jadwal', [BimbinganController::class, 'jadwal'])->name('jadwal');
Route::get('/riwayat', [BimbinganController::class, 'riwayat'])->name('riwayat');
Route::get('/{id}', [BimbinganController::class, 'show'])->name('show');
