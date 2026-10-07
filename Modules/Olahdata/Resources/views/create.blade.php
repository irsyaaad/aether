@extends('layouts.app')

@section('title', 'Upload Dataset Olah Data - Aether')
@section('page_title', 'Form Pengajuan Olah Data')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6">
        <div class="mb-6 pb-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-black text-gray-900">Form Unggah Dataset &amp; Hipotesis</h2>
                <p class="text-xs text-gray-500 mt-1">Sertakan berkas kuesioner Excel atau tabulasi data agar analis dapat memproses uji statistik dengan cepat.</p>
            </div>
            <a href="{{ route('olahdata.index') }}" class="text-xs font-bold text-gray-500 hover:text-gray-800">
                ← Kembali
            </a>
        </div>

        <form action="{{ route('olahdata.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Judul Penelitian / Judul Skripsi *</label>
                <input type="text" name="judul_riset" required placeholder="Contoh: Pengaruh Beban Kerja Terhadap Burnout Dimediasi Motivasi Kerja..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Metode Analisis yang Diminta *</label>
                    <select name="metode_analisis" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                        <option value="Regresi Linier Berganda">Regresi Linier Berganda (Uji t, F, R2)</option>
                        <option value="Structural Equation Modeling (SEM-PLS)">Structural Equation Modeling (SEM-PLS)</option>
                        <option value="Covariance-Based SEM (CB-SEM AMOS)">Covariance-Based SEM (CB-SEM AMOS)</option>
                        <option value="Regresi Data Panel">Regresi Data Panel (FEM / REM / CEM)</option>
                        <option value="Path Analysis (Analisis Jalur)">Path Analysis (Analisis Jalur)</option>
                        <option value="Uji Beda (t-Test, ANOVA, Mann-Whitney)">Uji Beda (t-Test, ANOVA, Non-Parametrik)</option>
                        <option value="Kualitatif Thematic Coding">Kualitatif Thematic Coding (NVivo)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Software Statistik Pilihan *</label>
                    <select name="software" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500 bg-white">
                        <option value="SmartPLS">SmartPLS 3 / 4</option>
                        <option value="IBM SPSS">IBM SPSS Statistics 26 / 29</option>
                        <option value="EViews">EViews 12 / 13</option>
                        <option value="AMOS">IBM SPSS AMOS</option>
                        <option value="Stata">Stata MP</option>
                        <option value="R / RStudio">R / RStudio</option>
                        <option value="NVivo">NVivo 12 / 14</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Upload Dataset (Excel, CSV, SAV) *</label>
                <div class="border-2 border-dashed border-gray-300 rounded-2xl p-6 text-center hover:border-sky-500 transition bg-gray-50/50">
                    <i data-lucide="upload-cloud" class="w-8 h-8 text-sky-500 mx-auto mb-2"></i>
                    <p class="text-xs text-gray-600 font-semibold mb-1">Pilih berkas dataset dari komputer kamu</p>
                    <p class="text-[11px] text-gray-400">Format didukung: .xlsx, .xls, .csv, .sav (Maksimal 10 MB)</p>
                    <input type="file" name="dataset" class="mt-3 block mx-auto text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#1768B5] file:text-white hover:file:bg-[#102654]">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Deskripsi Hipotesis &amp; Permintaan Khusus</label>
                <textarea name="keterangan" rows="4" placeholder="Tuliskan daftar hipotesis (H1, H2, dll), variabel mediasi/moderasi, atau kriteria khusus dari dosen pembimbing..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
            </div>

            <div class="pt-3 flex justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('olahdata.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] text-white text-xs font-bold shadow-md">
                    Submit Dataset untuk Diproses
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
