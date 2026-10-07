@extends('layouts.app')

@section('title', 'Detail Progres Olah Data - ' . config('app.name'))
@section('page_title', 'Detail Progres Olah Data')

@section('content')
<div class="space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('olahdata.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Olah Data</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div>
                <span class="text-xs font-bold text-[#1768B5]">Project Order #{{ $id }}</span>
                <h1 class="text-xl font-bold text-gray-900 mt-1">Analisis Jalur Pengaruh Brand Equity Terhadap Loyalitas Pelanggan</h1>
                <p class="text-xs text-gray-500 mt-1">Software: <strong>SmartPLS 4</strong> &bull; Analis: <strong>Nur Atika Rochmah, M.Si.</strong></p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                Penyusunan Bab 4 (85%)
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-gray-700">
            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2">
                <span class="text-gray-400 block font-semibold">Dataset &amp; Dokumen Masukan</span>
                <p class="font-bold text-gray-900">dataset_responden_240.xlsx</p>
                <p>240 Sampel Kuisioner Terverifikasi</p>
            </div>

            <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-2">
                <span class="text-gray-400 block font-semibold">Output Running Data</span>
                <p class="font-bold text-emerald-700">output_smartpls_bab4_draft.docx</p>
                <button type="button" onclick="alert('Mengunduh draft output Bab 4 dan matriks PLS...')" class="inline-flex items-center gap-1.5 text-[#1768B5] hover:underline font-bold">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    <span>Unduh Hasil Olah Data</span>
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
