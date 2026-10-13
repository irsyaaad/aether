@extends('layouts.app')

@section('title', 'Modul Bimbingan Skripsi & Riset - Aether')
@section('page_title', 'Bimbingan Akademik & Tugas Akhir')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#F6C75A] text-xs font-bold mb-2 border border-white/15">
                <i data-lucide="graduation-cap" class="w-3.5 h-3.5"></i>
                Mentorship 1-on-1 Sampai Paham &amp; Lulus
            </div>
            <h1 class="text-2xl font-black">Sesi Bimbingan Tugas Akhir Mahasiswa</h1>
            <p class="text-sm text-sky-100 mt-1 max-w-xl">
                Jadwalkan konsultasi bersama mentor ahli untuk membedah Bab 1 sampai Bab 5, revisi dosen pembimbing, hingga persiapan simulasi ujian sidang skripsi/tesis.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('bimbingan.programs') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-white/15 hover:bg-white/25 border border-white/20 transition backdrop-blur-xs">
                <i data-lucide="layout-grid" class="w-4 h-4"></i>
                Katalog &amp; Pilihan Program
            </a>
            <a href="{{ route('bimbingan.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-md transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Ajukan Sesi Baru
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Total Sesi</span>
                <i data-lucide="book-open" class="w-4 h-4 text-sky-600"></i>
            </div>
            <div class="text-2xl font-black text-gray-900">{{ $stats['total'] }}</div>
            <span class="text-[11px] text-gray-400">Sepanjang keanggotaan</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Sesi Aktif</span>
                <i data-lucide="clock" class="w-4 h-4 text-amber-500"></i>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ $stats['aktif'] }}</div>
            <span class="text-[11px] text-amber-600 font-semibold">Sedang berjalan</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Tuntas</span>
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-500"></i>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ $stats['selesai'] }}</div>
            <span class="text-[11px] text-emerald-600 font-semibold">Selesai &amp; ACC Dosen</span>
        </div>

        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Jadwal Terdekat</span>
                <i data-lucide="calendar" class="w-4 h-4 text-purple-500"></i>
            </div>
            <div class="text-2xl font-black text-purple-600">{{ $stats['jadwal_mendatang'] }}</div>
            <span class="text-[11px] text-purple-600 font-semibold">Sesi 1-on-1 Online</span>
        </div>
    </div>

    <!-- Sesi Bimbingan List -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Daftar Sesi Bimbingan Terkini</h3>
                <p class="text-xs text-gray-500">Pantau kemajuan konsultasi dengan mentor riset dan status paket Anda.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('bimbingan.programs') }}" class="text-xs font-bold text-gray-500 hover:text-[#1768B5] flex items-center gap-1">
                    <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                    <span>Katalog Program</span>
                </a>
                <a href="{{ route('bimbingan.create') }}" class="text-xs font-bold text-[#1768B5] hover:underline flex items-center gap-1">
                    <span>+ Buat Permintaan</span>
                </a>
            </div>
        </div>

        <div class="divide-y divide-gray-100">
            @foreach($sessions as $s)
            <div class="p-5 hover:bg-gray-50/80 transition flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="space-y-1.5 max-w-2xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold 
                            {{ $s['status'] === 'Selesai' ? 'bg-emerald-100 text-emerald-800' : ($s['status'] === 'Dijadwalkan' ? 'bg-sky-100 text-[#1768B5]' : 'bg-amber-100 text-amber-800') }}">
                            {{ $s['status'] }}
                        </span>
                        <span class="text-xs font-bold text-gray-400">•</span>
                        <span class="px-2 py-0.5 rounded-md text-[11px] font-black bg-indigo-50 text-indigo-700 border border-indigo-100">
                            {{ $s['program'] }}
                        </span>
                        @if(!empty($s['biaya']) && $s['biaya'] !== '-')
                        <span class="text-xs font-bold text-emerald-700">
                            ({{ $s['biaya'] }})
                        </span>
                        @endif
                    </div>
                    <h4 class="text-sm font-bold text-gray-900 hover:text-[#2B8BD1] transition">
                        {{ $s['judul'] }}
                    </h4>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        {{ $s['catatan'] }}
                    </p>
                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500 pt-1">
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400"></i>
                            Mentor: <strong class="text-gray-700">{{ $s['mentor'] }}</strong>
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i>
                            {{ $s['tanggal'] }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-sky-700 font-medium">
                            <i data-lucide="video" class="w-3.5 h-3.5"></i>
                            {{ $s['media'] }}
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('bimbingan.create') }}" class="px-3.5 py-1.5 rounded-lg border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100 transition">
                        Detail Catatan
                    </a>
                    @if($s['status'] === 'Dijadwalkan')
                    <a href="https://meet.google.com" target="_blank" class="px-3.5 py-1.5 rounded-lg bg-[#2B8BD1] text-white text-xs font-bold hover:bg-[#1768B5] transition inline-flex items-center gap-1.5">
                        <i data-lucide="video" class="w-3.5 h-3.5"></i>
                        Masuk Ruang
                    </a>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
