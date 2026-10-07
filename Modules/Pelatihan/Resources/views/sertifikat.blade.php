@extends('layouts.app')

@section('title', 'E-Sertifikat Kelulusan - ' . config('app.name'))
@section('page_title', 'E-Sertifikat Kelulusan')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="p-2.5 bg-amber-50 text-amber-700 rounded-2xl border border-amber-200">
                <i data-lucide="award" class="w-6 h-6"></i>
            </span>
            <div>
                <h2 class="text-lg font-bold text-gray-900">E-Sertifikat Pelatihan &amp; Workshop Riset</h2>
                <p class="text-xs text-gray-500">Daftar sertifikat kelulusan yang telah diterbitkan lengkap dengan nomor verifikasi kredensial resmi Aether.</p>
            </div>
        </div>
    </div>

    <!-- Certificate List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Certificate Item 1 -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
            <div class="flex items-start justify-between gap-3">
                <div class="space-y-1">
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2 py-0.5 rounded bg-purple-50 text-purple-700 border border-purple-200">
                        Bootcamp Bersertifikat
                    </span>
                    <h3 class="text-base font-bold text-gray-900">
                        Bootcamp Mahir SPSS: Uji Validitas, Reliabilitas, &amp; Asumsi Klasik
                    </h3>
                    <p class="text-xs text-gray-500 font-mono">No. Sertifikat: AETH-CERT/2026/SPSS-0849</p>
                </div>
                <div class="p-2 bg-emerald-50 text-emerald-600 rounded-xl shrink-0">
                    <i data-lucide="check-circle" class="w-6 h-6"></i>
                </div>
            </div>

            <div class="text-xs text-gray-600 border-t border-b border-gray-100 py-3 space-y-1">
                <div class="flex justify-between">
                    <span class="text-gray-400">Penerima:</span>
                    <strong class="text-gray-800">{{ Auth::user()->nm_user ?? 'Mahasiswa Aether' }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Instruktur:</span>
                    <span class="text-gray-700">Arrum Rochmani Hidayah, M.Kom.</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Tanggal Terbit:</span>
                    <span class="text-gray-700">01 Oktober 2026</span>
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 pt-1">
                <span class="text-xs text-emerald-700 font-semibold flex items-center gap-1">
                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                    Terverifikasi Valid
                </span>
                <button type="button" onclick="alert('Mengunduh e-sertifikat PDF berlisensi resmi...')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] transition">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    <span>Unduh PDF</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
