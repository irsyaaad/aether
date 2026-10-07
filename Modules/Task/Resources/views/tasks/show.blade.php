@extends('layouts.app')

@section('title', 'Detail Tugas: ' . $task->task_number . ' - ' . config('app.name'))
@section('page_title', 'Detail Tugas: ' . $task->task_number)

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single {
        border-color: #d1d5db !important;
        border-radius: 0.5rem !important;
        height: 38px !important;
        padding: 0.35rem 0.5rem !important;
        font-size: 0.75rem !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        padding-left: 0 !important;
        color: #1f2937 !important;
        font-size: 0.75rem !important;
        line-height: 1.5 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 6px !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--single,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #e11d48 !important;
        outline: none !important;
        box-shadow: 0 0 0 1px #e11d48 !important;
    }
    .select2-dropdown {
        border-color: #e5e7eb !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        font-size: 0.75rem !important;
        z-index: 99999 !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #e11d48 !important;
        color: #ffffff !important;
    }

    /* DataTables data-entries-table custom styles */
    #data-entries-table_wrapper {
        width: 100% !important;
    }
    #data-entries-table_wrapper .dt-layout-row:first-child,
    #data-entries-table_wrapper .dt-search,
    #data-entries-table_wrapper .dt-length,
    #data-entries-table_filter,
    #data-entries-table_length {
        display: none !important;
    }
    #data-entries-table_wrapper .dt-layout-table {
        width: 100% !important;
        display: block !important;
        overflow-x: auto !important;
    }
    #data-entries-table_wrapper .dt-paging,
    #data-entries-table_wrapper .dataTables_paginate {
        display: flex !important;
        align-items: center !important;
        gap: 0.25rem !important;
    }
    #data-entries-table_wrapper .dt-paging .dt-paging-button,
    #data-entries-table_wrapper .paginate_button {
        padding: 0.25rem 0.625rem !important;
        font-size: 0.75rem !important;
        border-radius: 0.375rem !important;
        border: 1px solid #e5e7eb !important;
        background-color: #ffffff !important;
        color: #374151 !important;
        cursor: pointer !important;
        transition: all 0.15s ease-in-out !important;
    }
    #data-entries-table_wrapper .dt-paging .dt-paging-button.current,
    #data-entries-table_wrapper .paginate_button.current {
        background-color: #e11d48 !important;
        border-color: #e11d48 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    #data-entries-table_wrapper .dt-paging .dt-paging-button:hover:not(.current):not(.disabled),
    #data-entries-table_wrapper .paginate_button:hover:not(.current):not(.disabled) {
        background-color: #f3f4f6 !important;
        color: #111827 !important;
    }
    #data-entries-table_wrapper .dt-paging .dt-paging-button.disabled,
    #data-entries-table_wrapper .paginate_button.disabled {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
    }
</style>
@endpush

@section('toolbar_actions')
<div class="flex items-center gap-2">
    <a href="{{ route('tasks.edit', $task->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm">
        <i data-lucide="edit-3" class="w-4 h-4"></i>
        Edit Tugas
    </a>
    <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
        <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
        Daftar Tugas
    </a>
