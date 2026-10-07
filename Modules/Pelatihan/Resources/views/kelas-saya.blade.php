@extends('layouts.app')

@section('title', 'Kelas Terdaftar Saya - ' . config('app.name'))
@section('page_title', 'Kelas Terdaftar Saya')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] rounded-2xl p-6 text-white shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-[#F6C75A] text-xs font-bold mb-2 border border-white/15">
                <i data-lucide="award" class="w-3.5 h-3.5"></i>
                <span>Portal Pembelajaran Mahasiswa</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black">Kelas &amp; Workshop Terdaftar Saya</h1>
            <p class="text-xs sm:text-sm text-sky-100 mt-1 max-w-xl">
                Akses link pertemuan live mentoring, materi modul silabus, rekaman video, dan unduh sertifikat kelulusan pelatihan Anda.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pelatihan.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Jelajahi Workshop Lain</span>
            </a>
        </div>
    </div>

    <!-- Active Enrolled Courses -->
    <div class="space-y-4">
        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="play-circle" class="w-4 h-4 text-[#1768B5]"></i>
            <span>Kelas yang Sedang Diikuti (2 Kelas)</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Card 1 -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:border-[#1768B5]/50 transition-all">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Aktif Berjalan
                        </span>
                        <span class="text-xs font-mono text-gray-400">ID: KLS-PLS-01</span>
                    </div>

                    <h3 class="text-base font-bold text-gray-900 hover:text-[#1768B5] transition">
                        Mastering SEM-PLS 4: Dari Kerangka Konseptual Hingga Interpretasi Bab 4
                    </h3>

                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400"></i>
                        <span>Instruktur: <strong>Nur Atika Rochmah, M.Si.</strong></span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1.5 pt-2">
                        <div class="flex justify-between text-xs font-medium text-gray-600">
                            <span>Progres Belajar</span>
                            <span class="font-bold text-[#1768B5]">5 dari 8 Modul (62%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#1768B5] h-2 rounded-full" style="width: 62%"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-5 mt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                    <a href="https://meet.google.com/aether-pls" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] transition">
                        <i data-lucide="video" class="w-3.5 h-3.5"></i>
                        <span>Buka Live Meet</span>
                    </a>
                    <a href="{{ route('pelatihan.show', 1) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                        <span>Akses Materi</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 flex flex-col justify-between hover:border-[#1768B5]/50 transition-all">
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200">
                            Jadwal Mendatang
                        </span>
                        <span class="text-xs font-mono text-gray-400">ID: KLS-EVW-02</span>
                    </div>

                    <h3 class="text-base font-bold text-gray-900 hover:text-[#1768B5] transition">
                        Workshop Regresi Data Panel Menggunakan EViews &amp; Stata untuk Tesis Magister
                    </h3>

                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400"></i>
                        <span>Instruktur: <strong>Deny Aditya Pratama, S.Stat.</strong></span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1.5 pt-2">
                        <div class="flex justify-between text-xs font-medium text-gray-600">
                            <span>Progres Belajar</span>
                            <span class="font-bold text-gray-400">Belum Dimulai (0%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#1768B5] h-2 rounded-full" style="width: 0%"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-5 mt-4 border-t border-gray-100 flex items-center justify-between gap-3">
                    <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1.5 rounded-lg border border-amber-200 flex items-center gap-1.5">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                        Mulai 22 Okt 2026
                    </span>
                    <a href="{{ route('pelatihan.show', 2) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                        <span>Lihat Silabus</span>
                        <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
