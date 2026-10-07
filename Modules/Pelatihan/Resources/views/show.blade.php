@extends('layouts.app')

@section('title', 'Detail Kelas & Materi - ' . config('app.name'))
@section('page_title', 'Detail Kelas & Silabus')

@section('content')
<div class="space-y-6">

    <!-- Header Action -->
    <div class="flex items-center justify-between">
        <a href="{{ route('pelatihan.kelas-saya') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Kelas Saya</span>
        </a>
    </div>

    <!-- Course Overview Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-4 border-b border-gray-100">
            <div>
                <span class="text-xs font-bold text-[#1768B5] uppercase tracking-wider">Materi Kelas</span>
                <h1 class="text-xl font-bold text-gray-900 mt-1">Mastering SEM-PLS 4: Dari Kerangka Konseptual Hingga Interpretasi Bab 4</h1>
                <p class="text-xs text-gray-500 mt-1">Mentor: <strong>Nur Atika Rochmah, M.Si.</strong> &bull; Durasi Total: 8 Modul Praktik</p>
            </div>
            <a href="https://meet.google.com/aether-pls" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] shadow-sm transition shrink-0">
                <i data-lucide="video" class="w-4 h-4"></i>
                <span>Masuk Ruang Sesi Live</span>
            </a>
        </div>

        <!-- Syllabus Items -->
        <div class="space-y-3">
            <h3 class="text-sm font-bold text-gray-800">Daftar Modul &amp; File Praktik</h3>

            <div class="divide-y divide-gray-100 border border-gray-100 rounded-xl overflow-hidden">
                <div class="p-4 bg-gray-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-sky-100 text-[#1768B5] font-bold text-xs flex items-center justify-center">#1</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900">Pengenalan SmartPLS 4 &amp; Format Dataset yang Benar</h4>
                            <span class="text-[11px] text-gray-500">Video Tutorial (45 Menit)</span>
                        </div>
                    </div>
                    <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded">Selesai Ditonton</span>
                </div>

                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-sky-100 text-[#1768B5] font-bold text-xs flex items-center justify-center">#2</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900">Evaluasi Measurement Model (Outer Model): Validitas &amp; Reliabilitas</h4>
                            <span class="text-[11px] text-gray-500">Video Tutorial (60 Menit)</span>
                        </div>
                    </div>
                    <button class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392]">Putar Video</button>
                </div>

                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-800 font-bold text-xs flex items-center justify-center">#3</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900">Template &amp; Panduan Narasi Pembahasan Bab 4 Siap Sidang</h4>
                            <span class="text-[11px] text-gray-500">Dokumen Microsoft Word (.DOCX)</span>
                        </div>
                    </div>
                    <button class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200">Unduh Dokumen</button>
                </div>

                <div class="p-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-purple-100 text-purple-800 font-bold text-xs flex items-center justify-center">#4</span>
                        <div>
                            <h4 class="text-xs font-bold text-gray-900">Master Dataset Praktik: 150 Responden Kuisioner Skala Likert</h4>
                            <span class="text-[11px] text-gray-500">File Dataset (.CSV / .SAV)</span>
                        </div>
                    </div>
                    <button class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200">Unduh Dataset</button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
