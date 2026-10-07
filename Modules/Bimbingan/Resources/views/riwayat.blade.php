@extends('layouts.app')

@section('title', 'Riwayat Konsultasi Bimbingan - ' . config('app.name'))
@section('page_title', 'Riwayat Konsultasi Bimbingan')

@section('content')
<div class="space-y-6">

    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex items-center justify-between">
        <div>
            <h2 class="text-lg font-bold text-gray-900">Riwayat &amp; Log Sesi Bimbingan Selesai</h2>
            <p class="text-xs text-gray-500">Catatan notulensi konsultasi, arahan revisi skripsi, dan rekaman bimbingan sebelumnya.</p>
        </div>
        <a href="{{ route('bimbingan.index') }}" class="px-3.5 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
            Kembali ke Dashboard Bimbingan
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
        <div class="divide-y divide-gray-100">
            <div class="py-4 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                        Selesai Dilaksanakan
                    </span>
                    <span class="text-xs text-gray-400">05 Oktober 2026</span>
                </div>
                <h3 class="text-base font-bold text-gray-900">Bimbingan Bab 4: Triangulasi Data Kualitatif &amp; Coding Tree NVivo</h3>
                <p class="text-xs text-gray-600">Mentor: Alessandra Natasya Panie, S.Psi.</p>
                <div class="bg-gray-50 p-3 rounded-xl border border-gray-100 text-xs text-gray-700">
                    <strong>Catatan Mentor:</strong> Analisis tematik telah valid, lanjutkan penulisan sub-bab 4.3 untuk pembahasan implikasi praktis bagi stakeholder UMKM.
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
