@extends('layouts.public')

@section('title', 'Paket Olah Data Statistik & Riset Akademik | Aether Data Lab')
@section('meta_description', 'Pilihan paket lengkap olah data skripsi, tesis, dan disertasi: SPSS, SmartPLS, AMOS, Stata, EViews, NVivo, dan R. Garansi revisi dosen pembimbing dan bimbingan sampai paham.')
@section('meta_keywords', 'paket olah data skripsi, jasa statistik tesis, harga olah data spss, paket smartpls sem, aether data lab')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-[#102654] via-[#1768B5] to-[#102654] text-white py-16 lg:py-24 overflow-hidden">
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#58B7EA_1px,transparent_1px)] [background-size:24px_24px]" aria-hidden="true"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex justify-center items-center space-x-2 text-xs font-semibold text-sky-200 mb-4">
            <a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <span class="text-white">Olah Data &amp; Publikasi</span>
            <span>/</span>
            <span class="text-white">Paket Olah Data</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-4">
            📊 Solusi Lengkap Kuantitatif &amp; Kualitatif
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Paket Olah Data Riset</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Pilih paket pengolahan data yang sesuai dengan metodologi risetmu. Bebas konsultasi awal tanpa biaya sebelum pengerjaan.
        </p>
    </div>
</section>

<!-- Packages Grid -->
<section class="py-16 bg-[#F8FBFE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Paket 1 -->
            <div class="bg-white rounded-3xl p-7 border border-slate-200 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 flex items-center justify-center text-[#2B8BD1] mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#102654]">Paket Kuantitatif Dasar</h3>
                    <p class="text-xs font-bold text-[#2B8BD1] mt-1">SPSS &amp; Minitab</p>
                    <p class="text-slate-600 text-sm mt-3">
                        Uji instrumen (validitas &amp; reliabilitas), uji asumsi klasik, regresi linear sederhana/berganda, dan uji hipotesis (t, F, R-squared).
                    </p>
                    <ul class="mt-5 space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-4">
                        <li class="flex items-center gap-1.5">✓ Tabulasi &amp; cleaning data</li>
                        <li class="flex items-center gap-1.5">✓ Narasi interpretasi bab 4</li>
                        <li class="flex items-center gap-1.5">✓ Lampiran output software</li>
                        <li class="flex items-center gap-1.5">✓ Garansi revisi gratis</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20tertarik%20dengan%20Paket%20Kuantitatif%20SPSS" target="_blank" class="mt-6 block w-full text-center py-2.5 rounded-xl font-bold text-xs text-[#1768B5] bg-sky-50 hover:bg-sky-100 transition border border-sky-200">
                    Pilih Paket SPSS
                </a>
            </div>

            <!-- Paket 2 -->
            <div class="bg-white rounded-3xl p-7 border-2 border-[#2B8BD1] shadow-lg relative flex flex-col justify-between">
                <div class="absolute -top-3.5 right-6 bg-[#2B8BD1] text-white text-[10px] font-extrabold uppercase px-3 py-1 rounded-full">
                    Best Seller
                </div>
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-sky-100 flex items-center justify-center text-[#2B8BD1] mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#102654]">Paket Structural (SEM)</h3>
                    <p class="text-xs font-bold text-[#2B8BD1] mt-1">SmartPLS, AMOS, LISREL</p>
                    <p class="text-slate-600 text-sm mt-3">
                        Model persamaan struktural kompleks dengan variabel mediasi (intervening), moderasi, higher-order construct, dan multigroup analysis (MGA).
                    </p>
                    <ul class="mt-5 space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-4">
                        <li class="flex items-center gap-1.5">✓ Evaluasi Outer &amp; Inner Model</li>
                        <li class="flex items-center gap-1.5">✓ Goodness of Fit &amp; Q-squared</li>
                        <li class="flex items-center gap-1.5">✓ Diagram jalur resolusi tinggi</li>
                        <li class="flex items-center gap-1.5">✓ Bimbingan 1-on-1 sampai paham</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20tertarik%20dengan%20Paket%20SEM%20SmartPLS" target="_blank" class="mt-6 block w-full text-center py-2.5 rounded-xl font-bold text-xs text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:opacity-95 shadow">
                    Pilih Paket SEM
                </a>
            </div>

            <!-- Paket 3 -->
            <div class="bg-white rounded-3xl p-7 border border-slate-200 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 flex items-center justify-center text-emerald-600 mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#102654]">Paket Ekonometrika</h3>
                    <p class="text-xs font-bold text-emerald-600 mt-1">EViews, Stata, &amp; R</p>
                    <p class="text-slate-600 text-sm mt-3">
                        Khusus riset ekonomi, perbankan, dan keuangan. Analisis data panel (CEM, FEM, REM), uji Chow, Hausman, LM, serta time-series (ARIMA/VAR/VECM).
                    </p>
                    <ul class="mt-5 space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-4">
                        <li class="flex items-center gap-1.5">✓ Pengolahan data sekunder BEI/BPS</li>
                        <li class="flex items-center gap-1.5">✓ Solusi data tidak normal/outlier</li>
                        <li class="flex items-center gap-1.5">✓ Penulisan sintaks &amp; do-file rapi</li>
                        <li class="flex items-center gap-1.5">✓ Garansi perbaikan dosen</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20tertarik%20dengan%20Paket%20Ekonometrika%20EViews" target="_blank" class="mt-6 block w-full text-center py-2.5 rounded-xl font-bold text-xs text-emerald-800 bg-emerald-50 hover:bg-emerald-100 transition border border-emerald-200">
                    Pilih Paket Ekonometrika
                </a>
            </div>

            <!-- Paket 4 -->
            <div class="bg-white rounded-3xl p-7 border border-slate-200 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-amber-600 mb-5">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-[#102654]">Paket Kualitatif</h3>
                    <p class="text-xs font-bold text-amber-600 mt-1">NVivo, ATLAS.ti, MAXQDA</p>
                    <p class="text-slate-600 text-sm mt-3">
                        Analisis data kualitatif berbasis transkrip wawancara mendalam, FGD, studi literatur, pembuatan coding tree, word frequency, dan model konseptual tematik.
                    </p>
                    <ul class="mt-5 space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-4">
                        <li class="flex items-center gap-1.5">✓ Coding tree terstruktur</li>
                        <li class="flex items-center gap-1.5">✓ Visualisasi peta konsep tematik</li>
                        <li class="flex items-center gap-1.5">✓ Triangulasi data ilmiah</li>
                        <li class="flex items-center gap-1.5">✓ Konsultasi langsung mentor</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20tertarik%20dengan%20Paket%20Kualitatif%20NVivo" target="_blank" class="mt-6 block w-full text-center py-2.5 rounded-xl font-bold text-xs text-amber-800 bg-amber-50 hover:bg-amber-100 transition border border-amber-200">
                    Pilih Paket Kualitatif
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