</div>
@endsection

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">

    <!-- Overdue Alert Banner if overdue -->
    @if($task->is_overdue)
    <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-red-100 text-red-600 rounded-lg shrink-0">
                <i data-lucide="alert-triangle" class="h-5 w-5"></i>
            </div>
            <div>
                <h4 class="font-bold text-red-900">Tugas Terlambat (Overdue)!</h4>
                <p class="text-xs text-red-700 mt-0.5">
                    Batas waktu penyelesaian tugas ini telah terlewati pada <strong>{{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}</strong> ({{ $task->due_date ? $task->due_date->diffForHumans() : '' }}).
                </p>
            </div>
        </div>
    </div>
    @endif

    <!-- Task Header Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-gray-100">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <span class="font-mono text-sm font-bold text-primary px-2 py-0.5 bg-primary/10 rounded">
                        {{ $task->task_number }}
                    </span>
                    <span class="text-xs text-gray-500 flex items-center gap-1">
                        <i data-lucide="building-2" class="w-3.5 h-3.5 text-gray-400"></i>
                        {{ $task->company->name ?? '-' }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $task->task_type_badge_class }}">
                        {{ $task->task_type_label }}
                    </span>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $task->output_type_badge_class }}">
                        {{ $task->output_type_label }}
                    </span>
                    @if($task->category)
                    <span class="text-xs text-gray-600 bg-gray-100 px-2 py-0.5 rounded flex items-center gap-1">
                        <i data-lucide="tag" class="w-3 h-3 text-gray-400"></i>
                        {{ $task->category->name }}
                    </span>
                    @endif
                </div>
                <h2 class="text-xl font-bold text-gray-900 mt-1">{{ $task->title }}</h2>
            </div>

            <!-- Status & Action Quick Controls -->
            <div class="flex items-center gap-3 self-start md:self-auto">
                <div class="flex flex-col items-end">
                    <span class="text-[11px] text-gray-400 uppercase tracking-wider font-semibold mb-1">Status Saat Ini</span>
                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $task->status_badge_class }}">
                        {{ $task->status_label }}
                    </span>
                </div>
                @if($task->priority)
                <div class="flex flex-col items-end">
                    <span class="text-[11px] text-gray-400 uppercase tracking-wider font-semibold mb-1">Prioritas</span>
                    <span class="inline-flex items-center px-3 py-1 rounded text-xs font-semibold border {{ $task->priority->badge_class }}">
                        {{ $task->priority->name }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        <!-- Description -->
        <div class="pt-2">
            <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Deskripsi Tugas</h4>
            <div class="text-sm text-gray-700 bg-gray-50/50 p-4 rounded-lg border border-gray-100 whitespace-pre-line leading-relaxed">
                {{ $task->description ?: 'Tidak ada deskripsi rinci untuk tugas ini.' }}
            </div>
        </div>

        <!-- Meta Information Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-3 border-t border-gray-100 text-xs">
            <div>
                <span class="text-gray-400 block text-[11px]">Tanggal Mulai</span>
                <span class="font-medium text-gray-800 mt-0.5 block">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 inline mr-1 text-gray-400"></i>
                    {{ $task->start_date ? $task->start_date->format('d M Y') : '-' }}
                </span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px]">Tenggat Waktu</span>
                <span class="font-medium {{ $task->is_overdue ? 'text-red-600 font-bold' : 'text-gray-800' }} mt-0.5 block">
                    <i data-lucide="clock" class="w-3.5 h-3.5 inline mr-1 {{ $task->is_overdue ? 'text-red-500' : 'text-gray-400' }}"></i>
                    {{ $task->due_date ? $task->due_date->format('d M Y') : '-' }}
                </span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px]">Diselesaikan Pada</span>
                <span class="font-medium text-gray-800 mt-0.5 block">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 inline mr-1 text-gray-400"></i>
                    {{ $task->completed_at ? $task->completed_at->format('d M Y H:i') : '-' }}
                </span>
            </div>
            <div>
                <span class="text-gray-400 block text-[11px]">Dibuat Oleh</span>
                <span class="font-medium text-gray-800 mt-0.5 block">
                    <i data-lucide="user" class="w-3.5 h-3.5 inline mr-1 text-gray-400"></i>
                    {{ $task->creator->nm_user ?? 'System' }}
                </span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- REKAP HASIL PENGERJAAN KARYAWAN (TASK SUBMISSIONS - DATATABLE SERVERSIDE) -->
    <!-- ========================================================================= -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-5 bg-gradient-to-r from-slate-50 to-gray-50 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="users" class="w-4 h-4 text-primary"></i>
                    Rekap Hasil Pengerjaan Karyawan
                </span>
                <h3 class="text-base font-bold text-gray-900 mt-0.5">Status & Bukti Pengerjaan Setiap Karyawan</h3>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold px-3 py-1 bg-white border border-gray-200 rounded-lg text-gray-600 shadow-2xs">
                    <strong class="text-gray-900" id="recap-employees-count">{{ $totalEmployeesCount ?? 0 }}</strong> Karyawan
                </span>
                <span class="text-xs font-semibold px-3 py-1 bg-white border border-gray-200 rounded-lg text-gray-600 shadow-2xs">
                    Total <strong class="text-gray-900" id="recap-plotings-count">{{ $totalPlotingsCount ?? 0 }}</strong> Ploting Penugasan
                </span>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="p-4 border-b border-gray-100 bg-white flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="relative flex-1 sm:max-w-xs">
                <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="recap-search" placeholder="Cari nama karyawan, NIK, divisi..." class="w-full text-xs pl-9 pr-8 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                <button type="button" id="btn-clear-recap-search" class="hidden absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600" title="Bersihkan">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
            <div class="flex items-center gap-2 justify-between sm:justify-end">
                <div class="flex items-center gap-1.5 text-xs text-gray-500">
                    <span>Tampilkan:</span>
                    <select id="recap-length" class="text-xs py-1.5 px-2.5 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary bg-white">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <button type="button" onclick="reloadEmployeeRecap()" class="p-2 text-gray-500 hover:text-primary hover:bg-purple-50 rounded-lg transition-colors border border-gray-200" title="Muat Ulang Data">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table id="employee-recap-table" class="w-full text-xs text-left">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Nama Karyawan</th>
                        <th class="py-3 px-4 text-center w-36">Ploting Tugas</th>
                        <th class="py-3 px-4 w-48">Persentase Selesai</th>
                        <th class="py-3 px-4 text-center w-48">Total Baris Data Diinput</th>
                        <th class="py-3 px-4 text-center w-28">Status</th>
                        <th class="py-3 px-4 text-center w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                </tbody>
            </table>
        </div>
    </div>

    @if($task->output_type === 'target')
    <!-- Target Execution Panel -->
    <div class="bg-white rounded-xl border border-amber-200/80 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
            <div>
                <span class="text-xs font-bold text-amber-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="target" class="w-4 h-4"></i>
                    Target & Capaian Kinerja
                </span>
                <h3 class="text-base font-bold text-gray-900 mt-1">Realisasi Target Tugas</h3>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <span class="text-[11px] text-gray-400 block font-medium">Target Ditetapkan</span>
                    <span class="text-base font-bold text-gray-900">{{ $task->formatted_target }}</span>
                </div>
                <div class="text-right">
                    <span class="text-[11px] text-amber-600 block font-medium">Realisasi Saat Ini</span>
                    <span class="text-base font-bold text-amber-700" id="current-achieved-display">{{ $task->formatted_achieved }}</span>
                </div>
            </div>
        </div>

        <!-- Progress Bar -->
        <div class="space-y-1.5">
            <div class="flex justify-between text-xs font-semibold">
                <span class="text-gray-600">Persentase Pencapaian Target</span>
                <span class="text-primary font-bold" id="target-percent-display">{{ $task->progress }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
                <div id="target-progress-bar" class="bg-gradient-to-r from-amber-500 to-primary h-3 rounded-full transition-all duration-300" style="width: {{ min(100, $task->progress) }}%"></div>
            </div>
        </div>

        <!-- Update Target Form -->
        <form id="form-update-target" onsubmit="submitUpdateTarget(event)" class="pt-2 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-700 mb-1">
                    Input / Perbarui Nilai Realisasi Capaian:
                </label>
                <div class="relative">
                    <input type="number" step="any" id="input-achieved-amount" value="{{ $task->achieved_amount }}" required placeholder="Masukkan angka capaian..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                </div>
            </div>
            <button type="submit" id="btn-save-target" class="sm:self-end px-5 py-2 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm flex items-center justify-center gap-1.5 shrink-0">
                <i data-lucide="check" class="w-4 h-4"></i>
                Simpan Capaian Target
            </button>
        </form>
    </div>
    @endif

    @if($task->output_type === 'data_list')
    <!-- Data List Collection Panel -->
    <div class="bg-white rounded-xl border border-blue-200/80 shadow-sm p-6 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-gray-100">
            <div>
                <span class="text-xs font-bold text-blue-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="table" class="w-4 h-4"></i>
                    Daftar Entri Data / Jawaban
                </span>
                <p class="text-xs text-gray-500 mt-0.5">
                    Karyawan mengumpulkan dan menginput baris data sesuai kolom yang telah ditentukan.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold px-3 py-1 bg-blue-50 text-blue-700 rounded-lg border border-blue-200">
                    <span id="entries-count">{{ $task->dataEntries->count() }}</span>
                    @if($task->target_count) / {{ $task->target_count }} @endif Baris Data
                </span>
                <button type="button" onclick="openAddEntryModal()" class="px-3.5 py-1.5 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    Tambah Baris Data
                </button>
            </div>
        </div>

        <!-- Toolbar: Custom Search & Page Length Controls -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-1">
            <div class="relative w-full sm:w-80">
                <input type="text" id="custom-entries-search" placeholder="Cari data jawaban / penginput..." class="w-full pl-9 pr-8 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary shadow-2xs bg-white text-gray-800 transition-all">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                <button type="button" id="btn-clear-entries-search" class="hidden absolute right-2.5 top-2.5 text-gray-400 hover:text-gray-600 transition-colors" title="Hapus Pencarian">
                    <i data-lucide="x" class="w-3.5 h-3.5"></i>
                </button>
            </div>
            <div class="flex items-center gap-2 self-end sm:self-auto">
                <span class="text-xs text-gray-500 font-medium">Tampilkan:</span>
                <select id="custom-entries-length" class="text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary text-gray-700 bg-white shadow-2xs cursor-pointer">
                    <option value="10">10 baris</option>
                    <option value="25">25 baris</option>
                    <option value="50">50 baris</option>
                    <option value="100">100 baris</option>
                </select>
            </div>
        </div>

        <!-- Entries Table -->
        <div class="overflow-x-auto rounded-lg border border-gray-200">
            <table class="w-full text-left text-xs" id="data-entries-table">
                <thead>
                    <tr class="bg-gray-50 text-gray-700 font-semibold uppercase text-[11px] border-b border-gray-200">
                        <th class="py-2.5 px-3 w-10 text-center">#</th>
                        @php $columns = $task->list_columns ?: ['Nama Afiliator', 'Kontak WhatsApp', 'Catatan']; @endphp
                        @foreach($columns as $col)
                            <th class="py-2.5 px-3">{{ $col }}</th>
                        @endforeach
                        <th class="py-2.5 px-3">Diinput Oleh</th>
                        <th class="py-2.5 px-3">Waktu</th>
                        <th class="py-2.5 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="data-entries-tbody" class="divide-y divide-gray-100">
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Baris Data -->
    <div id="add-entry-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs hidden p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full overflow-hidden border border-gray-200">
            <div class="flex items-center justify-between p-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-primary"></i>
                    Tambah Baris Data Jawaban
                </h3>
                <button type="button" onclick="closeAddEntryModal()" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="form-add-entry" onsubmit="submitAddEntry(event)" class="p-5 space-y-3">
                @foreach($columns as $col)
                @php
                    $isTokoCol = (bool) preg_match('/toko|store|cabang|marketplace|channel/i', $col);
                @endphp
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">{{ $col }} <span class="text-red-500">*</span></label>
                    @if($isTokoCol)
                        <select name="entry_data[{{ $col }}]" required class="w-full text-xs select2-toko-ajax" data-placeholder="-- Cari dan Pilih Toko / Marketplace --" style="width: 100%;">
                            <option value="">-- Cari dan Pilih Toko / Marketplace --</option>
                        </select>
                        <span class="text-[10px] text-gray-400 mt-1 block flex items-center gap-1">
                            <i data-lucide="info" class="w-3 h-3 text-blue-500 inline"></i>
                            Pilih toko dari master database atau ketik nama toko/channel.
                        </span>
                    @else
                        <input type="text" name="entry_data[{{ $col }}]" required placeholder="Masukkan {{ $col }}..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    @endif
                </div>
                @endforeach
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="closeAddEntryModal()" class="px-3.5 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="btn-submit-entry" class="px-4 py-2 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($task->output_type === 'stock_update')
    <!-- Stock Update Execution Panel -->
    <div class="bg-white rounded-xl border border-rose-200/90 shadow-sm overflow-hidden space-y-0">
        <div class="p-5 bg-gradient-to-r from-rose-50/70 to-pink-50/50 border-b border-rose-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-xs font-bold text-rose-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="package-x" class="w-4 h-4"></i>
                    Pembaruan Status Stok Toko & Marketplace
                </span>
                <h3 class="text-base font-bold text-gray-900 mt-0.5">Daftar Log Update Stok Kosong</h3>
            </div>
            <button type="button" onclick="$('#add-stock-modal').removeClass('hidden')" class="px-3.5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-lg shadow-xs flex items-center gap-1.5 transition-colors self-start sm:self-auto">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Catat Update Stok
            </button>
        </div>

        <!-- Scope Badges: Toko & Produk yang Ditugaskan -->
        <div class="p-4 bg-gray-50/50 border-b border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <span class="text-[11px] font-semibold text-gray-500 block mb-1">Toko yang Ditugaskan:</span>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($availableTokos as $toko)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-white border border-gray-200 text-gray-700">
                            <i data-lucide="store" class="w-3 h-3 text-primary"></i> {{ $toko->name }}
                        </span>
                    @empty
                        <span class="text-xs text-gray-400 italic">Semua Toko Perusahaan</span>
                    @endforelse
                </div>
            </div>
            <div>
                <span class="text-[11px] font-semibold text-gray-500 block mb-1">Produk yang Ditugaskan:</span>
                <div class="flex flex-wrap gap-1.5">
                    @forelse($availableProduks as $prod)
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[11px] font-medium bg-white border border-gray-200 text-gray-700">
                            <i data-lucide="package" class="w-3 h-3 text-rose-600"></i> {{ $prod->name }}
                        </span>
                    @empty
                        <span class="text-xs text-gray-400 italic">Semua Produk Perusahaan</span>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Table of Log Updates -->
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left" id="stock-updates-table">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200">
                    <tr>
                        <th class="py-3 px-4 w-12 text-center">#</th>
                        <th class="py-3 px-4">Toko / Marketplace</th>
                        <th class="py-3 px-4">Produk</th>
                        <th class="py-3 px-4">Status Stok</th>
                        <th class="py-3 px-4 text-center">Sisa Stok</th>
                        <th class="py-3 px-4">Catatan / Bukti</th>
                        <th class="py-3 px-4">Diupdate Oleh</th>
                        <th class="py-3 px-4">Waktu Update</th>
                        <th class="py-3 px-4 text-center w-16">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($task->stockUpdates as $index => $log)
                    <tr id="stock-row-{{ $log->id }}" class="hover:bg-gray-50/50">
                        <td class="py-2.5 px-4 text-center font-medium text-gray-500">{{ $index + 1 }}</td>
                        <td class="py-2.5 px-4 font-semibold text-gray-800">
                            {{ $log->toko->name ?? '-' }}
                        </td>
                        <td class="py-2.5 px-4 font-medium text-gray-800">
                            {{ $log->produk->name ?? '-' }}
                        </td>
                        <td class="py-2.5 px-4">
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $log->stock_status_badge_class }}">
                                {{ $log->stock_status_label }}
                            </span>
                        </td>
                        <td class="py-2.5 px-4 text-center font-mono font-bold text-gray-700">
                            {{ $log->current_stock }}
                        </td>
                        <td class="py-2.5 px-4 text-gray-600">
                            {{ $log->notes ?: '-' }}
                        </td>
                        <td class="py-2.5 px-4 text-gray-600">
                            {{ $log->employee->display_name ?? ($log->creator->nm_user ?? 'Karyawan') }}
                        </td>
                        <td class="py-2.5 px-4 text-gray-500 font-mono text-[11px]">
                            {{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '-' }}
                        </td>
                        <td class="py-2.5 px-4 text-center">
                            <button type="button" onclick="deleteStockUpdate({{ $log->id }})" class="p-1 text-gray-400 hover:text-red-600 transition-colors rounded">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr id="empty-stock-row">
                        <td colspan="9" class="py-8 text-center text-gray-400 italic">
                            Belum ada catatan update stok kosong. Klik tombol "Catat Update Stok" di atas untuk menambahkan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Catat Update Stok -->
    <div id="add-stock-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-xs hidden p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full overflow-hidden border border-gray-200">
            <div class="flex items-center justify-between p-4 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-900 flex items-center gap-1.5">
                    <i data-lucide="package-x" class="w-4 h-4 text-rose-600"></i>
                    Catat Update Stok Produk
                </h3>
                <button type="button" onclick="$('#add-stock-modal').addClass('hidden')" class="p-1 text-gray-400 hover:text-gray-600 rounded-lg">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>
            <form id="form-add-stock" onsubmit="submitStockUpdate(event)" class="p-5 space-y-3.5">
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Toko / Marketplace <span class="text-red-500">*</span></label>
                    <select name="toko_id" required class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                        <option value="">-- Pilih Toko --</option>
                        @foreach($availableTokos as $toko)
                            <option value="{{ $toko->id }}">{{ $toko->name }} ({{ $toko->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Produk <span class="text-red-500">*</span></label>
                    <select name="produk_id" required class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                        <option value="">-- Pilih Produk --</option>
                        @foreach($availableProduks as $prod)
                            <option value="{{ $prod->id }}">{{ $prod->name }} ({{ $prod->code }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Status Stok <span class="text-red-500">*</span></label>
                        <select name="stock_status" required class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                            <option value="kosong">Stok Kosong</option>
                            <option value="menipis">Stok Menipis</option>
                            <option value="tersedia">Stok Tersedia / Sudah Diisi</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Sisa Stok Aktual</label>
                        <input type="number" name="current_stock" value="0" min="0" required class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1">Catatan / Bukti Pengerjaan</label>
                    <textarea name="notes" rows="2" placeholder="Contoh: Stok di shopee sudah dinolkan, supplier sedang mengirim 50 pcs..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary"></textarea>
                </div>
                <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-100">
                    <button type="button" onclick="$('#add-stock-modal').addClass('hidden')" class="px-3.5 py-2 text-xs font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                        Batal
                    </button>
                    <button type="submit" id="btn-submit-stock" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-lg transition-colors shadow-sm flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        Simpan Update Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @if($task->output_type === 'full_custom')
    <!-- Full Custom Execution Panel -->
    <div class="bg-white rounded-xl border border-purple-200/90 shadow-sm overflow-hidden space-y-0">
        <div class="p-5 bg-gradient-to-r from-purple-50/80 to-indigo-50/50 border-b border-purple-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-xs font-bold text-purple-700 uppercase tracking-wider flex items-center gap-1.5">
                    <i data-lucide="sliders-horizontal" class="w-4 h-4"></i>
                    Skema Form Input Full Custom
                </span>
                <h3 class="text-base font-bold text-gray-900 mt-0.5">Daftar Field Kustom yang Ditugaskan ke Karyawan</h3>
            </div>
            <span class="text-xs font-semibold px-3 py-1 bg-purple-100 text-purple-800 rounded-lg self-start sm:self-auto">
                {{ count($task->custom_fields ?? []) }} Field Dikonfigurasi
            </span>
        </div>

        <div class="p-5">
            <div class="overflow-x-auto border border-gray-200 rounded-xl">
                <table class="w-full text-xs text-left">
                    <thead class="bg-gray-50 text-gray-700 font-semibold border-b border-gray-200">
                        <tr>
                            <th class="py-3 px-4 w-12 text-center">#</th>
                            <th class="py-3 px-4">Nama Field / Label</th>
                            <th class="py-3 px-4">Jenis Field</th>
                            <th class="py-3 px-4">Status Pengisian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($task->custom_fields ?? [] as $fIdx => $field)
                            @php
                                $typeLabels = [
                                    'text'     => ['label' => 'Teks Singkat (Text)', 'class' => 'bg-blue-50 text-blue-700 border-blue-200'],
                                    'number'   => ['label' => 'Angka / Nominal (Number)', 'class' => 'bg-amber-50 text-amber-700 border-amber-200'],
                                    'date'     => ['label' => 'Tanggal (Date)', 'class' => 'bg-emerald-50 text-emerald-700 border-emerald-200'],
                                    'time'     => ['label' => 'Waktu / Jam (Time)', 'class' => 'bg-teal-50 text-teal-700 border-teal-200'],
                                    'datetime' => ['label' => 'Tanggal & Waktu (DateTime)', 'class' => 'bg-indigo-50 text-indigo-700 border-indigo-200'],
                                    'textarea' => ['label' => 'Teks Panjang (Textarea)', 'class' => 'bg-slate-50 text-slate-700 border-slate-200'],
                                    'file'     => ['label' => 'File / Dokumen / Gambar (File)', 'class' => 'bg-purple-50 text-purple-700 border-purple-200'],
                                    'url'      => ['label' => 'Link / Tautan Web (URL)', 'class' => 'bg-sky-50 text-sky-700 border-sky-200'],
                                    'email'    => ['label' => 'Alamat Email (Email)', 'class' => 'bg-rose-50 text-rose-700 border-rose-200'],
                                ];
                                $fType = $field['type'] ?? 'text';
                                $fMeta = $typeLabels[$fType] ?? ['label' => ucfirst($fType), 'class' => 'bg-gray-50 text-gray-700 border-gray-200'];
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition-colors">
                                <td class="py-3 px-4 text-center font-medium text-gray-400">{{ $fIdx + 1 }}</td>
                                <td class="py-3 px-4 font-semibold text-gray-900">
                                    {{ $field['label'] ?? '-' }}
                                    <span class="block text-[10px] text-gray-400 font-mono mt-0.5">key: {{ $field['key'] ?? '-' }}</span>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border {{ $fMeta['class'] }}">
                                        {{ $fMeta['label'] }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    @if(!empty($field['required']))
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            <i data-lucide="alert-circle" class="w-3 h-3"></i> Wajib Diisi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-medium bg-gray-100 text-gray-600">
                                            Opsional
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-6 text-center text-gray-400 italic">
                                    Tidak ada field kustom yang dikonfigurasi.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Assignees & Followers Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <!-- Assignees Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="user-check" class="w-4 h-4 text-blue-600"></i>
                    Penerima Tugas (Assignees)
                </h3>
                <span class="text-xs font-semibold px-2 py-0.5 bg-blue-50 text-blue-700 rounded-full">
                    {{ $task->assignedEmployees->count() }} Orang
                </span>
            </div>

            <div class="space-y-3 max-h-72 overflow-y-auto">
                @forelse($task->assignedEmployees as $emp)
                <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        @if($emp->avatar_url)
                            <img src="{{ $emp->avatar_url }}" alt="{{ $emp->display_name }}" class="w-9 h-9 rounded-full object-cover border border-gray-200 shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($emp->display_name, 0, 2)) }}
                            </div>
                        @endif
                        <div class="flex flex-col">
                            <span class="text-xs font-semibold text-gray-900">{{ $emp->display_name }}</span>
                            <span class="text-[11px] text-gray-500">
                                {{ $emp->position->name ?? $emp->jabatan ?? '-' }}
                                @if($emp->department) • {{ $emp->department->name }} @endif
                            </span>
                        </div>
                    </div>
                    <span class="text-[10px] text-gray-400 font-mono">
                        {{ $emp->pivot->assigned_at ? \Carbon\Carbon::parse($emp->pivot->assigned_at)->format('d M') : '' }}
                    </span>
                </div>
                @empty
                <div class="text-center py-6 text-xs text-gray-400 italic">
                    Belum ada karyawan yang ditugaskan.
                </div>
                @endforelse
            </div>
        </div>

        <!-- Followers Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
            <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                    <i data-lucide="eye" class="w-4 h-4 text-purple-600"></i>
                    Pemantau (Followers)
                </h3>
                <span class="text-xs font-semibold px-2 py-0.5 bg-purple-50 text-purple-700 rounded-full">
                    {{ $task->followerEmployees->count() }} Orang
                </span>
            </div>

            <div class="space-y-3 max-h-72 overflow-y-auto">
                @forelse($task->followerEmployees as $emp)
                <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        @if($emp->avatar_url)
                            <img src="{{ $emp->avatar_url }}" alt="{{ $emp->display_name }}" class="w-9 h-9 rounded-full object-cover border border-gray-200 shrink-0">
                        @else
                            <div class="w-9 h-9 rounded-full bg-purple-100 text-purple-700 font-bold text-xs flex items-center justify-center shrink-0">
                                {{ strtoupper(substr($emp->display_name, 0, 2)) }}
                            </div>
                        @endif
                        <div class="flex flex-col">
                            <span class="text-xs font-semibold text-gray-900">{{ $emp->display_name }}</span>
                            <span class="text-[11px] text-gray-500">
                                {{ $emp->position->name ?? $emp->jabatan ?? '-' }}
                                @if($emp->department) • {{ $emp->department->name }} @endif
                            </span>
                        </div>
                    </div>
                    <span class="text-[10px] text-gray-400 font-mono">
                        {{ $emp->pivot->followed_at ? \Carbon\Carbon::parse($emp->pivot->followed_at)->format('d M') : '' }}
                    </span>
                </div>
                @empty
                <div class="text-center py-6 text-xs text-gray-400 italic">
                    Belum ada pemantau untuk tugas ini.
                </div>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    // Initialize Toko Select2 Ajax
    function initTokoSelect2() {
        if (typeof $ !== 'undefined' && $.fn.select2) {
            $('.select2-toko-ajax').each(function() {
                var $el = $(this);
                if (!$el.hasClass('select2-hidden-accessible')) {
                    $el.select2({
                        dropdownParent: $('#add-entry-modal'),
                        width: '100%',
                        placeholder: $el.data('placeholder') || '-- Cari & Pilih Toko / Marketplace --',
                        allowClear: true,
                        tags: true,
                        ajax: {
                            url: "{{ route('tasks.tokos-select2') }}",
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    q: params.term,
                                    task_id: "{{ $task->id }}"
                                };
                            },
                            processResults: function(data) {
                                return {
                                    results: data.results || []
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 0
                    });
                }
            });
        }
    }

    function openAddEntryModal() {
        $('#add-entry-modal').removeClass('hidden');
        setTimeout(function() {
            initTokoSelect2();
        }, 50);
    }

    function closeAddEntryModal() {
        $('#add-entry-modal').addClass('hidden');
    }

    let dataEntriesTable;
    let employeeRecapTable;

    function reloadEmployeeRecap() {
        if (employeeRecapTable) {
            employeeRecapTable.ajax.reload(null, false);
        }
    }

    $(document).ready(function() {
        initTokoSelect2();

        // =========================================================================
        // REKAP HASIL PENGERJAAN KARYAWAN (DATATABLE SERVERSIDE)
        // =========================================================================
        if ($('#employee-recap-table').length) {
            employeeRecapTable = $('#employee-recap-table').DataTable({
                processing: true,
                serverSide: true,
                dom: 'rt<"flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border-t border-gray-100 text-xs text-gray-500"ip>',
                ajax: {
                    url: "{{ route('tasks.employee-recap.get-data', $task->id) }}"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-mono py-3.5 px-4 w-12' },
                    { data: 'employee_info', name: 'name', orderable: true, searchable: true, className: 'py-3.5 px-4' },
                    { data: 'ploting_count', name: 'ploting_count', orderable: false, searchable: false, className: 'text-center py-3.5 px-4 w-36' },
                    { data: 'progress_bar', name: 'progress_bar', orderable: false, searchable: false, className: 'py-3.5 px-4 w-48' },
                    { data: 'total_data_rows', name: 'total_data_rows', orderable: false, searchable: false, className: 'text-center py-3.5 px-4 w-48' },
                    { data: 'status_badge', name: 'status_badge', orderable: false, searchable: false, className: 'text-center py-3.5 px-4 w-28' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center py-3.5 px-4 w-32' }
                ],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [[1, 'asc']],
                language: {
                    emptyTable: "Belum ada karyawan yang diploting pada tugas ini.",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ karyawan",
                    infoEmpty: "Menampilkan 0 karyawan",
                    infoFiltered: "(disaring dari _MAX_ total karyawan)",
                    lengthMenu: "Tampilkan _MENU_ data",
                    loadingRecords: "&nbsp;",
                    zeroRecords: "Tidak ada data karyawan yang cocok",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                },
                drawCallback: function(settings) {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            });

            // Connect custom search input with debounce & clear button
            let recapSearchTimer = null;
            $('#recap-search').on('keyup input', function() {
                const val = $(this).val();
                if (val.length > 0) {
                    $('#btn-clear-recap-search').removeClass('hidden');
                } else {
                    $('#btn-clear-recap-search').addClass('hidden');
                }
                clearTimeout(recapSearchTimer);
                recapSearchTimer = setTimeout(function() {
                    employeeRecapTable.search(val).draw();
                }, 300);
            });

            $('#btn-clear-recap-search').on('click', function() {
                $('#recap-search').val('').trigger('input').focus();
            });

            // Connect custom page length selector
            $('#recap-length').on('change', function() {
                employeeRecapTable.page.len(parseInt($(this).val(), 10)).draw();
            });
        }

        @if($task->output_type === 'data_list')
        @php
            $dtColumns = $task->list_columns ?: ['Nama Afiliator', 'Kontak WhatsApp', 'Catatan'];
        @endphp
        if ($('#data-entries-table').length) {
            dataEntriesTable = $('#data-entries-table').DataTable({
                processing: true,
                serverSide: true,
                dom: 'rt<"flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border-t border-gray-100 text-xs text-gray-500"ip>',
                ajax: {
                    url: "{{ route('tasks.data-entries.get-data', $task->id) }}"
                },
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-mono py-2.5 px-3' },
                    @foreach($dtColumns as $idx => $col)
                    { data: 'col_{{ $idx }}', name: 'col_{{ $idx }}', orderable: false, searchable: false, className: 'font-medium text-gray-900 py-2.5 px-3' },
                    @endforeach
                    { data: 'employee_name', name: 'employee_name', orderable: false, searchable: false, className: 'text-gray-600 py-2.5 px-3' },
                    { data: 'created_time', name: 'created_at', orderable: true, searchable: false, className: 'text-gray-400 font-mono text-[11px] py-2.5 px-3' },
                    { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right py-2.5 px-3' }
                ],
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
                order: [], // Pertahankan default server-side order
                language: {
                    emptyTable: "Belum ada baris data jawaban yang diinputkan. Klik tombol \"Tambah Baris Data\" di atas untuk menambahkan.",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ baris data",
                    infoEmpty: "Menampilkan 0 baris data",
                    infoFiltered: "(disaring dari _MAX_ total baris)",
                    lengthMenu: "Tampilkan _MENU_ baris",
                    loadingRecords: "&nbsp;",
                    zeroRecords: "Tidak ada data yang cocok dengan pencarian",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                },
                drawCallback: function(settings) {
                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            });

            // Connect custom search input with debounce & clear button
            let searchTimer = null;
            $('#custom-entries-search').on('keyup input', function() {
                const val = $(this).val();
                if (val.length > 0) {
                    $('#btn-clear-entries-search').removeClass('hidden');
                } else {
                    $('#btn-clear-entries-search').addClass('hidden');
                }
                clearTimeout(searchTimer);
                searchTimer = setTimeout(function() {
                    dataEntriesTable.search(val).draw();
                }, 300);
            });

            $('#btn-clear-entries-search').on('click', function() {
                $('#custom-entries-search').val('').trigger('input').focus();
            });

            // Connect custom page length selector
            $('#custom-entries-length').on('change', function() {
                dataEntriesTable.page.len(parseInt($(this).val(), 10)).draw();
            });
        }
        @endif
    });

    function submitUpdateTarget(e) {
        e.preventDefault();
        const btn = $('#btn-save-target');
        const amount = $('#input-achieved-amount').val();
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{ route('tasks.update-target', $task->id) }}",
            type: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                achieved_amount: amount
            },
            success: function(res) {
                btn.prop('disabled', false).html('<i data-lucide="check" class="w-4 h-4"></i> Simpan Capaian Target');
                if (res.status === 'success') {
                    $('#current-achieved-display').text(res.formatted_achieved);
                    $('#target-percent-display').text(res.progress + '%');
                    $('#target-progress-bar').css('width', Math.min(100, res.progress) + '%');
                    $('#progress-display').text(res.progress + '%');
                    $('#quick-progress-range').val(res.progress);
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: res.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        if (res.progress >= 100) location.reload();
                    });
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).html('<i data-lucide="check" class="w-4 h-4"></i> Simpan Capaian Target');
                Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
            }
        });
    }

    function submitAddEntry(e) {
        e.preventDefault();
        const form = $('#form-add-entry');
        const btn = $('#btn-submit-entry');
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{ route('tasks.data-entries.store', $task->id) }}",
            type: 'POST',
            data: form.serialize() + "&_token={{ csrf_token() }}",
            success: function(res) {
                btn.prop('disabled', false).text('Simpan Data');
                if (res.status === 'success') {
                    closeAddEntryModal();
                    form[0].reset();
                    $('.select2-toko-ajax').val(null).trigger('change');
                    if (typeof dataEntriesTable !== 'undefined' && dataEntriesTable) {
                        dataEntriesTable.ajax.reload(null, false);
                    }
                    if (res.total_count !== undefined) {
                        $('#entries-count').text(res.total_count);
                    }
                    Swal.fire({
                        icon: 'success',
                        title: 'Data Tersimpan!',
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Simpan Data');
                Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
            }
        });
    }

    function deleteEntry(entryId) {
        Swal.fire({
            title: 'Hapus baris data ini?',
            text: 'Data yang dihapus tidak dapat dikembalikan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('tasks/data-entries') }}/${entryId}`,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            if (typeof dataEntriesTable !== 'undefined' && dataEntriesTable) {
                                dataEntriesTable.ajax.reload(null, false);
                            } else {
                                $(`#entry-row-${entryId}`).fadeOut(300, function() {
                                    $(this).remove();
                                });
                            }
                            if (res.total_count !== undefined) {
                                $('#entries-count').text(res.total_count);
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 1000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

    function submitStockUpdate(e) {
        e.preventDefault();
        const form = $('#form-add-stock');
        const btn = $('#btn-submit-stock');
        btn.prop('disabled', true).text('Menyimpan...');

        $.ajax({
            url: "{{ route('tasks.stock-updates.store', $task->id) }}",
            type: 'POST',
            data: form.serialize() + "&_token={{ csrf_token() }}",
            success: function(res) {
                btn.prop('disabled', false).text('Simpan Update Stok');
                if (res.status === 'success') {
                    $('#add-stock-modal').addClass('hidden');
                    form[0].reset();
                    Swal.fire({
                        icon: 'success',
                        title: 'Update Stok Disimpan!',
                        text: res.message,
                        timer: 1200,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }
            },
            error: function(xhr) {
                btn.prop('disabled', false).text('Simpan Update Stok');
                Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
            }
        });
    }

    function deleteStockUpdate(id) {
        Swal.fire({
            title: 'Hapus Log Update Stok?',
            text: 'Data update stok ini akan dihapus permanen.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('tasks/stock-updates') }}/" + id,
                    type: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#stock-row-' + id).remove();
                            if ($('#stock-updates-table tbody tr').length === 0) {
                                $('#stock-updates-table tbody').html(`
                                    <tr id="empty-stock-row">
                                        <td colspan="9" class="py-8 text-center text-gray-400 italic">
                                            Belum ada catatan update stok kosong. Klik tombol "Catat Update Stok" di atas untuk menambahkan.
                                        </td>
                                    </tr>
                                `);
                            }
                            Swal.fire({
                                icon: 'success',
                                title: 'Terhapus!',
                                text: res.message,
                                timer: 1000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan.', 'error');
                    }
                });
            }
        });
    }

</script>
@endpush
