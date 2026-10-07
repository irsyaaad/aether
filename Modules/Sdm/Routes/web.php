<?php
use Illuminate\Support\Facades\Route;
use Modules\Sdm\Http\Controllers\AbsensiController;
use Modules\Sdm\Http\Controllers\AdsController;
use Modules\Sdm\Http\Controllers\DailyReportController;
use Modules\Sdm\Http\Controllers\EmployeeActivityController;
use Modules\Sdm\Http\Controllers\HandoverRecordController;
use Modules\Sdm\Http\Controllers\IzinController;
use Modules\Sdm\Http\Controllers\JabatanController;
use Modules\Sdm\Http\Controllers\JobDescriptionController;
use Modules\Sdm\Http\Controllers\KandidatController;
use Modules\Sdm\Http\Controllers\KartuPiutangController;
use Modules\Sdm\Http\Controllers\KaryawanController;
use Modules\Sdm\Http\Controllers\KontrakKaryawanController;
use Modules\Sdm\Http\Controllers\KpiController;
use Modules\Sdm\Http\Controllers\KpiRealisasiController;
use Modules\Sdm\Http\Controllers\KpiTargetController;
use Modules\Sdm\Http\Controllers\MasterTrainingController;
use Modules\Sdm\Http\Controllers\MeetingController;
use Modules\Sdm\Http\Controllers\PayrollComponentController;
use Modules\Sdm\Http\Controllers\PayrollPeriodController;
use Modules\Sdm\Http\Controllers\PayrollSlipController;
use Modules\Sdm\Http\Controllers\PerusahaanController;
use Modules\Sdm\Http\Controllers\PiutangKaryawanController;
use Modules\Sdm\Http\Controllers\PkwtController;
use Modules\Sdm\Http\Controllers\RekrutmentController;
use Modules\Sdm\Http\Controllers\SopController;
use Modules\Sdm\Http\Controllers\TrainingController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */
Route::group(['prefix' => '/', 'middleware' => ['auth']], function () {
    Route::resource('karyawan', KaryawanController::class)->names('karyawan')->except('show');
    Route::get('karyawan/{id}/detail', [KaryawanController::class, 'show'])->name('karyawan.detail');
    Route::resource('iklan', AdsController::class)->names('ads')->except('show');
    Route::get('iklan/{id}/detail', [AdsController::class, 'show'])->name('ads.detail');
    Route::resource('training', TrainingController::class)->names('training')->except('show');
    Route::get('training/{id}/detail', [TrainingController::class, 'show'])->name('training.detail');
    Route::resource('handover', HandoverRecordController::class)->names('handover')->except('show');
    Route::get('handover/{id}/detail', [HandoverRecordController::class, 'show'])->name('handover.detail');
    Route::resource('perusahaan', PerusahaanController::class)->names('perusahaan')->except('show');
    Route::get('perusahaan/{id}/detail', [PerusahaanController::class, 'show'])->name('perusahaan.detail');
    Route::resource('kandidat', KandidatController::class)->names('kandidat')->except('show');
    Route::get('kandidat/{id}/detail', [KandidatController::class, 'show'])->name('kandidat.detail');

    // Rekrutment (Kanban) routes
    Route::get('rekrutment', [RekrutmentController::class, 'index'])->name('rekrutment.index');
    Route::post('rekrutment', [RekrutmentController::class, 'store'])->name('rekrutment.store');
    Route::get('rekrutment/{id}', [RekrutmentController::class, 'show'])->name('rekrutment.show');
    Route::put('rekrutment/{id}', [RekrutmentController::class, 'update'])->name('rekrutment.update');
    Route::patch('rekrutment/{id}/status', [RekrutmentController::class, 'updateStatus'])->name('rekrutment.update-status');
    Route::post('rekrutment/{id}/move', [RekrutmentController::class, 'move'])->name('rekrutment.move');
    Route::delete('rekrutment/{id}', [RekrutmentController::class, 'destroy'])->name('rekrutment.destroy');

    Route::resource('absensi', AbsensiController::class)->names('absensi')->except('show');
    Route::get('absensi/{id}/detail', [AbsensiController::class, 'show'])->name('absensi.detail');
    Route::resource('employee-activities', EmployeeActivityController::class)->names('employee-activities')->except('show');
    Route::get('employee-activities/{id}/detail', [EmployeeActivityController::class, 'show'])->name('employee-activities.detail');

    // Daily Report routes
    Route::resource('daily-report', DailyReportController::class)->names('daily-report')->except('show');
    Route::get('daily-report/{id}/detail', [DailyReportController::class, 'show'])->name('daily-report.show');
    Route::get('daily-report-summary', [DailyReportController::class, 'summary'])->name('daily-report.summary');

    // Master Laporan / Activity Reporting
    Route::get('master-laporan', [EmployeeActivityController::class, 'masterReport'])->name('employee-activities.master-report');
    Route::post('master-laporan/fix-do', [EmployeeActivityController::class, 'fixDoAjax'])->name('employee-activities.fix-do');
    Route::post('master-laporan/approve-do', [EmployeeActivityController::class, 'approveDoAjax'])->name('employee-activities.approve-do');
    Route::post('master-laporan/save-daily-plan', [EmployeeActivityController::class, 'saveDailyPlanAjax'])->name('employee-activities.save-daily-plan');
    Route::get('employee-activities/get-do-count', [EmployeeActivityController::class, 'getDoTodayCountAjax'])->name('employee-activities.get-do-count');

    Route::get('laporan-absensi', [AbsensiController::class, 'index_laporan_absensi'])->name('laporan-absensi.index');

    // Izin routes
    Route::resource('izin', IzinController::class)->names('izin')->except('show');
    Route::get('izin/{id}/detail', [IzinController::class, 'show'])->name('izin.show');
    Route::patch('izin/{id}/update-status', [IzinController::class, 'updateStatus'])->name('izin.update-status');

    // Piutang Karyawan routes
    Route::resource('piutang-karyawan', PiutangKaryawanController::class)->names('piutang-karyawan')->except('show');
    Route::get('piutang-karyawan/{id}/detail', [PiutangKaryawanController::class, 'show'])->name('piutang-karyawan.show');
    Route::post('piutang-karyawan/{id}/bayar', [PiutangKaryawanController::class, 'storePembayaran'])->name('piutang-karyawan.bayar');
    Route::delete('piutang-karyawan/{id}/pembayaran/{pembayaranId}', [PiutangKaryawanController::class, 'deletePembayaran'])->name('piutang-karyawan.delete-pembayaran');
    Route::get('piutang-karyawan/{id}/pembayaran/{pembayaranId}/edit', [PiutangKaryawanController::class, 'editPembayaran'])->name('piutang-karyawan.edit-pembayaran');
    Route::put('piutang-karyawan/{id}/pembayaran/{pembayaranId}', [PiutangKaryawanController::class, 'updatePembayaran'])->name('piutang-karyawan.update-pembayaran');

    // Kontrak Karyawan routes
    Route::resource('kontrak-karyawan', KontrakKaryawanController::class)->names('kontrak-karyawan');

    // Kartu Piutang routes
    Route::get('kartu-piutang', [KartuPiutangController::class, 'index'])->name('kartu-piutang.index');
    Route::get('kartu-piutang/get-data', [KartuPiutangController::class, 'getData'])->name('kartu-piutang.get-data');
    Route::get('kartu-piutang/{id}', [KartuPiutangController::class, 'show'])->name('kartu-piutang.show');
    Route::get('kartu-piutang/{id}/cetak', [KartuPiutangController::class, 'cetak'])->name('kartu-piutang.cetak');
    Route::get('kartu-piutang/{id}/export-excel', [KartuPiutangController::class, 'exportExcel'])->name('kartu-piutang.export-excel');

    // Payroll AJAX routes (must be before resource routes)
    Route::get('payroll-period/get-data', [PayrollPeriodController::class, 'getData'])->name('payroll-period.get-data');
    Route::get('payroll-component/get-data', [PayrollComponentController::class, 'getData'])->name('payroll-component.get-data');
    Route::get('payroll-slip/get-data', [PayrollSlipController::class, 'getData'])->name('payroll-slip.get-data');

    // Payroll Period routes
    Route::resource('payroll-period', PayrollPeriodController::class)->names('payroll-period')->except('show');
    Route::get('payroll-period/{id}/detail', [PayrollPeriodController::class, 'show'])->name('payroll-period.show');
    Route::post('payroll-period/{id}/generate-slips', [PayrollPeriodController::class, 'generateSlips'])->name('payroll-period.generate-slips');

    // Payroll Component routes
    Route::resource('payroll-component', PayrollComponentController::class)->names('payroll-component')->except('show');
    Route::get('payroll-component/{id}/detail', [PayrollComponentController::class, 'show'])->name('payroll-component.show');
    Route::patch('payroll-component/{id}/toggle-status', [PayrollComponentController::class, 'toggleStatus'])->name('payroll-component.toggle-status');

    // Payroll Slip routes
    Route::resource('payroll-slip', PayrollSlipController::class)->names('payroll-slip')->only(['index', 'show', 'edit', 'update']);
    Route::patch('payroll-slip/{id}/approve', [PayrollSlipController::class, 'approve'])->name('payroll-slip.approve');
    Route::patch('payroll-slip/{id}/mark-as-paid', [PayrollSlipController::class, 'markAsPaid'])->name('payroll-slip.mark-as-paid');
    Route::get('payroll-slip/{id}/print', [PayrollSlipController::class, 'print'])->name('payroll-slip.print');
    Route::post('payroll-slip/{id}/add-component', [PayrollSlipController::class, 'addComponent'])->name('payroll-slip.add-component');
    Route::delete('payroll-slip/{slipId}/remove-component/{detailId}', [PayrollSlipController::class, 'removeComponent'])->name('payroll-slip.remove-component');

    // Jabatan Routes
    Route::resource('jabatan', JabatanController::class)->names('jabatan')->except('show');
    Route::get('jabatan/{id}/detail', [JabatanController::class, 'show'])->name('jabatan.show');

    // SOP Routes
    Route::resource('sop', SopController::class)->names('sop')->except('show');
    Route::get('sop/{id}/detail', [SopController::class, 'show'])->name('sop.show');

    // Job Description Routes
    Route::resource('job-description', JobDescriptionController::class)->names('job-description')->except('show');
    Route::get('job-description/{id}/detail', [JobDescriptionController::class, 'show'])->name('job-description.show');

    // KPI Routes
    Route::resource('kpi', KpiController::class)->names('kpi')->except('show');
    Route::get('kpi/{id}/detail', [KpiController::class, 'show'])->name('kpi.show');

    // KPI Target Routes
    Route::resource('kpi-target', KpiTargetController::class)->names('kpi-target')->except('show');
    Route::get('kpi-target/{id}/detail', [KpiTargetController::class, 'show'])->name('kpi-target.show');

    // KPI Realisasi Routes
    Route::resource('kpi-realisasi', KpiRealisasiController::class)->names('kpi-realisasi')->except('show');
    Route::get('kpi-realisasi/{id}/detail', [KpiRealisasiController::class, 'show'])->name('kpi-realisasi.show');
    Route::post('kpi-realisasi/{id}/approve', [KpiRealisasiController::class, 'approve'])->name('kpi-realisasi.approve');
    Route::post('kpi-realisasi/{id}/reject', [KpiRealisasiController::class, 'reject'])->name('kpi-realisasi.reject');

    // PKWT routes
    Route::resource('pkwt', PkwtController::class)->names('pkwt')->except('show');
    Route::get('pkwt/{id}/detail', [PkwtController::class, 'show'])->name('pkwt.show');
    Route::get('pkwt/{id}/download', [PkwtController::class, 'download'])->name('pkwt.download');

    // Master Training Routes
    Route::resource('master-training', MasterTrainingController::class)->names('master-training')->except('show');
    Route::get('master-training/{id}/detail', [MasterTrainingController::class, 'show'])->name('master-training.show');

    // Meeting Routes
    Route::resource('meeting', MeetingController::class)->names('meeting')->except('show');
    Route::get('meeting/{id}/detail', [MeetingController::class, 'show'])->name('meeting.show');
});

