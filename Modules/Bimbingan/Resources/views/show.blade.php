@extends('layouts.app')

@section('title', 'Detail Bimbingan - ' . config('app.name'))
@section('page_title', 'Detail Bimbingan')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('bimbingan.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Bimbingan</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div>
                <span class="text-xs font-bold text-[#1768B5]">Sesi Konsultasi #{{ $id }}</span>
                <h1 class="text-xl font-bold text-gray-900 mt-1">Pembahasan Bab 4 &amp; Pengujian SEM-PLS 4</h1>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                Terkonfirmasi
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-gray-700">
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2">
                <span class="text-gray-400 block font-semibold">Informasi Mentor</span>
                <p class="font-bold text-gray-900 text-sm">Nur Atika Rochmah, M.Si.</p>
                <p>Spesialis Statistika Kuantitatif &amp; SEM-PLS</p>
                <p>Jadwal: <strong>10 Oktober 2026, 14:00 WIB</strong></p>
            </div>

            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2">
                <span class="text-gray-400 block font-semibold">Tautan Pertemuan Online</span>
                <p class="font-bold text-gray-900 text-sm">Google Meet</p>
                <a href="https://meet.google.com/aether-bmb-01" target="_blank" class="inline-flex items-center gap-1.5 text-[#1768B5] hover:underline font-bold">
                    <i data-lucide="video" class="w-4 h-4"></i>
                    <span>meet.google.com/aether-bmb-01</span>
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
