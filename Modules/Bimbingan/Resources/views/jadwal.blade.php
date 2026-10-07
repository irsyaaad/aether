@extends('layouts.app')

@section('title', 'Jadwal Sesi Konsultasi - ' . config('app.name'))
@section('page_title', 'Jadwal Sesi Konsultasi')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="p-2.5 bg-sky-50 text-[#1768B5] rounded-2xl border border-sky-100">
                <i data-lucide="calendar" class="w-6 h-6"></i>
            </span>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Agenda &amp; Kalender Sesi Bimbingan</h2>
                <p class="text-xs text-gray-500">Daftar jadwal konsultasi 1-on-1 bersama dosen pembimbing &amp; mentor ahli riset Anda.</p>
            </div>
        </div>
        <a href="{{ route('bimbingan.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] shadow-sm transition shrink-0">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Ajukan Jadwal Baru</span>
        </a>
    </div>

    <!-- Timeline Jadwal -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
            <i data-lucide="clock" class="w-4 h-4 text-[#1768B5]"></i>
            <span>Jadwal Bimbingan Terdekat</span>
        </h3>

        <div class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden">
            <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Terkonfirmasi
                    </span>
                    <h4 class="text-sm font-bold text-gray-900">Pembahasan Bab 4: Outer Model &amp; Inner Model SEM-PLS</h4>
                    <p class="text-xs text-gray-500">Mentor: <strong>Nur Atika Rochmah, M.Si.</strong> &bull; Waktu: <strong>10 Okt 2026, 14:00 - 15:30 WIB</strong></p>
                </div>
                <a href="https://meet.google.com/aether-bmb-01" target="_blank" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] transition shrink-0">
                    <i data-lucide="video" class="w-4 h-4"></i>
                    <span>Masuk Google Meet</span>
                </a>
            </div>

            <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-gray-50/50">
                <div class="space-y-1">
                    <span class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded bg-blue-50 text-blue-700 border border-blue-200">
                        Menunggu Konfirmasi
                    </span>
                    <h4 class="text-sm font-bold text-gray-900">Review Bab 3: Spesifikasi Model Regresi Panel &amp; Uji Chow</h4>
                    <p class="text-xs text-gray-500">Mentor: <strong>Deny Aditya Pratama, S.Stat.</strong> &bull; Waktu: <strong>12 Okt 2026, 19:30 - 21:00 WIB</strong></p>
                </div>
                <span class="text-xs font-semibold text-gray-400 italic">Menunggu respon mentor</span>
            </div>
        </div>
    </div>

</div>
@endsection
