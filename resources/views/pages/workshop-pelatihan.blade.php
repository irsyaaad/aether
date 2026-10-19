@extends('layouts.public')

@section('title', 'Workshop & Pelatihan Analisis Data Riset Bersertifikat | Aether Data Lab')
@section('meta_description', 'Ikuti workshop dan pelatihan intensif olah data statistik Aether: SmartPLS, SPSS, NVivo, Stata, dan Python riset dengan studi kasus riil, e-sertifikat resmi ber-barcode, dan rekaman kelas seumur hidup.')
@section('meta_keywords', 'workshop olah data, pelatihan smartpls, kursus statistik spss, bootcamp riset mahasiswa, sertifikat pelatihan riset, aether data lab')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-[#102654] via-[#1768B5] to-[#102654] text-white py-16 lg:py-24 overflow-hidden">
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#58B7EA_1px,transparent_1px)] [background-size:24px_24px]" aria-hidden="true"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex justify-center items-center space-x-2 text-xs font-semibold text-sky-200 mb-4">
            <a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <span class="text-white">Class &amp; Programs</span>
            <span>/</span>
            <span class="text-white">Workshop &amp; Pelatihan</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-4">
            🎓 Pelatihan Praktis &amp; Sertifikat Terverifikasi
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Workshop &amp; Pelatihan Analisis Riset</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Tingkatkan keahlian olah data secara mandiri melalui kelas intensif langsung bersama konsultan statistik berpengalaman.
        </p>
    </div>
</section>

<!-- Courses Grid -->
<section class="py-16 bg-[#F8FBFE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Workshop 1 -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <span class="px-3 py-1 bg-sky-100 text-[#1768B5] rounded-full text-xs font-bold uppercase">Weekend Class</span>
                    <h3 class="text-xl font-bold text-[#102654] mt-3">Masterclass SmartPLS 4: Structural Equation Modeling</h3>
                    <p class="text-slate-600 text-sm mt-3">
                        Pelatihan mendalam mulai dari rancangan model, mediasi, moderasi, evaluasi outer/inner model, hingga penulisan narasi hasil penelitian untuk bab 4 dan jurnal.
                    </p>
                    <ul class="mt-6 space-y-2 text-xs text-slate-600">
                        <li>📅 Durasi: 2 Hari (Total 6 Jam)</li>
                        <li>💻 Dataset &amp; Template Interpretasi Word</li>
                        <li>📜 E-Sertifikat 32 JP terverifikasi QR</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20mau%20daftar%20Workshop%20SmartPLS" target="_blank" class="mt-8 block w-full text-center py-2.5 rounded-xl font-bold text-xs text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:opacity-95 shadow">
                    Daftar Kelas SmartPLS
                </a>
            </div>

            <!-- Workshop 2 -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <span class="px-3 py-1 bg-amber-100 text-[#F29B3A] rounded-full text-xs font-bold uppercase">Reguler Class</span>
                    <h3 class="text-xl font-bold text-[#102654] mt-3">SPSS Kuantitatif: Dari Input Data Hingga Sidang Skripsi</h3>
                    <p class="text-slate-600 text-sm mt-3">
                        Pelajari cara olah data mandiri dengan SPSS: uji validitas kuesioner, uji reliabilitas, uji normalitas &amp; asumsi klasik, regresi berganda, serta tips menjawab pertanyaan penguji.
                    </p>
                    <ul class="mt-6 space-y-2 text-xs text-slate-600">
                        <li>📅 Durasi: 2 Hari (Total 6 Jam)</li>
                        <li>💻 Panduan Langkah demi Langkah PDF</li>
                        <li>📜 E-Sertifikat Resmi Aether</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20mau%20daftar%20Workshop%20SPSS" target="_blank" class="mt-8 block w-full text-center py-2.5 rounded-xl font-bold text-xs text-white bg-[#102654] hover:bg-[#1768B5] transition shadow">
                    Daftar Kelas SPSS
                </a>
            </div>

            <!-- Workshop 3 -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase">Specialist Class</span>
                    <h3 class="text-xl font-bold text-[#102654] mt-3">Analisis Kualitatif &amp; Thematic Coding dengan NVivo</h3>
                    <p class="text-slate-600 text-sm mt-3">
                        Kuasai teknik reduksi wawancara dan dokumen dengan NVivo. Belajar membuat coding tree yang elegan, visualisasi konsep tematik, dan matriks triangulasi.
                    </p>
                    <ul class="mt-6 space-y-2 text-xs text-slate-600">
                        <li>📅 Durasi: 2 Hari (Total 6 Jam)</li>
                        <li>💻 Transkrip Kasus Riil &amp; Project File</li>
                        <li>📜 E-Sertifikat Resmi Aether</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20mau%20daftar%20Workshop%20NVivo" target="_blank" class="mt-8 block w-full text-center py-2.5 rounded-xl font-bold text-xs text-emerald-800 bg-emerald-50 hover:bg-emerald-100 transition border border-emerald-200">
                    Daftar Kelas NVivo
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