Route::group(['prefix' => '/', 'middleware' => ['auth']], function () {
    Route::get('karyawan/get-data', [KaryawanController::class, 'get_data'])->name('karyawan.get-data');
    Route::get('kandidat/get-data', [KandidatController::class, 'get_data'])->name('kandidat.get-data');
    Route::get('absensi/get-data', [AbsensiController::class, 'get_data'])->name('absensi.get-data');
    Route::get('employee-activities/get-data', [EmployeeActivityController::class, 'get_data'])->name('employee-activities.get-data');
    Route::get('daily-report/get-data', [DailyReportController::class, 'get_data'])->name('daily-report.get-data');
    Route::get('daily-report/get-role-data', [DailyReportController::class, 'getRoleData'])->name('daily-report.get-role-data');
    Route::get('daily-report/get-summary-data', [DailyReportController::class, 'getSummaryData'])->name('daily-report.get-summary-data');
    Route::get('daily-report/get-statistics', [DailyReportController::class, 'getStatistics'])->name('daily-report.get-statistics');
    Route::get('daily-report/{id}/armada-location-data', [DailyReportController::class, 'getArmadaLocationData'])->name('daily-report.armada-location-data');
    Route::get('daily-report/get-do-select2', [DailyReportController::class, 'getDoForSelect2'])->name('daily-report.get-do-select2');
    Route::get('ads/get-data', [AdsController::class, 'get_data'])->name('ads.get-data');
    Route::get('training/get-data', [TrainingController::class, 'get_data'])->name('training.get-data');
    Route::get('handover/get-data', [HandoverRecordController::class, 'get_data'])->name('handover.get-data');
    Route::get('perusahaan/get-data', [PerusahaanController::class, 'get_data'])->name('perusahaan.get-data');
    Route::get('laporan-absensi/get-data', [AbsensiController::class, 'get_data_laporan_absensi'])->name('laporan-absensi.get-data');
    Route::get('karyawan/get-doc', [KaryawanController::class, 'get_doc'])->name('karyawan.get-doc');

    // Izin AJAX routes
    Route::get('izin/get-data', [IzinController::class, 'getData'])->name('izin.get-data');

    // Piutang Karyawan AJAX routes
    Route::get('piutang-karyawan/get-data', [PiutangKaryawanController::class, 'getData'])->name('piutang-karyawan.get-data');

    // Jabatan AJAX routes
    Route::get('jabatan/get-data', [JabatanController::class, 'getData'])->name('jabatan.get-data');

    // SOP AJAX routes
    Route::get('sop/get-data', [SopController::class, 'getData'])->name('sop.get-data');

    // Job Description AJAX routes
    Route::get('job-description/get-data', [JobDescriptionController::class, 'getData'])->name('job-description.get-data');

    // KPI AJAX routes
    Route::get('kpi/get-data', [KpiController::class, 'getData'])->name('kpi.get-data');

    // KPI Target AJAX routes
    Route::get('kpi-target/get-data', [KpiTargetController::class, 'getData'])->name('kpi-target.get-data');

    // KPI Realisasi AJAX routes
    Route::get('kpi-realisasi/get-data', [KpiRealisasiController::class, 'getData'])->name('kpi-realisasi.get-data');

    Route::get('ajax/get-sopir', [KaryawanController::class, 'get_sopir'])->name('ajax.get-sopir');
    Route::get('ajax/get-marketing', [KaryawanController::class, 'get_marketing'])->name('ajax.get-marketing');
    Route::get('ajax/get-karyawan', [KaryawanController::class, 'get_karyawan'])->name('ajax.get-karyawan');

    // PKWT AJAX routes
    Route::get('pkwt/get-data', [PkwtController::class, 'getData'])->name('pkwt.get-data');
    Route::get('pkwt/expiring-contracts', [PkwtController::class, 'getExpiringContracts'])->name('pkwt.expiring-contracts');

    // Master Training AJAX routes
    Route::get('master-training/get-data', [MasterTrainingController::class, 'getData'])->name('master-training.get-data');

    // Meeting AJAX routes
    Route::get('meeting/get-data', [MeetingController::class, 'getData'])->name('meeting.get-data');
});

Route::get('recruitment', [KandidatController::class, 'public_create'])->name('recruitment.create');
Route::post('recruitment', [KandidatController::class, 'public_store'])->name('recruitment.store');
