<?php

use Illuminate\Support\Facades\Route;
use Modules\Master\Http\Controllers\WilayahController;
use Modules\Master\Http\Controllers\LayananController;
use Modules\Master\Http\Controllers\ProvinsiController;
use Modules\Master\Http\Controllers\KotaKabController;
use Modules\Master\Http\Controllers\KecamatanController;
use Modules\Master\Http\Controllers\DesaController;

Route::group(['prefix' => 'master', 'middleware' => ['auth']], function () {

    // Master Chart of Accounts (CoA)
    Route::get('coa/get-data', [\Modules\Master\Http\Controllers\MasterCoaController::class, 'getData'])->name('master.coa.get-data');
    Route::resource('coa', \Modules\Master\Http\Controllers\MasterCoaController::class)->names('master.coa');

    // Master Program Akademik & Bimbingan (Dynamic CRUD)
    Route::get('programs/get-data', [\Modules\Master\Http\Controllers\MasterProgramController::class, 'getData'])->name('master.programs.get-data');
    Route::post('programs/{id}/toggle-status', [\Modules\Master\Http\Controllers\MasterProgramController::class, 'toggleStatusProgram'])->name('master.programs.toggle-status');
    Route::post('programs/transaksi/{id}/batalkan', [\Modules\Master\Http\Controllers\MasterProgramController::class, 'batalkanPembayaranTransaksi'])->name('master.programs.transaksi.batalkan');
    Route::post('programs/transaksi/{id}/verifikasi', [\Modules\Master\Http\Controllers\MasterProgramController::class, 'verifikasiPembayaranTransaksi'])->name('master.programs.transaksi.verifikasi');
    Route::post('programs/transaksi/{id}/selesaikan', [\Modules\Master\Http\Controllers\MasterProgramController::class, 'selesaikanProgramTransaksi'])->name('master.programs.transaksi.selesaikan');
    Route::post('programs/transaksi/{id}/toggle-user', [\Modules\Master\Http\Controllers\MasterProgramController::class, 'toggleStatusUserTransaksi'])->name('master.programs.transaksi.toggle-user');
    Route::resource('programs', \Modules\Master\Http\Controllers\MasterProgramController::class)->names([
        'index' => 'master.programs.index',
        'create' => 'master.programs.create',
        'store' => 'master.programs.store',
        'show' => 'master.programs.show',
        'edit' => 'master.programs.edit',
        'update' => 'master.programs.update',
        'destroy' => 'master.programs.destroy',
    ]);

    // Master Mentor & Konsultan Riset (Dynamic CRUD)
    Route::get('mentors/get-data', [\Modules\Master\Http\Controllers\MasterMentorController::class, 'getData'])->name('master.mentors.get-data');
    Route::resource('mentors', \Modules\Master\Http\Controllers\MasterMentorController::class)->names([
        'index' => 'master.mentors.index',
        'create' => 'master.mentors.create',
        'store' => 'master.mentors.store',
        'show' => 'master.mentors.show',
        'edit' => 'master.mentors.edit',
        'update' => 'master.mentors.update',
        'destroy' => 'master.mentors.destroy',
    ]);

    // Master Kelas & Workshop (Dynamic CRUD)
    Route::get('kelas/get-data', [\Modules\Master\Http\Controllers\MasterKelasController::class, 'getData'])->name('master.kelas.get-data');
    Route::post('kelas/{id}/update-status', [\Modules\Master\Http\Controllers\MasterKelasController::class, 'updateStatus'])->name('master.kelas.update-status');
    Route::post('kelas/transaksi/{id}/verifikasi', [\Modules\Pelatihan\Http\Controllers\PelatihanController::class, 'verifikasiPembayaran'])->name('master.kelas.transaksi.verifikasi');
    Route::post('kelas/transaksi/{id}/selesaikan', [\Modules\Pelatihan\Http\Controllers\PelatihanController::class, 'selesaikanPelatihan'])->name('master.kelas.transaksi.selesaikan');
    Route::post('kelas/transaksi/{id}/batalkan', [\Modules\Pelatihan\Http\Controllers\PelatihanController::class, 'batalkanPembayaran'])->name('master.kelas.transaksi.batalkan');
    Route::resource('kelas', \Modules\Master\Http\Controllers\MasterKelasController::class)->names('master.kelas');

    // Master Materi & Silabus (Dynamic CRUD)
    Route::get('materi/get-data', [\Modules\Master\Http\Controllers\MasterMateriController::class, 'getData'])->name('master.materi.get-data');
    Route::resource('materi', \Modules\Master\Http\Controllers\MasterMateriController::class)->names('master.materi');

    // Master Metode Riset & Software (Dynamic CRUD)
    Route::get('metode/get-data', [\Modules\Master\Http\Controllers\MasterMetodeRisetController::class, 'getData'])->name('master.metode.get-data');
    Route::resource('metode', \Modules\Master\Http\Controllers\MasterMetodeRisetController::class)->names('master.metode');

    // Wilayah Tree (single page)
    Route::get('wilayah', [WilayahController::class, 'index'])->name('wilayah.index');
    Route::get('wilayah/children', [WilayahController::class, 'getChildren'])->name('wilayah.get-children');
    Route::post('wilayah/store', [WilayahController::class, 'store'])->name('wilayah.store');
    Route::post('wilayah/{id}/update', [WilayahController::class, 'update'])->name('wilayah.update');
    Route::post('wilayah/{id}/destroy', [WilayahController::class, 'destroy'])->name('wilayah.destroy');

    // Legacy CRUD routes (kept for backward compatibility)
    Route::get('provinsi/get-data', [ProvinsiController::class, 'getData'])->name('wilayah-provinsi.get-data');
    Route::resource('provinsi', ProvinsiController::class)
        ->names('wilayah-provinsi')
        ->except('show');

    Route::get('kota-kab/get-data', [KotaKabController::class, 'getData'])->name('wilayah-kota-kab.get-data');
    Route::get('kota-kab/get-regencies/{provinceId}', [KotaKabController::class, 'getRegenciesByProvince'])->name('wilayah-kota-kab.get-regencies');
    Route::resource('kota-kab', KotaKabController::class)
        ->names('wilayah-kota-kab')
        ->except('show');

    Route::get('kecamatan/get-data', [KecamatanController::class, 'getData'])->name('wilayah-kecamatan.get-data');
    Route::get('kecamatan/get-regencies/{provinceId}', [KecamatanController::class, 'getRegenciesByProvince'])->name('wilayah-kecamatan.get-regencies');
    Route::get('kecamatan/get-districts/{regencyId}', [KecamatanController::class, 'getDistrictsByRegency'])->name('wilayah-kecamatan.get-districts');
    Route::resource('kecamatan', KecamatanController::class)
        ->names('wilayah-kecamatan')
        ->except('show');

    Route::get('desa/get-data', [DesaController::class, 'getData'])->name('wilayah-desa.get-data');
    Route::get('desa/get-regencies/{provinceId}', [DesaController::class, 'getRegenciesByProvince'])->name('wilayah-desa.get-regencies');
    Route::get('desa/get-districts/{regencyId}', [DesaController::class, 'getDistrictsByRegency'])->name('wilayah-desa.get-districts');
    Route::get('desa/get-villages/{districtId}', [DesaController::class, 'getVillagesByDistrict'])->name('wilayah-desa.get-villages');
    Route::resource('desa', DesaController::class)
        ->names('wilayah-desa')
        ->except('show');

    // Layanan
    Route::get('layanan/get-data', [LayananController::class, 'getData'])->name('layanan.get-data');
    Route::resource('layanan', LayananController::class)
        ->names('layanan')
        ->except('show');
});
