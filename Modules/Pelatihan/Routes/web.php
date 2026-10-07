<?php

use Illuminate\Support\Facades\Route;
use Modules\Pelatihan\Http\Controllers\PelatihanController;

Route::get('/', [PelatihanController::class, 'index'])->name('index');
Route::get('/katalog', [PelatihanController::class, 'katalog'])->name('katalog');
Route::get('/kelas-saya', [PelatihanController::class, 'kelasSaya'])->name('kelas-saya');
Route::get('/sertifikat', [PelatihanController::class, 'sertifikat'])->name('sertifikat');
Route::get('/{id}', [PelatihanController::class, 'show'])->name('show');
Route::post('/{id}/daftar', [PelatihanController::class, 'daftar'])->name('daftar');
