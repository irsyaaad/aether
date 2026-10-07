@extends('layouts.app')

@section('title', 'Dashboard Monitoring Riset & Akademik - ' . config('app.name'))
@section('page_title', 'Dashboard Monitoring Riset & Akademik')

@push('styles')
<style>
    #employee-performance-table_wrapper,
    #recent-submissions-table_wrapper {
        width: 100%;
    }
    #employee-performance-table_wrapper .dataTables_length,
    #employee-performance-table_wrapper .dt-length,
    #recent-submissions-table_wrapper .dataTables_length,
    #recent-submissions-table_wrapper .dt-length {
        font-size: 0.75rem;
        color: #4b5563;
        margin-bottom: 0.75rem;
        float: left;
    }
    #employee-performance-table_wrapper .dataTables_length select,
    #employee-performance-table_wrapper .dt-length select,
    #recent-submissions-table_wrapper .dataTables_length select,
    #recent-submissions-table_wrapper .dt-length select {
        padding: 0.25rem 1.75rem 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
        background-color: #fff;
    }
    #employee-performance-table_wrapper .dataTables_filter,
    #employee-performance-table_wrapper .dt-search,
    #recent-submissions-table_wrapper .dataTables_filter,
    #recent-submissions-table_wrapper .dt-search {
        font-size: 0.75rem;
        color: #4b5563;
        margin-bottom: 0.75rem;
        float: right;
    }
    #employee-performance-table_wrapper .dataTables_filter input,
    #employee-performance-table_wrapper .dt-search input,
    #recent-submissions-table_wrapper .dataTables_filter input,
    #recent-submissions-table_wrapper .dt-search input {
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        outline: none;
        width: 200px;
        max-width: 100%;
        transition: border-color 0.15s;
    }
    #employee-performance-table_wrapper .dataTables_filter input:focus,
    #employee-performance-table_wrapper .dt-search input:focus,
    #recent-submissions-table_wrapper .dataTables_filter input:focus,
    #recent-submissions-table_wrapper .dt-search input:focus {
        border-color: var(--color-primary, #e11d48);
        box-shadow: 0 0 0 2px rgba(225, 29, 72, 0.1);
    }
    #employee-performance-table_wrapper .dataTables_info,
    #employee-performance-table_wrapper .dt-info,
    #recent-submissions-table_wrapper .dataTables_info,
    #recent-submissions-table_wrapper .dt-info {
        font-size: 0.75rem;
        color: #6b7280;
        padding-top: 0.75rem;
        float: left;
    }
    #employee-performance-table_wrapper .dataTables_paginate,
    #employee-performance-table_wrapper .dt-paging,
    #recent-submissions-table_wrapper .dataTables_paginate,
    #recent-submissions-table_wrapper .dt-paging {
        padding-top: 0.75rem;
        float: right;
        display: flex;
        gap: 0.25rem;
    }
    #employee-performance-table_wrapper .paginate_button,
    #employee-performance-table_wrapper .dt-paging-button,
    #recent-submissions-table_wrapper .paginate_button,
    #recent-submissions-table_wrapper .dt-paging-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.75rem;
        height: 1.75rem;
        padding: 0 0.5rem;
        font-size: 0.75rem;
        font-weight: 500;
        border-radius: 0.375rem;
        border: 1px solid #e5e7eb;
        background-color: #ffffff;
        color: #374151;
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    #employee-performance-table_wrapper .paginate_button:hover:not(.current):not(.disabled),
    #employee-performance-table_wrapper .dt-paging-button:hover:not(.current):not(:disabled),
    #recent-submissions-table_wrapper .paginate_button:hover:not(.current):not(.disabled),
    #recent-submissions-table_wrapper .dt-paging-button:hover:not(.current):not(:disabled) {
        background-color: #f9fafb;
        border-color: #d1d5db;
        color: #111827;
    }
    #employee-performance-table_wrapper .paginate_button.current,
    #employee-performance-table_wrapper .dt-paging-button.current,
    #recent-submissions-table_wrapper .paginate_button.current,
    #recent-submissions-table_wrapper .dt-paging-button.current {
        background-color: var(--color-primary, #e11d48) !important;
        border-color: var(--color-primary, #e11d48) !important;
        color: #ffffff !important;
        font-weight: 600;
    }
    #employee-performance-table_wrapper .paginate_button.disabled,
    #employee-performance-table_wrapper .dt-paging-button.disabled,
    #recent-submissions-table_wrapper .paginate_button.disabled,
    #recent-submissions-table_wrapper .dt-paging-button.disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    #employee-performance-table_wrapper::after,
    #recent-submissions-table_wrapper::after {
        content: "";
        display: table;
        clear: both;
    }
    #employee-performance-table th,
    #recent-submissions-table th {
        white-space: nowrap;
    }
    #employee-performance-table td,
    #recent-submissions-table td {
        vertical-align: middle;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Student Portal Welcome & Quick Navigation -->
    <div class="bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] rounded-2xl p-6 text-white shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#F6C75A] text-xs font-bold mb-2 border border-white/15">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                Aether Student Academic Hub
            </div>
            <h1 class="text-2xl font-black">Portal Riset, Bimbingan &amp; Olah Data Mahasiswa</h1>
            <p class="text-xs sm:text-sm text-sky-100 mt-1 max-w-2xl leading-relaxed">
                Pusat pendampingan skripsi, tesis, dan pengolahan data statistik. Akses konsultasi 1-on-1 bersama mentor, pantau progres running data Bab 4, dan ikuti workshop bersertifikat.
            </p>
        </div>
        <div class="flex flex-wrap gap-2 shrink-0">
            <a href="{{ route('bimbingan.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-md transition">
                <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                Bimbingan
            </a>
            <a href="{{ route('olahdata.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-white/20 hover:bg-white/30 border border-white/20 transition">
                <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
                Olah Data
            </a>
            <a href="{{ route('pelatihan.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-white/20 hover:bg-white/30 border border-white/20 transition">
                <i data-lucide="award" class="w-4 h-4"></i>
                Pelatihan
            </a>
        </div>
    </div>

    <!-- Header Section with Title -->
    <div class="bg-white p-5 rounded-xl border border-border shadow-sm space-y-4">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary shrink-0">
                <i data-lucide="activity" class="h-5 w-5"></i>
            </span>
            <div>
                <h2 class="text-lg font-bold text-gray-900 tracking-tight">Monitoring Aktivitas &amp; Pengelolaan Tugas Riset</h2>
                <p class="text-xs text-muted-foreground">Monitoring pengerjaan revisi, plotting tugas konsultasi tim analis, dan status pengerjaan dataset mahasiswa</p>
            </div>
        </div>

        <!-- Filter Bar: Departemen, Kategori, Tanggal Ploting (Dari - Sampai) -->
        <div class="pt-3 border-t border-gray-100">
            <form method="GET" action="{{ route('dashboard') }}" class="space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
                    
                    <!-- 1. Filter Departemen -->
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Departemen</label>
                        <div class="relative">
                            <select name="department_id" class="w-full h-9 pl-7 pr-7 text-xs font-medium bg-gray-50 border border-gray-200 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="">Semua Departemen</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}" {{ ($selectedDepartmentId == $dept->id) ? 'selected' : '' }}>
                                        {{ $dept->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i data-lucide="network" class="h-3.5 w-3.5 text-gray-400 absolute left-2.5 top-2.5"></i>
                        </div>
                    </div>

                    <!-- 2. Filter Kategori Tugas -->
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Kategori Tugas</label>
                        <div class="relative">
                            <select name="category_id" class="w-full h-9 pl-7 pr-7 text-xs font-medium bg-gray-50 border border-gray-200 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ ($selectedCategoryId == $cat->id) ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            <i data-lucide="tags" class="h-3.5 w-3.5 text-gray-400 absolute left-2.5 top-2.5"></i>
                        </div>
                    </div>

                    <!-- 3. Filter Tanggal Ploting: Dari -->
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Ploting Dari</label>
                        <div class="relative">
                            <input type="date" name="date_from" value="{{ $dateFrom }}" class="w-full h-9 pl-7 pr-2.5 text-xs font-medium bg-gray-50 border border-gray-200 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <i data-lucide="calendar" class="h-3.5 w-3.5 text-gray-400 absolute left-2.5 top-2.5"></i>
                        </div>
                    </div>

                    <!-- 4. Filter Tanggal Ploting: Sampai -->
                    <div>
                        <label class="block text-[11px] font-semibold text-gray-700 mb-1">Ploting Sampai</label>
                        <div class="relative">
                            <input type="date" name="date_to" value="{{ $dateTo }}" class="w-full h-9 pl-7 pr-2.5 text-xs font-medium bg-gray-50 border border-gray-200 rounded-lg text-gray-800 focus:outline-none focus:ring-2 focus:ring-primary/20 focus:border-primary">
                            <i data-lucide="calendar" class="h-3.5 w-3.5 text-gray-400 absolute left-2.5 top-2.5"></i>
                        </div>
                    </div>

                    <!-- 5. Action Filter Buttons -->
                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex items-center justify-center gap-1.5 h-9 px-4 rounded-lg text-xs font-semibold text-white bg-primary hover:bg-primary/90 transition-colors shadow-sm shrink-0 w-full sm:w-auto">
                            <i data-lucide="filter" class="h-3.5 w-3.5"></i>
                            <span>Terapkan</span>
                        </button>

                        @php
                            $hasActiveFilters = $selectedDepartmentId || $selectedCategoryId || $dateFrom || $dateTo;
                        @endphp
                        @if($hasActiveFilters)
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-1 h-9 px-3 rounded-lg text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors shrink-0" title="Reset Semua Filter">
                                <i data-lucide="rotate-ccw" class="h-3.5 w-3.5"></i>
                                <span>Reset</span>
                            </a>
                        @endif
                    </div>

                </div>

                <!-- Active Filter Tags indicator -->
                @if($hasActiveFilters)
                    <div class="flex flex-wrap items-center gap-2 pt-2 text-xs">
                        <span class="text-muted-foreground font-medium text-[11px]">Filter aktif:</span>
                        @if($selectedDepartmentId)
                            @php $deptObj = $departments->firstWhere('id', $selectedDepartmentId); @endphp
                            <span class="inline-flex items-center gap-1 bg-blue-50 text-blue-700 px-2 py-0.5 rounded-md text-[11px] font-medium border border-blue-200">
                                <i data-lucide="network" class="h-3 w-3"></i>
                                Departemen: {{ $deptObj?->name ?? $selectedDepartmentId }}
                            </span>
                        @endif
                        @if($selectedCategoryId)
                            @php $catObj = $categories->firstWhere('id', $selectedCategoryId); @endphp
                            <span class="inline-flex items-center gap-1 bg-purple-50 text-purple-700 px-2 py-0.5 rounded-md text-[11px] font-medium border border-purple-200">
                                <i data-lucide="tags" class="h-3 w-3"></i>
                                Kategori: {{ $catObj?->name ?? $selectedCategoryId }}
                            </span>
                        @endif
                        @if($dateFrom || $dateTo)
                            <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 px-2 py-0.5 rounded-md text-[11px] font-medium border border-amber-200">
                                <i data-lucide="calendar" class="h-3 w-3"></i>
                                Ploting: {{ $dateFrom ? \Carbon\Carbon::parse($dateFrom)->format('d/m/Y') : 'Awal' }} s/d {{ $dateTo ? \Carbon\Carbon::parse($dateTo)->format('d/m/Y') : 'Sekarang' }}
                            </span>
                        @endif
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Personal Context Banner (If Logged-in User is linked to an Employee) -->
    @if(isset($myEmployee) && $myEmployee)
    <div class="rounded-xl border border-primary/20 bg-gradient-to-r from-primary/5 via-white to-primary/5 p-4 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5">
            <div class="relative">
                @if($myEmployee->avatar_url)
                    <img src="{{ $myEmployee->avatar_url }}" alt="{{ $myEmployee->display_name }}" class="h-11 w-11 rounded-full object-cover border border-primary/30">
                @else
                    <div class="h-11 w-11 rounded-full bg-primary/10 text-primary flex items-center justify-center font-bold text-sm">
                        {{ strtoupper(substr($myEmployee->display_name, 0, 2)) }}
                    </div>
                @endif
                <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-emerald-500 border-2 border-white"></span>
            </div>
            <div>
                <h2 class="text-sm font-semibold text-gray-900">Halo, {{ $myEmployee->display_name }}! 👋</h2>
                <p class="text-xs text-muted-foreground mt-0.5">
                    Departemen {{ $myEmployee->department?->name ?? 'Umum' }} &bull; {{ $myEmployee->position?->name ?? 'Karyawan' }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            @if(isset($myTaskStats))
                <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-gray-200 text-xs shadow-2xs">
                    <span class="text-muted-foreground">Tugas Anda:</span>
                    <span class="font-bold text-gray-900">{{ $myTaskStats->total }}</span>
                    <span class="text-gray-300">|</span>
                    <span class="text-emerald-600 font-semibold">{{ $myTaskStats->completed }} Selesai</span>
                    <span class="text-gray-300">|</span>
                    <span class="{{ $myTaskStats->overdue > 0 ? 'text-rose-600 font-bold' : 'text-gray-500' }}">
                        {{ $myTaskStats->overdue }} Terlambat
                    </span>
                </div>
            @endif

            <a href="{{ route('tasks.my-tasks.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:text-primary/80 transition-colors">
                <span>Buka Tugas Saya</span>
                <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
            </a>
        </div>
    </div>
    @endif

    <!-- 5 Core KPI Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. Total Tugas Aktif -->
        <div class="rounded-xl border border-border bg-card p-5 text-card-foreground shadow-2xs hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-muted-foreground">Total Tugas Aktif</span>
                <div class="h-8 w-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="clipboard-list" class="h-4 w-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-gray-900">{{ number_format($totalTasks) }}</div>
                <div class="flex items-center gap-1.5 mt-1 text-[11px] text-muted-foreground">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-blue-500"></span>
                    <span>{{ $assignedTasks }} Ditugaskan / Antrean</span>
                </div>
            </div>
        </div>

        <!-- 2. Tingkat Penyelesaian (Completion Rate) -->
        <div class="rounded-xl border border-border bg-card p-5 text-card-foreground shadow-2xs hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-muted-foreground">Tingkat Penyelesaian</span>
                <div class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="h-4 w-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-emerald-600">{{ $completionRate }}%</div>
                <div class="w-full bg-gray-100 rounded-full h-1.5 mt-2">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min(100, $completionRate) }}%"></div>
                </div>
                <div class="mt-1 text-[11px] text-muted-foreground">
                    {{ number_format($completedTasks) }} dari {{ number_format($totalTasks) }} tugas selesai
                </div>
            </div>
        </div>

        <!-- 3. Sedang Berjalan (In Progress) -->
        <div class="rounded-xl border border-border bg-card p-5 text-card-foreground shadow-2xs hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-muted-foreground">Sedang Berjalan</span>
                <div class="h-8 w-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="hourglass" class="h-4 w-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-amber-600">{{ number_format($inProgressTasks) }}</div>
                <div class="flex items-center gap-1.5 mt-1 text-[11px] text-muted-foreground">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                    <span>Dalam progres pengerjaan</span>
                </div>
            </div>
        </div>

        <!-- 4. Tugas Terlambat (Overdue) -->
        <div class="rounded-xl border border-border bg-card p-5 text-card-foreground shadow-2xs hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-muted-foreground">Tugas Terlambat</span>
                <div class="h-8 w-8 rounded-lg {{ $overdueTasks > 0 ? 'bg-rose-100 text-rose-600' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="h-4 w-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold {{ $overdueTasks > 0 ? 'text-rose-600' : 'text-gray-900' }}">
                    {{ number_format($overdueTasks) }}
                </div>
                <div class="flex items-center gap-1.5 mt-1 text-[11px] {{ $overdueTasks > 0 ? 'text-rose-600 font-medium' : 'text-muted-foreground' }}">
                    <i data-lucide="clock" class="h-3 w-3"></i>
                    <span>{{ $overdueTasks > 0 ? 'Perlu tindakan segera' : 'Semua tugas on-track' }}</span>
                </div>
            </div>
        </div>

        <!-- 5. Pegawai Terlibat / Produktif -->
        <div class="rounded-xl border border-border bg-card p-5 text-card-foreground shadow-2xs hover:shadow-md transition-shadow relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-muted-foreground">Pegawai Produktif</span>
                <div class="h-8 w-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i data-lucide="users" class="h-4 w-4"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-extrabold text-gray-900">{{ number_format($activeAssigneesCount) }} <span class="text-xs font-normal text-muted-foreground">/ {{ $totalEmployees }}</span></div>
                <div class="flex items-center gap-1.5 mt-1 text-[11px] text-muted-foreground">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-purple-500"></span>
                    <span>Karyawan aktif bertugas</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row (Trend 14 Days & Distribution Donut) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- 14-Day Velocity & Trend Chart (Col 2/3) -->
        <div class="col-span-1 lg:col-span-2 rounded-xl border border-border bg-card text-card-foreground shadow-sm p-6 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-4 border-b border-gray-100">
                <div>
                    <h2 class="text-base font-bold text-gray-900">Tren Pengerjaan & Penyelesaian Tugas</h2>
                    <p class="text-xs text-muted-foreground mt-0.5">Perbandingan tugas baru yang ditugaskan dengan tugas yang diselesaikan (14 Hari Terakhir)</p>
                </div>
                <div class="flex items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5 font-medium text-gray-700">
                        <span class="h-2.5 w-2.5 rounded-full bg-blue-500 inline-block"></span>
                        Ditugaskan
                    </span>
                    <span class="flex items-center gap-1.5 font-medium text-gray-700">
                        <span class="h-2.5 w-2.5 rounded-full bg-emerald-500 inline-block"></span>
                        Selesai
                    </span>
                </div>
            </div>

            <div class="pt-4">
                <div id="task_trend_chart" class="h-[290px]"></div>
            </div>
        </div>

        <!-- Task Status & Type Distribution (Col 1/3) -->
        <div class="col-span-1 rounded-xl border border-border bg-card text-card-foreground shadow-sm p-6 flex flex-col justify-between">
            <div class="pb-4 border-b border-gray-100">
                <h2 class="text-base font-bold text-gray-900">Distribusi Status Tugas</h2>
                <p class="text-xs text-muted-foreground mt-0.5">Proporsi status pengerjaan seluruh tugas aktif</p>
            </div>

            <div class="py-2 flex items-center justify-center">
                <div id="task_status_donut_chart" class="h-[220px]"></div>
            </div>

            <!-- Task Type Breakdown Sub-section -->
            <div class="border-t border-gray-100 pt-3">
                <div class="text-[11px] font-bold uppercase tracking-wider text-gray-400 mb-2">Berdasarkan Tipe Tugas</div>
                <div class="grid grid-cols-3 gap-2 text-center">
                    <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                        <div class="text-[11px] text-muted-foreground">Harian</div>
                        <div class="text-sm font-bold text-gray-900 mt-0.5">{{ $typeBreakdown['harian'] }}</div>
                    </div>
                    <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                        <div class="text-[11px] text-muted-foreground">Bulanan</div>
                        <div class="text-sm font-bold text-gray-900 mt-0.5">{{ $typeBreakdown['bulanan'] }}</div>
                    </div>
                    <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                        <div class="text-[11px] text-muted-foreground">Insidentil</div>
                        <div class="text-sm font-bold text-gray-900 mt-0.5">{{ $typeBreakdown['insidentil'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee Performance Leaderboard Table -->
    <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white">
            <div>
                <h2 class="text-base font-bold text-gray-900">Monitoring & Peringkat Kinerja Pegawai</h2>
                <p class="text-xs text-muted-foreground mt-0.5">Evaluasi pencapaian, beban penugasan, dan tingkat penyelesaian per karyawan</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('tasks.assignment.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:text-primary/80 transition-colors">
                    <span>Kelola Plotting Tugas</span>
                    <i data-lucide="arrow-right" class="h-3.5 w-3.5"></i>
                </a>
            </div>
        </div>

        <div class="p-5 overflow-x-auto">
            <table id="employee-performance-table" class="w-full text-left text-sm">
                <thead class="bg-gray-50/70 text-[11px] uppercase tracking-wider text-muted-foreground font-semibold border-b border-gray-100">
                    <tr>
                        <th class="py-3.5 px-4">Pegawai</th>
                        <th class="py-3.5 px-3">Departemen / Jabatan</th>
                        <th class="py-3.5 px-3 text-center">Beban Tugas</th>
                        <th class="py-3.5 px-3 text-center">Selesai</th>
                        <th class="py-3.5 px-3 text-center">Berjalan</th>
                        <th class="py-3.5 px-3 text-center">Terlambat</th>
                        <th class="py-3.5 px-4 min-w-[160px]">Tingkat Penyelesaian</th>
                        <th class="py-3.5 px-3 text-center">Evaluasi</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Data dimuat secara server-side via DataTables -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bottom Row: Recent Submissions & Overdue Watchlist -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Recent Submissions Feed -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Aktivitas & Bukti Pengerjaan Terbaru</h2>
                        <p class="text-xs text-muted-foreground mt-0.5">Laporan pengerjaan tugas terkini yang dikirimkan pegawai</p>
                    </div>
                    <span class="text-xs bg-emerald-50 text-emerald-700 font-semibold px-2 py-0.5 rounded-full border border-emerald-200">
                        Live Data
                    </span>
                </div>

                <div class="overflow-x-auto mt-4">
                    <table id="recent-submissions-table" class="w-full text-left text-xs">
                        <thead class="bg-gray-50/70 text-[11px] uppercase tracking-wider text-muted-foreground font-semibold border-b border-gray-100">
                            <tr>
                                <th class="py-2.5 px-3">Pegawai</th>
                                <th class="py-2.5 px-3">Tugas & Catatan</th>
                                <th class="py-2.5 px-3">Status & Bukti</th>
                                <th class="py-2.5 px-3 text-right">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <!-- Data dimuat secara server-side via DataTables -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 text-right">
                <a href="{{ route('tasks.index') }}" class="text-xs font-semibold text-primary hover:underline">
                    Lihat Semua Tugas &rarr;
                </a>
            </div>
        </div>

        <!-- Urgent & Overdue Watchlist -->
        <div class="rounded-xl border border-border bg-card text-card-foreground shadow-sm p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Watchlist Tugas Mendesak & Terlambat</h2>
                        <p class="text-xs text-muted-foreground mt-0.5">Tugas berprioritas tinggi atau lewat batas waktu yang perlu difollow-up</p>
                    </div>
                    <span class="text-xs bg-rose-50 text-rose-700 font-semibold px-2 py-0.5 rounded-full border border-rose-200">
                        {{ $urgentTasks->count() }} Perlu Perhatian
                    </span>
                </div>

                <div class="divide-y divide-gray-100 mt-2">
                    @forelse($urgentTasks as $task)
                        <div class="py-3 flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="text-[11px] font-mono font-bold text-primary bg-primary/10 px-1.5 py-0.5 rounded">
                                        {{ $task->task_number }}
                                    </span>
                                    @if($task->priority)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium border {{ $task->priority->badge_class ?? 'bg-gray-100 text-gray-700' }}">
                                            {{ $task->priority->name }}
                                        </span>
                                    @endif
                                </div>

                                <a href="{{ route('tasks.show', $task->id) }}" class="text-xs font-bold text-gray-900 hover:text-primary mt-1 block truncate">
                                    {{ $task->title }}
                                </a>

                                <div class="flex items-center gap-2 mt-1.5 text-[11px] text-muted-foreground">
                                    <!-- PIC Assignee -->
                                    <div class="flex items-center gap-1">
                                        <i data-lucide="user" class="h-3 w-3 text-gray-400"></i>
                                        <span>
                                            @if($task->assignedEmployees && $task->assignedEmployees->count() > 0)
                                                {{ $task->assignedEmployees->pluck('name')->join(', ') }}
                                            @else
                                                <span class="italic text-gray-400">Belum diplot</span>
                                            @endif
                                        </span>
                                    </div>

                                    <span>&bull;</span>

                                    <!-- Due Date -->
                                    <div class="flex items-center gap-1 {{ $task->is_overdue ? 'text-rose-600 font-bold' : 'text-gray-600' }}">
                                        <i data-lucide="calendar" class="h-3 w-3"></i>
                                        <span>
                                            {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}
                                            @if($task->is_overdue)
                                                (Terlambat)
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="shrink-0 text-right">
                                <a href="{{ route('tasks.show', $task->id) }}" class="inline-flex items-center justify-center h-7 w-7 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-600 hover:text-gray-900 transition-colors">
                                    <i data-lucide="chevron-right" class="h-4 w-4"></i>
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-muted-foreground text-xs">
                            <i data-lucide="shield-check" class="h-8 w-8 text-emerald-400 mx-auto mb-2"></i>
                            Kabar baik! Tidak ada tugas mendesak atau terlambat saat ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 text-right">
                <a href="{{ route('tasks.index') }}" class="text-xs font-semibold text-primary hover:underline">
                    Kelola Semua Tugas &rarr;
                </a>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Re-initialize Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // ApexCharts: 14-Day Velocity & Trend Chart
    if (typeof ApexCharts !== 'undefined') {
        var trendOptions = {
            series: [
                {
                    name: 'Tugas Ditugaskan',
                    data: {!! json_encode($trendAssignedSeries) !!}
                },
                {
                    name: 'Tugas Selesai',
                    data: {!! json_encode($trendCompletedSeries) !!}
                }
            ],
            chart: {
                type: 'area',
                height: 290,
                toolbar: { show: false },
                zoom: { enabled: false },
                fontFamily: 'Roboto, sans-serif'
            },
            colors: ['#3b82f6', '#10b981'],
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 2.5
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.25,
                    opacityTo: 0.03,
                    stops: [0, 95, 100]
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                xaxis: { lines: { show: false } }
            },
            xaxis: {
                categories: {!! json_encode($trendDates) !!},
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '11px'
                    }
                },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '11px'
                    },
                    formatter: function(val) {
                        return Math.round(val);
                    }
                }
            },
            legend: {
                show: false
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function(val) {
                        return val + ' Tugas';
                    }
                }
            }
        };

        var trendEl = document.querySelector("#task_trend_chart");
        if (trendEl) {
            var trendChart = new ApexCharts(trendEl, trendOptions);
            trendChart.render();
        }

        // ApexCharts: Task Status Donut Chart
        var statusValues = [
            {{ $statusBreakdown['completed'] }},
            {{ $statusBreakdown['in_progress'] }},
            {{ $statusBreakdown['assigned'] }},
            {{ $statusBreakdown['overdue'] }}
        ];

        // Ensure chart renders gracefully even when all counts are 0
        var allZeros = statusValues.every(function(v) { return v === 0; });
        var displayValues = allZeros ? [1] : statusValues;
        var displayLabels = allZeros ? ['Belum Ada Data'] : ['Selesai', 'Sedang Berjalan', 'Ditugaskan', 'Terlambat'];
        var displayColors = allZeros ? ['#e2e8f0'] : ['#10b981', '#f59e0b', '#3b82f6', '#ef4444'];

        var donutOptions = {
            series: displayValues,
            labels: displayLabels,
            chart: {
                type: 'donut',
                height: 220,
                fontFamily: 'Roboto, sans-serif'
            },
            colors: displayColors,
            dataLabels: {
                enabled: !allZeros,
                formatter: function (val, opts) {
                    return Math.round(val) + "%";
                }
            },
            stroke: {
                width: 2,
                colors: ['#ffffff']
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            name: {
                                show: true,
                                fontSize: '12px',
                                color: '#64748b'
                            },
                            value: {
                                show: true,
                                fontSize: '20px',
                                fontWeight: 'bold',
                                color: '#0f172a',
                                formatter: function (val) {
                                    return allZeros ? 0 : val;
                                }
                            },
                            total: {
                                show: true,
                                label: 'Total Tugas',
                                color: '#64748b',
                                fontSize: '11px',
                                formatter: function (w) {
                                    return {{ $totalTasks }};
                                }
                            }
                        }
                    }
                }
            },
            legend: {
                position: 'bottom',
                fontSize: '11px',
                labels: {
                    colors: '#475569'
                },
                markers: {
                    width: 8,
                    height: 8
                }
            },
            tooltip: {
                enabled: !allZeros,
                y: {
                    formatter: function(val) {
                        return val + ' Tugas';
                    }
                }
            }
        };

        var donutEl = document.querySelector("#task_status_donut_chart");
        if (donutEl) {
            var donutChart = new ApexCharts(donutEl, donutOptions);
            donutChart.render();
        }
    }

    // -------------------------------------------------------------
    // DataTables: Monitoring & Peringkat Kinerja Pegawai (Server-side)
    // -------------------------------------------------------------
    if (typeof $ !== 'undefined' && $('#employee-performance-table').length) {
        var employeePerfTable = $('#employee-performance-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dashboard.employee-performance-data') }}",
                data: function (d) {
                    d.department_id = $('select[name="department_id"]').val() || '{{ $selectedDepartmentId ?? "" }}';
                    d.category_id = $('select[name="category_id"]').val() || '{{ $selectedCategoryId ?? "" }}';
                    d.date_from = $('input[name="date_from"]').val() || '{{ $dateFrom ?? "" }}';
                    d.date_to = $('input[name="date_to"]').val() || '{{ $dateTo ?? "" }}';
                    d.company_id = '{{ $selectedCompanyId ?? "" }}';
                }
            },
            columns: [
                { data: 'employee', name: 'name' },
                { data: 'department_position', name: 'department' },
                { data: 'total_tasks_formatted', name: 'total_tasks', className: 'text-center' },
                { data: 'completed_tasks_formatted', name: 'completed_tasks', className: 'text-center' },
                { data: 'in_progress_tasks_formatted', name: 'in_progress_tasks', className: 'text-center' },
                { data: 'overdue_tasks_formatted', name: 'overdue_tasks', className: 'text-center' },
                { data: 'completion_rate_bar', name: 'completion_rate' },
                { data: 'performance_badge', name: 'performance_status', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ],
            order: [[6, 'desc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                emptyTable: "Belum ada data kinerja pegawai untuk filter yang dipilih",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ pegawai",
                infoEmpty: "Menampilkan 0 pegawai",
                infoFiltered: "(disaring dari _MAX_ total pegawai)",
                lengthMenu: "Tampilkan _MENU_ data",
                loadingRecords: "Memuat data...",
                processing: "Memuat data kinerja pegawai...",
                search: "Cari Pegawai:",
                searchPlaceholder: "Nama, divisi, jabatan...",
                zeroRecords: "Tidak ada data pegawai yang sesuai pencarian"
            },
            drawCallback: function () {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });
    }

    // -------------------------------------------------------------
    // DataTables: Aktivitas & Bukti Pengerjaan Terbaru (Server-side)
    // -------------------------------------------------------------
    if (typeof $ !== 'undefined' && $('#recent-submissions-table').length) {
        var recentSubmissionsTable = $('#recent-submissions-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('dashboard.recent-submissions-data') }}",
                data: function (d) {
                    d.department_id = $('select[name="department_id"]').val() || '{{ $selectedDepartmentId ?? "" }}';
                    d.category_id = $('select[name="category_id"]').val() || '{{ $selectedCategoryId ?? "" }}';
                    d.date_from = $('input[name="date_from"]').val() || '{{ $dateFrom ?? "" }}';
                    d.date_to = $('input[name="date_to"]').val() || '{{ $dateTo ?? "" }}';
                    d.company_id = '{{ $selectedCompanyId ?? "" }}';
                }
            },
            columns: [
                { data: 'employee', name: 'employee' },
                { data: 'task_info', name: 'task_info' },
                { data: 'status_proof', name: 'status_proof', orderable: false, searchable: false },
                { data: 'time_ago', name: 'created_at', className: 'text-right' }
            ],
            order: [[3, 'desc']],
            pageLength: 5,
            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
            language: {
                emptyTable: "Belum ada aktivitas pengerjaan tugas untuk filter yang dipilih",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                infoEmpty: "Menampilkan 0 data",
                infoFiltered: "(disaring dari _MAX_ total data)",
                lengthMenu: "Tampilkan _MENU_",
                loadingRecords: "Memuat aktivitas...",
                processing: "Memuat aktivitas...",
                search: "Cari:",
                searchPlaceholder: "Pegawai, tugas, catatan...",
                zeroRecords: "Tidak ada data yang sesuai pencarian"
            },
            drawCallback: function () {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });
    }
});
</script>
@endpush

