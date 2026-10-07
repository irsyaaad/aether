<?php

use Illuminate\Support\Facades\Route;
use Modules\Olahdata\Http\Controllers\OlahdataController;

Route::get('/', [OlahdataController::class, 'index'])->name('index');
Route::get('/upload-dataset', [OlahdataController::class, 'create'])->name('create');
Route::post('/upload-dataset', [OlahdataController::class, 'store'])->name('store');
Route::get('/diagnosis', [OlahdataController::class, 'diagnosis'])->name('diagnosis');
Route::get('/{id}', [OlahdataController::class, 'show'])->name('show');
