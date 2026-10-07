@extends('layouts.app')

@section('title', 'Diagnosis Dataset Riset - ' . config('app.name'))
@section('page_title', 'Diagnosis Dataset Riset')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-3">
            <span class="p-2.5 bg-amber-50 text-amber-700 rounded-2xl border border-amber-200">
                <i data-lucide="activity" class="w-6 h-6"></i>
            </span>
            <div>
                <h2 class="text-lg font-bold text-gray-900">Diagnosis Awal Kelayakan Data (Pre-Analysis Check)</h2>
                <p class="text-xs text-gray-500">Pemeriksaan missing value, outlier multivariate, normalitas, dan kecukupan sampel sebelum running olah data.</p>
            </div>
        </div>
        <a href="{{ route('olahdata.create') }}" class="px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] shadow-sm transition">
            Upload Dataset Baru
        </a>
    </div>

    <!-- Diagnostic Checklist -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-800">1. Missing Values</span>
                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">0 Missing (Lolos)</span>
            </div>
            <p class="text-xs text-gray-500">Tidak terdeteksi adanya data kuisioner kosong pada total 240 baris responden.</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-800">2. Outlier Mahalanobis</span>
                <span class="text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Normal (Lolos)</span>
            </div>
            <p class="text-xs text-gray-500">Nilai p-value d-squared di atas alpha 0.001, bebas dari pencilan ekstrem.</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-800">3. Rekomendasi Software</span>
                <span class="text-[11px] font-bold text-[#1768B5] bg-sky-50 px-2 py-0.5 rounded">SmartPLS 4</span>
            </div>
            <p class="text-xs text-gray-500">Model konseptual memiliki variabel mediasi dan indikator reflektif majemuk.</p>
        </div>
    </div>

</div>
@endsection
