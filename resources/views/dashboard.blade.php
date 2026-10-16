@extends('layouts.app')

@section('title', 'Dashboard Monitoring Riset & Akademik - ' . config('app.name'))
@section('page_title', 'Dashboard Monitoring Riset & Akademik')



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

    <!-- Academic Bimbingan & Program KPI Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3.5">
        <!-- 1. Program Aktif -->
        @php
            $bimbinganCount = isset($userActivePrograms) ? $userActivePrograms->count() : 0;
            $workshopCount = isset($userActiveWorkshops) ? $userActiveWorkshops->count() : 0;
            $totalActive = isset($totalActiveProgramsCount) ? $totalActiveProgramsCount : ($bimbinganCount + $workshopCount);
            $cardActiveLink = ($workshopCount > 0 && $bimbinganCount === 0) 
                ? route('pelatihan.kelas-saya') 
                : route('bimbingan.programs');
        @endphp
        <a href="{{ $cardActiveLink }}" class="rounded-2xl border border-gray-200 bg-white p-4 sm:p-5 shadow-xs hover:shadow-md hover:border-emerald-300 transition flex flex-col justify-between group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider group-hover:text-emerald-700 transition">Program Aktif</span>
                <div class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100 transition">
                    <i data-lucide="layers" class="h-4.5 w-4.5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-gray-900 group-hover:text-emerald-700 transition">{{ number_format($totalActive) }}</div>
                <div class="flex items-center gap-1.5 mt-1 text-[11px] text-emerald-600 font-medium truncate" title="{{ $bimbinganCount }} Paket Bimbingan, {{ $workshopCount }} Kelas & Workshop Aktif">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                    @if($bimbinganCount > 0 && $workshopCount > 0)
                        <span>{{ $bimbinganCount }} Bimbingan • {{ $workshopCount }} Workshop</span>
                    @elseif($workshopCount > 0)
                        <span>{{ $workshopCount }} Kelas/Workshop</span>
                    @elseif($bimbinganCount > 0)
                        <span>{{ $bimbinganCount }} Paket Bimbingan</span>
                    @else
                        <span>0 Paket aktif</span>
                    @endif
                </div>
            </div>
        </a>

        <!-- 2. Program Menunggu Pembayaran -->
        @if(isset($userPendingPrograms) && $userPendingPrograms->isNotEmpty())
        <a href="{{ route('bimbingan.payment.instruction', $userPendingPrograms->first()->id) }}" class="rounded-2xl border-2 border-amber-300 bg-gradient-to-br from-amber-50/80 via-white to-amber-50/40 p-4 sm:p-5 shadow-xs hover:shadow-md hover:border-amber-400 transition flex flex-col justify-between group">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-amber-800 uppercase tracking-wider group-hover:text-amber-900 transition">Menunggu Bayar</span>
                <div class="h-9 w-9 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center animate-pulse">
                    <i data-lucide="credit-card" class="h-4.5 w-4.5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-amber-700">{{ number_format($userPendingPrograms->count()) }}</div>
                <div class="flex items-center gap-1.5 mt-1 text-[11px] text-amber-800 font-bold truncate">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-500 shrink-0 animate-ping"></span>
                    <span>Bayar sekarang &rarr;</span>
                </div>
            </div>
        </a>
        @else
        <div class="rounded-2xl border border-gray-200 bg-white p-4 sm:p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Menunggu Bayar</span>
                <div class="h-9 w-9 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center">
                    <i data-lucide="credit-card" class="h-4.5 w-4.5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-gray-400">0</div>
                <div class="flex items-center gap-1.5 mt-1 text-[11px] text-gray-400 truncate">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-gray-300 shrink-0"></span>
                    <span>Semua tagihan lunas</span>
                </div>
            </div>
        </div>
        @endif

        <!-- 3. Total Sesi Bimbingan -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 sm:p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Total Sesi</span>
                <div class="h-9 w-9 rounded-xl bg-sky-50 text-[#1768B5] flex items-center justify-center">
                    <i data-lucide="graduation-cap" class="h-4.5 w-4.5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-gray-900">{{ number_format($academicStats->total_sesi) }}</div>
                <div class="flex items-center gap-1.5 mt-1 text-[11px] text-gray-500 truncate">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-sky-500 shrink-0"></span>
                    <span>Riwayat pengajuan</span>
                </div>
            </div>
        </div>

        <!-- 4. Sesi Terjadwal (Dijadwalkan) -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 sm:p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Sesi Terjadwal</span>
                <div class="h-9 w-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="calendar-check" class="h-4.5 w-4.5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-blue-600">{{ number_format($academicStats->dijadwalkan) }}</div>
                <div class="flex items-center gap-1.5 mt-1 text-[11px] text-blue-600 font-semibold truncate">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-blue-500 shrink-0"></span>
                    <span>Siap konsultasi</span>
                </div>
            </div>
        </div>

        <!-- 5. Menunggu Review / Konfirmasi -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 sm:p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Menunggu Review</span>
                <div class="h-9 w-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="h-4.5 w-4.5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-amber-600">{{ number_format($academicStats->menunggu) }}</div>
                <div class="flex items-center gap-1.5 mt-1 text-[11px] text-amber-600 font-medium truncate">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-amber-500 shrink-0"></span>
                    <span>Review mentor</span>
                </div>
            </div>
        </div>

        <!-- 6. Sesi Selesai -->
        <div class="rounded-2xl border border-gray-200 bg-white p-4 sm:p-5 shadow-xs hover:shadow-md transition flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-gray-500 uppercase tracking-wider">Tuntas / Selesai</span>
                <div class="h-9 w-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="h-4.5 w-4.5"></i>
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-black text-emerald-600">{{ number_format($academicStats->selesai) }}</div>
                <div class="flex items-center gap-1.5 mt-1 text-[11px] text-emerald-600 font-medium truncate">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                    <span>Selesai terlaksana</span>
                </div>
            </div>
        </div>
    </div>

    <!-- MAIN SECTION: Kalender & List Agenda Sesi Bimbingan Mahasiswa -->
    <div class="space-y-4">
        <!-- Section Header Bar -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-[#102654] to-[#1768B5] text-[#F6C75A] shadow-xs shrink-0">
                    <i data-lucide="calendar" class="h-6 w-6"></i>
                </span>
                <div>
                    <h2 class="text-lg font-black text-gray-900 tracking-tight">Kalender &amp; Agenda Sesi Bimbingan</h2>
                    <p class="text-xs text-gray-500">Jadwal konsultasi 1-on-1, bimbingan bab skripsi, dan agenda pendampingan riset Anda</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                <a href="{{ route('bimbingan.programs') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                    <i data-lucide="layers" class="w-4 h-4 text-gray-500"></i>
                    <span>Katalog Program</span>
                </a>
                <button type="button" onclick="openQuickSessionModal()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-black text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-sm transition">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i>
                    <span>Ajukan Sesi Baru</span>
                </button>
            </div>
        </div>

        <!-- 2-Column Calendar & Agenda Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            
            <!-- LEFT COLUMN: Kalender Interaktif (7 COLS) -->
            <div class="lg:col-span-7 bg-white rounded-2xl border border-gray-200 shadow-xs p-5 sm:p-6 space-y-4">
                <!-- Calendar Month & Navigation Controls -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-gray-100 pb-4">
                    <div class="flex items-center gap-2.5">
                        <span class="p-2 rounded-xl bg-sky-50 text-[#1768B5] border border-sky-100">
                            <i data-lucide="calendar-days" class="w-4 h-4"></i>
                        </span>
                        <div>
                            <h3 id="cal-month-year" class="text-base font-black text-gray-900 tracking-tight">Oktober 2026</h3>
                            <p class="text-[11px] text-gray-400">Pilih tanggal untuk melihat atau menambah sesi</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-1.5">
                        <button type="button" onclick="calPrevMonth()" class="p-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition shadow-2xs" title="Bulan Sebelumnya">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                        </button>
                        <button type="button" onclick="calGoToday()" class="px-3 py-1.5 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-100 hover:text-gray-900 transition shadow-2xs">
                            Hari Ini
                        </button>
                        <button type="button" onclick="calNextMonth()" class="p-2 rounded-xl border border-gray-200 text-gray-600 hover:bg-gray-100 hover:text-gray-900 transition shadow-2xs" title="Bulan Berikutnya">
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Legend Indicator -->
                <div class="flex flex-wrap items-center justify-between gap-2 text-xs bg-gray-50/80 p-2.5 rounded-xl border border-gray-100">
                    <span class="font-bold text-gray-400 uppercase tracking-wider text-[10px]">Status Sesi:</span>
                    <div class="flex flex-wrap items-center gap-3 text-[11px]">
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-sky-500"></span>
                            <span class="font-medium text-gray-700">Dijadwalkan</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="font-medium text-gray-700">Menunggu Review</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="font-medium text-gray-700">Selesai</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                            <span class="font-bold text-purple-700">Kelas &amp; Workshop</span>
                        </span>
                    </div>
                </div>

                <!-- 7-Column Day Header -->
                <div class="grid grid-cols-7 gap-1.5 text-center font-bold text-xs text-gray-500 pb-1 border-b border-gray-100">
                    <div class="py-1 text-rose-500">Min</div>
                    <div class="py-1">Sen</div>
                    <div class="py-1">Sel</div>
                    <div class="py-1">Rab</div>
                    <div class="py-1">Kam</div>
                    <div class="py-1">Jum</div>
                    <div class="py-1 text-sky-600">Sab</div>
                </div>

                <!-- Month Days Grid (Injected by JS) -->
                <div id="cal-grid" class="grid grid-cols-7 gap-1.5 pt-1">
                    <!-- Populated dynamically by renderCalendar() -->
                </div>

                <!-- Calendar Footer Note -->
                <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-gray-500">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-500 shrink-0"></i>
                        <span>Tanggal dengan titik/badge memiliki agenda bimbingan aktif.</span>
                    </span>
                    <button type="button" onclick="openQuickSessionModal()" class="text-[#1768B5] font-bold hover:underline inline-flex items-center gap-1 shrink-0">
                        <span>+ Ajukan Sesi Baru</span>
                    </button>
                </div>
            </div>

            <!-- RIGHT COLUMN: List Agenda Sesi (5 COLS) -->
            <div class="lg:col-span-5 bg-white rounded-2xl border border-gray-200 shadow-xs p-5 sm:p-6 space-y-4 flex flex-col justify-between min-h-[520px]">
                <div class="space-y-3">
                    <!-- Agenda Header -->
                    <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                        <div>
                            <h3 class="text-base font-black text-gray-900 tracking-tight flex items-center gap-2">
                                <i data-lucide="list-checks" class="w-4 h-4 text-[#1768B5]"></i>
                                <span>List Agenda Sesi</span>
                            </h3>
                            <p id="agenda-subheading" class="text-xs text-gray-500 mt-0.5">Semua agenda bimbingan bulan ini</p>
                        </div>
                        <div id="agenda-filter-reset-wrap" class="hidden">
                            <button type="button" onclick="calClearDateFilter()" class="inline-flex items-center gap-1 text-[11px] font-bold text-[#1768B5] hover:text-[#102654] bg-sky-50 px-2.5 py-1 rounded-lg border border-sky-100 transition shadow-2xs">
                                <i data-lucide="rotate-ccw" class="w-3 h-3"></i>
                                <span>Lihat Semua</span>
                            </button>
                        </div>
                    </div>

                    <!-- Status Tabs -->
                    <div class="flex items-center gap-1.5 pb-2 overflow-x-auto text-xs border-b border-gray-100">
                        <button type="button" onclick="filterAgendaStatus('all')" id="tab-status-all" class="px-2.5 py-1 rounded-lg font-bold text-xs bg-[#1768B5] text-white transition shrink-0">Semua</button>
                        <button type="button" onclick="filterAgendaStatus('Dijadwalkan')" id="tab-status-dijadwalkan" class="px-2.5 py-1 rounded-lg font-semibold text-xs text-gray-600 hover:bg-gray-100 transition shrink-0">Dijadwalkan</button>
                        <button type="button" onclick="filterAgendaStatus('Menunggu Review')" id="tab-status-menunggu" class="px-2.5 py-1 rounded-lg font-semibold text-xs text-gray-600 hover:bg-gray-100 transition shrink-0">Menunggu</button>
                        <button type="button" onclick="filterAgendaStatus('Selesai')" id="tab-status-selesai" class="px-2.5 py-1 rounded-lg font-semibold text-xs text-gray-600 hover:bg-gray-100 transition shrink-0">Selesai</button>
                        <button type="button" onclick="filterAgendaStatus('Workshop')" id="tab-status-workshop" class="px-2.5 py-1 rounded-lg font-semibold text-xs text-purple-700 bg-purple-50 hover:bg-purple-100 transition shrink-0 border border-purple-200">Kelas / Workshop</button>
                    </div>

                    <!-- Agendas Container (Scrollable) -->
                    <div id="agenda-list-container" class="space-y-3 max-h-[460px] overflow-y-auto pr-1">
                        <!-- Populated dynamically by renderAgendas() -->
                    </div>
                </div>

                <!-- Agenda Card Footer Actions -->
                <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('bimbingan.index') }}" class="text-xs font-bold text-gray-500 hover:text-[#1768B5] inline-flex items-center gap-1">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        <span>Buka Modul Bimbingan</span>
                    </a>
                    <button type="button" onclick="openQuickSessionModal()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] shadow-sm transition">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>Ajukan Sesi Baru</span>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <!-- MODAL: AJUKAN SESI BIMBINGAN BARU -->
    <div id="quickSessionModal" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl border border-gray-200 shadow-2xl max-w-xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full bg-sky-50 text-[#1768B5] text-[11px] font-bold mb-1 border border-sky-100">
                        <i data-lucide="sparkles" class="w-3 h-3"></i>
                        Jadwalkan Konsultasi 1-on-1
                    </div>
                    <h3 class="text-lg font-black text-gray-900">Ajukan Sesi Bimbingan Skripsi Baru</h3>
                    <p class="text-xs text-gray-500">Tentukan jadwal sesi konsultasi bersama mentor ahli.</p>
                </div>
                <button type="button" onclick="closeQuickSessionModal()" class="h-8 w-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form action="{{ route('bimbingan.store') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="redirect_to" value="dashboard">

                @if($isRole99 && (!isset($userActivePrograms) || $userActivePrograms->isEmpty()))
                <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-start gap-2.5">
                    <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                    <div>
                        <span class="font-bold block mb-0.5">
                            @if(isset($userPendingPrograms) && $userPendingPrograms->isNotEmpty())
                                Paket Program Sedang Menunggu Pembayaran
                            @else
                                Program Belum Dibeli
                            @endif
                        </span>
                        @if(isset($userPendingPrograms) && $userPendingPrograms->isNotEmpty())
                            <span>Anda memiliki <strong>{{ $userPendingPrograms->count() }} paket program</strong> yang sudah dipesan namun belum diselesaikan pembayarannya. Selesaikan pembayaran dan unggah bukti transfer agar paket program aktif dan dapat dipilih untuk konsultasi.</span>
                            <div class="mt-2">
                                <a href="{{ route('bimbingan.payment.instruction', $userPendingPrograms->first()->id) }}" class="inline-flex items-center gap-1 font-bold text-[#1768B5] hover:underline">
                                    <span>Bayar Program Pending Sekarang &rarr;</span>
                                </a>
                            </div>
                        @else
                            <span>Khusus mahasiswa (Role 99), Anda belum memiliki program bimbingan yang aktif. Silakan pilih dan beli program bimbingan terlebih dahulu untuk dapat mengajukan sesi konsultasi.</span>
                            <div class="mt-2">
                                <a href="{{ route('bimbingan.programs') }}" class="inline-flex items-center gap-1 font-bold text-[#1768B5] hover:underline">
                                    <span>Buka Katalog Program &amp; Beli Sekarang &rarr;</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                @endif

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-[#1768B5] mb-1.5">Pilih Paket Program Bimbingan *</label>
                    <select id="modal_program_id" name="program_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs font-medium focus:ring-2 focus:ring-[#1768B5] focus:outline-none bg-white">
                        @if($isRole99 && (!isset($userActivePrograms) || $userActivePrograms->isEmpty()))
                            <option value="">-- Anda belum memiliki program bimbingan yang dibeli --</option>
                        @else
                            <option value="">-- Pilih Paket Program --</option>
                            @foreach($modalPrograms as $prog)
                                <option value="{{ $prog->id }}">
                                    {{ $prog->nama_program }} — {{ $prog->formatted_biaya }} ({{ $prog->jumlah_sesi ?: 1 }} Sesi)
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Judul Skripsi / Tesis / Riset *</label>
                    <input type="text" name="judul_penelitian" required placeholder="Contoh: Pengaruh E-WOM dan Brand Trust Terhadap Keputusan..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-[#1768B5] focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Fokus Bab yang Dibahas *</label>
                        <select name="topik_bab" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-[#1768B5] focus:outline-none bg-white">
                            <option value="Bab 4 - Hasil Olah Data & Pembahasan" selected>Bab 4 - Hasil Olah Data &amp; Pembahasan</option>
                            <option value="Bab 1 - Latar Belakang & Rumusan Masalah">Bab 1 - Latar Belakang</option>
                            <option value="Bab 2 - Kajian Pustaka & Hipotesis">Bab 2 - Kajian Pustaka</option>
                            <option value="Bab 3 - Metodologi Riset & Instrumen">Bab 3 - Metodologi Riset</option>
                            <option value="Bab 5 - Kesimpulan & Rekomendasi">Bab 5 - Kesimpulan &amp; Saran</option>
                            <option value="Full Bab 1 s/d Bab 5">Full Bab 1 s/d 5 (Komprehensif)</option>
                            <option value="Simulasi Tanya Jawab Sidang">Simulasi Ujian Sidang</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Pilih Mentor *</label>
                        <select name="mentor" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-[#1768B5] focus:outline-none bg-white">
                            @foreach($activeMentors as $m)
                                <option value="{{ $m->nama_lengkap }}{{ $m->gelar ? ', ' . $m->gelar : '' }}">
                                    {{ $m->nama_lengkap }}{{ $m->gelar ? ', ' . $m->gelar : '' }} ({{ $m->spesialisasi }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Tanggal &amp; Waktu Sesi *</label>
                        <input type="datetime-local" id="modal_tanggal_preferensi" name="tanggal_preferensi" value="{{ now()->addDays(2)->format('Y-m-d\T14:00') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-[#1768B5] focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Media Konsultasi *</label>
                        <select name="media" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-[#1768B5] focus:outline-none bg-white">
                            <option value="Google Meet">Google Meet (Online Video)</option>
                            <option value="Zoom Meeting">Zoom Meeting</option>
                            <option value="Offline Studio Aether">Tatap Muka Studio</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Catatan Khusus / Revisi Dosen</label>
                    <textarea name="catatan" rows="3" placeholder="Tuliskan kendala analisis atau poin revisi yang ingin dibahas bersama mentor..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:ring-2 focus:ring-[#1768B5] focus:outline-none"></textarea>
                </div>

                <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2.5">
                    <button type="button" onclick="closeQuickSessionModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100 transition">
                        Batal
                    </button>
                    @if($isRole99 && (!isset($userActivePrograms) || $userActivePrograms->isEmpty()))
                        <a href="{{ route('bimbingan.programs') }}" class="px-5 py-2.5 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-black shadow-md flex items-center gap-1.5 transition">
                            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                            <span>Beli Program Terlebih Dahulu</span>
                        </a>
                    @else
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] text-white text-xs font-black shadow-md flex items-center gap-1.5 transition">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            <span>Kirim &amp; Tandai di Kalender</span>
                        </button>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: DETAIL SESI BIMBINGAN -->
    <div id="sessionDetailModal" class="fixed inset-0 z-50 bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl border border-gray-200 shadow-2xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span id="detail-status-badge" class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-100 text-[#1768B5]">
                        Dijadwalkan
                    </span>
                    <span id="detail-tiket" class="text-xs font-mono font-bold text-gray-400">#BMB-000</span>
                </div>
                <button type="button" onclick="closeSessionDetailModal()" class="h-8 w-8 rounded-full bg-gray-100 hover:bg-gray-200 text-gray-600 flex items-center justify-center transition">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Judul Skripsi / Riset</span>
                    <h3 id="detail-judul" class="text-base font-black text-gray-900 mt-0.5 leading-snug">-</h3>
                </div>

                <div class="grid grid-cols-2 gap-3 bg-gray-50 p-3.5 rounded-2xl border border-gray-100 text-xs">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-gray-400 block">Paket Program:</span>
                        <strong id="detail-program" class="text-gray-800">-</strong>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-gray-400 block">Mentor Spesialis:</span>
                        <strong id="detail-mentor" class="text-gray-800">-</strong>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-gray-400 block">Jadwal Sesi:</span>
                        <strong id="detail-jadwal" class="text-[#1768B5]">-</strong>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase text-gray-400 block">Fokus Topik:</span>
                        <strong id="detail-topik" class="text-gray-800">-</strong>
                    </div>
                </div>

                <div id="detail-meet-wrap" class="p-3.5 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-between">
                    <div class="flex items-center gap-2.5 text-xs text-sky-900">
                        <i data-lucide="video" class="w-5 h-5 text-[#1768B5] shrink-0"></i>
                        <div>
                            <div class="font-bold">Ruang Video Meeting Tersedia</div>
                            <div class="text-[11px] text-sky-700">Google Meet Online Consultation</div>
                        </div>
                    </div>
                    <a id="detail-meet-link" href="#" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-[#1768B5] text-white text-xs font-bold hover:bg-[#102654] transition shrink-0 inline-flex items-center gap-1">
                        <span>Masuk Ruang</span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>
                </div>

                <div>
                    <span id="detail-catatan-label" class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Catatan Mahasiswa / Kendala Analisis</span>
                    <p id="detail-catatan" class="text-xs text-gray-600 bg-gray-50 p-3 rounded-xl border border-gray-100 leading-relaxed italic">
                        -
                    </p>
                </div>

                <div id="detail-workshop-link-wrap" class="hidden">
                    <a id="detail-workshop-link" href="#" class="w-full py-2.5 px-3 rounded-xl bg-purple-50 hover:bg-purple-100 text-purple-700 border border-purple-200 text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-2xs">
                        <i data-lucide="book-open" class="w-4 h-4"></i>
                        <span>Buka Silabus &amp; Detail Kelas Workshop</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

            <div class="p-4 border-t border-gray-100 text-right">
                <button type="button" onclick="closeSessionDetailModal()" class="px-5 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-xs font-bold text-gray-700 transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// -------------------------------------------------------------
// Interactive Academic Calendar & Agenda Logic
// -------------------------------------------------------------
var calEventsData = {!! json_encode($calendarEvents) !!};
var monthNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

var calCurrentDate = new Date();
var calCurrentYear = 2026;
var calCurrentMonth = 9; // October (0-indexed)

// Default to month with events if available
if (calEventsData && calEventsData.length > 0) {
    var firstEvDate = new Date(calEventsData[0].date);
    if (!isNaN(firstEvDate.getTime())) {
        calCurrentYear = firstEvDate.getFullYear();
        calCurrentMonth = firstEvDate.getMonth();
    }
}

var calSelectedDate = null;
var calActiveStatusFilter = 'all';

function calPrevMonth() {
    calCurrentMonth--;
    if (calCurrentMonth < 0) {
        calCurrentMonth = 11;
        calCurrentYear--;
    }
    renderCalendar();
    renderAgendas();
}

function calNextMonth() {
    calCurrentMonth++;
    if (calCurrentMonth > 11) {
        calCurrentMonth = 0;
        calCurrentYear++;
    }
    renderCalendar();
    renderAgendas();
}

function calGoToday() {
    var now = new Date();
    calCurrentYear = now.getFullYear();
    calCurrentMonth = now.getMonth();
    calSelectedDate = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
    renderCalendar();
    renderAgendas();
}

function calClearDateFilter() {
    calSelectedDate = null;
    renderCalendar();
    renderAgendas();
}

function filterAgendaStatus(status) {
    calActiveStatusFilter = status;
    
    var tabs = ['all', 'Dijadwalkan', 'Menunggu Review', 'Selesai', 'Workshop'];
    tabs.forEach(function(t) {
        var id = 'tab-status-' + (t === 'all' ? 'all' : (t === 'Dijadwalkan' ? 'dijadwalkan' : (t === 'Menunggu Review' ? 'menunggu' : (t === 'Selesai' ? 'selesai' : 'workshop'))));
        var btn = document.getElementById(id);
        if (btn) {
            if (t === status) {
                if (t === 'Workshop') {
                    btn.className = 'px-2.5 py-1 rounded-lg font-bold text-xs bg-purple-700 text-white transition shrink-0 shadow-xs';
                } else {
                    btn.className = 'px-2.5 py-1 rounded-lg font-bold text-xs bg-[#1768B5] text-white transition shrink-0';
                }
            } else {
                if (t === 'Workshop') {
                    btn.className = 'px-2.5 py-1 rounded-lg font-semibold text-xs text-purple-700 bg-purple-50 hover:bg-purple-100 transition shrink-0 border border-purple-200';
                } else {
                    btn.className = 'px-2.5 py-1 rounded-lg font-semibold text-xs text-gray-600 hover:bg-gray-100 transition shrink-0';
                }
            }
        }
    });

    renderAgendas();
}

function formatIndoDate(dateStr) {
    if (!dateStr) return '';
    var parts = dateStr.split('-');
    if (parts.length !== 3) return dateStr;
    var y = parseInt(parts[0], 10);
    var m = parseInt(parts[1], 10) - 1;
    var d = parseInt(parts[2], 10);
    return d + ' ' + (monthNames[m] || '') + ' ' + y;
}

function renderCalendar() {
    var titleEl = document.getElementById('cal-month-year');
    if (titleEl) {
        titleEl.textContent = monthNames[calCurrentMonth] + ' ' + calCurrentYear;
    }

    var gridEl = document.getElementById('cal-grid');
    if (!gridEl) return;
    gridEl.innerHTML = '';

    var firstDayOfWeek = new Date(calCurrentYear, calCurrentMonth, 1).getDay(); // 0 = Sun
    var totalDaysInMonth = new Date(calCurrentYear, calCurrentMonth + 1, 0).getDate();
    var prevMonthTotalDays = new Date(calCurrentYear, calCurrentMonth, 0).getDate();

    var todayObj = new Date();
    var todayKey = `${todayObj.getFullYear()}-${String(todayObj.getMonth() + 1).padStart(2, '0')}-${String(todayObj.getDate()).padStart(2, '0')}`;

    // Leading days from previous month
    for (var i = firstDayOfWeek - 1; i >= 0; i--) {
        var prevDayNum = prevMonthTotalDays - i;
        var prevCell = document.createElement('div');
        prevCell.className = 'min-h-[56px] sm:min-h-[64px] p-1.5 rounded-xl border border-transparent text-gray-300 opacity-40 select-none flex flex-col justify-start items-center text-xs';
        prevCell.innerHTML = `<span class="font-medium">${prevDayNum}</span>`;
        gridEl.appendChild(prevCell);
    }

    // Days of current month
    for (var day = 1; day <= totalDaysInMonth; day++) {
        var dayKey = `${calCurrentYear}-${String(calCurrentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        var dayEvents = calEventsData.filter(function(e) { return e.date === dayKey; });
        var isToday = (dayKey === todayKey);
        var isSelected = (dayKey === calSelectedDate);

        var hasWorkshop = dayEvents.some(function(e) { return e.event_type === 'workshop' || e.status === 'Workshop'; });
        var hasScheduled = dayEvents.some(function(e) { return e.status === 'Dijadwalkan'; });
        var hasReview = dayEvents.some(function(e) { return e.status === 'Menunggu Review' || e.status === 'Menunggu Konfirmasi'; });
        var hasDone = dayEvents.some(function(e) { return e.status === 'Selesai'; });

        var cell = document.createElement('div');
        var cellClasses = 'min-h-[56px] sm:min-h-[64px] p-1.5 rounded-xl border transition-all select-none cursor-pointer flex flex-col justify-between items-center text-xs relative ';

        if (isSelected) {
            cellClasses += 'border-2 border-[#1768B5] bg-sky-100/70 font-black shadow-xs scale-[1.02] ';
        } else if (hasWorkshop && !hasScheduled && !hasReview && !hasDone) {
            cellClasses += 'bg-purple-50/70 border-purple-200 hover:border-purple-400 hover:shadow-2xs ';
        } else if (hasWorkshop && (hasScheduled || hasReview || hasDone)) {
            cellClasses += 'bg-gradient-to-br from-sky-50/70 to-purple-50/70 border-purple-200 hover:border-purple-400 hover:shadow-2xs ';
        } else if (dayEvents.length > 0) {
            cellClasses += 'bg-sky-50/70 border-sky-200 hover:border-sky-400 hover:shadow-2xs ';
        } else if (isToday) {
            cellClasses += 'border-primary/50 bg-rose-50/30 text-primary font-bold hover:bg-gray-50 ';
        } else {
            cellClasses += 'border-gray-100 bg-white hover:bg-gray-50 text-gray-700 ';
        }

        cell.className = cellClasses;
        cell.setAttribute('data-date', dayKey);

        var dayNumHtml = `<span class="${isToday ? 'h-5 w-5 rounded-full bg-primary text-white flex items-center justify-center text-[10px] font-bold' : (isSelected ? 'text-[#1768B5] font-black' : 'font-semibold')}">${day}</span>`;

        var eventIndicatorHtml = '';
        if (dayEvents.length > 0) {
            var pillClass = 'bg-[#1768B5] text-white';
            var pillText = dayEvents.length + ' Sesi';

            if (hasWorkshop && dayEvents.length === 1) {
                pillClass = 'bg-purple-600 text-white shadow-2xs';
                pillText = 'Workshop';
            } else if (hasWorkshop && (hasScheduled || hasReview || hasDone)) {
                pillClass = 'bg-gradient-to-r from-sky-600 to-purple-600 text-white shadow-2xs';
                pillText = dayEvents.length + ' Agenda';
            } else if (hasWorkshop) {
                pillClass = 'bg-purple-600 text-white shadow-2xs';
                pillText = dayEvents.length + ' Workshop';
            } else if (hasScheduled) {
                pillClass = 'bg-sky-600 text-white';
            } else if (hasReview) {
                pillClass = 'bg-amber-500 text-white';
            } else if (hasDone) {
                pillClass = 'bg-emerald-600 text-white';
            }

            eventIndicatorHtml = `<div class="w-full flex items-center justify-center mt-1">
                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-black ${pillClass} shadow-2xs truncate max-w-full">
                    <span>${pillText}</span>
                </span>
            </div>`;
        } else {
            eventIndicatorHtml = `<div class="h-4"></div>`;
        }

        cell.innerHTML = dayNumHtml + eventIndicatorHtml;

        (function(k) {
            cell.onclick = function() {
                if (calSelectedDate === k) {
                    calSelectedDate = null;
                } else {
                    calSelectedDate = k;
                }
                renderCalendar();
                renderAgendas();
            };
        })(dayKey);

        gridEl.appendChild(cell);
    }

    var totalCellsSoFar = firstDayOfWeek + totalDaysInMonth;
    var remainder = totalCellsSoFar % 7;
    if (remainder > 0) {
        var trailingDays = 7 - remainder;
        for (var t = 1; t <= trailingDays; t++) {
            var trailCell = document.createElement('div');
            trailCell.className = 'min-h-[56px] sm:min-h-[64px] p-1.5 rounded-xl border border-transparent text-gray-300 opacity-40 select-none flex flex-col justify-start items-center text-xs';
            trailCell.innerHTML = `<span class="font-medium">${t}</span>`;
            gridEl.appendChild(trailCell);
        }
    }

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function renderAgendas() {
    var subheadEl = document.getElementById('agenda-subheading');
    var resetWrap = document.getElementById('agenda-filter-reset-wrap');
    var listContainer = document.getElementById('agenda-list-container');
    if (!listContainer) return;

    var filtered = [];
    if (calSelectedDate) {
        filtered = calEventsData.filter(function(e) { return e.date === calSelectedDate; });
        if (subheadEl) subheadEl.innerHTML = `Menampilkan agenda: <strong>${formatIndoDate(calSelectedDate)}</strong>`;
        if (resetWrap) resetWrap.classList.remove('hidden');
    } else {
        filtered = calEventsData.filter(function(e) {
            var d = new Date(e.date);
            return d.getFullYear() === calCurrentYear && d.getMonth() === calCurrentMonth;
        });
        if (subheadEl) subheadEl.textContent = `Semua agenda di bulan ${monthNames[calCurrentMonth]} ${calCurrentYear} (${filtered.length} Sesi)`;
        if (resetWrap) resetWrap.classList.add('hidden');
    }

    if (calActiveStatusFilter !== 'all') {
        filtered = filtered.filter(function(e) {
            if (calActiveStatusFilter === 'Workshop') {
                return e.status === 'Workshop' || e.event_type === 'workshop';
            }
            return e.status === calActiveStatusFilter;
        });
    }

    if (filtered.length === 0) {
        var emptyMsg = calSelectedDate ? `Tidak ada jadwal sesi pada tanggal <strong>${formatIndoDate(calSelectedDate)}</strong>.` : `Belum ada jadwal sesi untuk filter status ini.`;
        var prefillParam = calSelectedDate ? `'${calSelectedDate}'` : ``;
        listContainer.innerHTML = `
            <div class="py-12 px-4 text-center rounded-2xl border-2 border-dashed border-gray-200 bg-gray-50/50">
                <div class="h-12 w-12 rounded-2xl bg-sky-50 text-[#1768B5] flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="calendar-plus" class="w-6 h-6"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-900 mb-1">Tidak Ada Agenda Sesi</h4>
                <p class="text-xs text-gray-500 max-w-xs mx-auto mb-4">${emptyMsg}</p>
                <button type="button" onclick="openQuickSessionModal(${prefillParam})" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#102654] shadow-sm transition">
                    <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                    <span>Ajukan Sesi pada Tanggal Ini</span>
                </button>
            </div>
        `;
    } else {
        var html = '';
        filtered.forEach(function(item) {
            var isWorkshop = (item.event_type === 'workshop' || item.status === 'Workshop');
            var statusBadge = '';
            if (isWorkshop) {
                statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">Kelas / Workshop</span>';
            } else if (item.status === 'Dijadwalkan') {
                statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-[#1768B5] border border-sky-200">Dijadwalkan</span>';
            } else if (item.status === 'Selesai') {
                statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Selesai</span>';
            } else {
                statusBadge = '<span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">Menunggu Review</span>';
            }

            var meetBtn = '';
            if (item.link_zoom && (item.status === 'Dijadwalkan' || isWorkshop)) {
                var meetLabel = isWorkshop ? 'Masuk Live' : 'Masuk Meet';
                var meetColor = isWorkshop ? 'bg-purple-700 hover:bg-purple-900' : 'bg-[#1768B5] hover:bg-[#102654]';
                meetBtn = `<a href="${item.link_zoom}" target="_blank" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg ${meetColor} text-white text-[11px] font-bold transition shadow-2xs">
                    <i data-lucide="video" class="w-3 h-3"></i>
                    <span>${meetLabel}</span>
                </a>`;
            }

            var programBadge = isWorkshop
                ? `<span class="text-[11px] font-bold text-purple-700 bg-purple-50 px-2 py-0.5 rounded-md border border-purple-100">${item.program}</span>`
                : `<span class="text-[11px] font-bold text-[#1768B5] bg-sky-50 px-2 py-0.5 rounded-md border border-sky-100">${item.program}</span>`;

            var cardBorder = isWorkshop
                ? 'border-purple-100 hover:border-purple-300 bg-white hover:bg-purple-50/20'
                : 'border-gray-100 hover:border-sky-200 bg-white hover:bg-sky-50/30';

            html += `
                <div class="p-3.5 rounded-xl border ${cardBorder} transition shadow-2xs space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 flex-wrap">
                            ${statusBadge}
                            <span class="text-[11px] font-bold text-gray-500">•</span>
                            ${programBadge}
                        </div>
                        <span class="text-[11px] font-mono font-bold text-gray-400">#${item.nomor_tiket}</span>
                    </div>

                    <div>
                        <h4 class="text-xs font-bold text-gray-900 hover:text-[#1768B5] transition line-clamp-2 leading-snug cursor-pointer" onclick="openSessionDetailModal('${item.id}')">
                            ${item.title}
                        </h4>
                    </div>

                    <div class="flex flex-wrap items-center justify-between gap-2 pt-1 border-t border-gray-100 text-[11px] text-gray-500">
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center gap-1 font-semibold text-gray-700">
                                <i data-lucide="calendar" class="w-3 h-3 text-gray-400"></i>
                                ${item.formatted_date}, ${item.time}
                            </span>
                            <span class="inline-flex items-center gap-1 text-gray-600">
                                <i data-lucide="user" class="w-3 h-3 text-gray-400"></i>
                                ${item.mentor}
                            </span>
                        </div>

                        <div class="flex items-center gap-1.5 shrink-0">
                            ${meetBtn}
                            <button type="button" onclick="openSessionDetailModal('${item.id}')" class="px-2.5 py-1 rounded-lg border border-gray-200 hover:bg-gray-100 text-gray-700 text-[11px] font-bold transition">
                                Detail
                            </button>
                        </div>
                    </div>
                </div>
            `;
        });
        listContainer.innerHTML = html;
    }

    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function openQuickSessionWithProgram(programId) {
    var select = document.getElementById('modal_program_id');
    if (select && programId) {
        select.value = programId;
    }
    openQuickSessionModal();
}

function openQuickSessionModal(prefillDate) {
    @if($isRole99 && (!isset($userActivePrograms) || $userActivePrograms->isEmpty()))
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'warning',
                title: 'Program Belum Dibeli',
                text: 'Khusus akun mahasiswa, Anda belum memiliki program bimbingan aktif. Silakan pilih dan beli program bimbingan terlebih dahulu.',
                confirmButtonText: 'Pilih Program di Katalog',
                confirmButtonColor: '#1768B5',
                showCancelButton: true,
                cancelButtonText: 'Batal'
            }).then(function(result) {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('bimbingan.programs') }}";
                }
            });
        } else {
            alert('Khusus akun mahasiswa, Anda belum memiliki program bimbingan aktif. Silakan beli program bimbingan terlebih dahulu di Katalog.');
            window.location.href = "{{ route('bimbingan.programs') }}";
        }
        return;
    @endif

    var modal = document.getElementById('quickSessionModal');
    if (!modal) return;
    if (prefillDate) {
        var input = document.getElementById('modal_tanggal_preferensi');
        if (input) input.value = prefillDate + 'T14:00';
    }
    modal.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeQuickSessionModal() {
    var modal = document.getElementById('quickSessionModal');
    if (modal) modal.classList.add('hidden');
}

function openSessionDetailModal(sessionId) {
    var session = calEventsData.find(function(e) { return String(e.id) === String(sessionId); });
    if (!session) return;

    var modal = document.getElementById('sessionDetailModal');
    if (!modal) return;

    var isWorkshop = (session.event_type === 'workshop' || session.status === 'Workshop');

    document.getElementById('detail-tiket').textContent = '#' + session.nomor_tiket;
    document.getElementById('detail-judul').textContent = session.title;
    document.getElementById('detail-program').textContent = session.program;
    document.getElementById('detail-mentor').textContent = session.mentor;
    document.getElementById('detail-jadwal').textContent = session.formatted_date + ', ' + session.time;
    document.getElementById('detail-topik').textContent = session.topik_bidang;
    document.getElementById('detail-catatan').textContent = session.keluhan_kendala || 'Tidak ada catatan keluhan khusus.';

    var catatanLabel = document.getElementById('detail-catatan-label');
    if (catatanLabel) {
        catatanLabel.textContent = isWorkshop ? 'Deskripsi / Silabus / Materi Workshop' : 'Catatan Mahasiswa / Kendala Analisis';
    }

    var statusBadge = document.getElementById('detail-status-badge');
    if (statusBadge) {
        statusBadge.textContent = isWorkshop ? 'Kelas & Workshop' : session.status;
        if (isWorkshop) {
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-purple-100 text-purple-800 border border-purple-200';
        } else if (session.status === 'Dijadwalkan') {
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-sky-100 text-[#1768B5]';
        } else if (session.status === 'Selesai') {
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800';
        } else {
            statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800';
        }
    }

    var meetWrap = document.getElementById('detail-meet-wrap');
    var meetLink = document.getElementById('detail-meet-link');
    if (session.link_zoom) {
        meetWrap.classList.remove('hidden');
        meetLink.href = session.link_zoom;
        if (isWorkshop) {
            meetWrap.className = 'p-3.5 rounded-2xl bg-purple-50 border border-purple-100 flex items-center justify-between';
            meetLink.className = 'px-3.5 py-1.5 rounded-xl bg-purple-700 text-white text-xs font-bold hover:bg-purple-900 transition shrink-0 inline-flex items-center gap-1';
        } else {
            meetWrap.className = 'p-3.5 rounded-2xl bg-sky-50 border border-sky-100 flex items-center justify-between';
            meetLink.className = 'px-3.5 py-1.5 rounded-xl bg-[#1768B5] text-white text-xs font-bold hover:bg-[#102654] transition shrink-0 inline-flex items-center gap-1';
        }
    } else {
        meetWrap.classList.add('hidden');
    }

    var wksLinkWrap = document.getElementById('detail-workshop-link-wrap');
    var wksLink = document.getElementById('detail-workshop-link');
    if (wksLinkWrap && wksLink) {
        if (session.link_detail) {
            wksLinkWrap.classList.remove('hidden');
            wksLink.href = session.link_detail;
        } else {
            wksLinkWrap.classList.add('hidden');
        }
    }

    modal.classList.remove('hidden');
    if (typeof lucide !== 'undefined') lucide.createIcons();
}

function closeSessionDetailModal() {
    var modal = document.getElementById('sessionDetailModal');
    if (modal) modal.classList.add('hidden');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQuickSessionModal();
        closeSessionDetailModal();
    }
});

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Calendar and Agenda
    renderCalendar();
    renderAgendas();

    // Re-initialize Lucide Icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endpush

