@extends('layouts.public')

@section('title', 'Publikasi Jurnal Ilmiah SINTA & Scopus | Aether Science Publisher')
@section('meta_description', 'Layanan publikasi jurnal nasional terakreditasi SINTA 1-6 dan internasional bereputasi Scopus/WoS: academic proofreading, formatting author guidelines, dan pendampingan peer-review.')
@section('meta_keywords', 'publikasi jurnal sinta, jasa publikasi scopus, pendampingan jurnal ilmiah, proofreading jurnal, aether science publisher')

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
            <span class="text-white">Jurnal Aether</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#F6C75A] text-xs font-bold border border-white/15 mb-4">
            📚 Aether Science Publisher &amp; Journal Fast-Track
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Publikasi Jurnal Nasional &amp; Internasional</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Pendampingan menyeluruh dari konversi skripsi/tesis menjadi artikel ilmiah, translasi akademik, perbaikan peer-review, hingga naskah terbit (Letter of Acceptance).
        </p>
    </div>
</section>

<!-- Journal Scope & Services -->
<section class="py-16 bg-[#F8FBFE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-2xl bg-sky-100 flex items-center justify-center text-[#2B8BD1] mb-5 font-bold text-lg">
                    SINTA
                </div>
                <h3 class="text-xl font-bold text-[#102654]">Jurnal Nasional SINTA 1 - 6</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Pendampingan submission ke jurnal nasional terindeks Kemendikbudristek (SINTA). Penyesuaian fokus &amp; ruang lingkup, layouting template, dan korespondensi editor.
                </p>
                <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500 space-y-2">
                    <div>⏱ Estimasi LoA: 2 - 8 minggu</div>
                    <div>🎯 Syarat kelulusan S1 &amp; S2</div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 border-2 border-[#2B8BD1] shadow-xl transition-all relative">
                <div class="w-12 h-12 rounded-2xl bg-sky-500 text-white flex items-center justify-center mb-5 font-bold text-lg shadow-md">
                    SCOPUS
                </div>
                <h3 class="text-xl font-bold text-[#102654]">Jurnal Internasional Scopus (Q1 - Q4)</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Riset berstandar global. Dilengkapi translasi akademik Native English speaker, structural rewriting, uji similarity Turnitin &lt; 15%, dan respon tanggapan reviewer.
                </p>
                <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500 space-y-2">
                    <div>⏱ Garansi terindeks Scopus &amp; WoS</div>
                    <div>🎯 Syarat kelulusan S3 &amp; kenaikan jenjang lektor</div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-[#F29B3A] mb-5 font-bold text-lg">
                    EDIT
                </div>
                <h3 class="text-xl font-bold text-[#102654]">Academic Proofreading &amp; Translasi</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Penyempurnaan tata bahasa akademis, perapihan sitasi Mendeley/Zotero (APA, IEEE, Harvard), dan bedah novelty artikel sebelum disubmit ke dewan redaksi jurnal.
                </p>
                <div class="mt-6 pt-4 border-t border-slate-100 text-xs text-slate-500 space-y-2">
                    <div>⏱ Pengerjaan: 2 - 4 hari kerja</div>
                    <div>🎯 Sertifikat resmi Proofreading Aether</div>
                </div>
            </div>
        </div>

        <!-- Banner Submission CTA -->
        <div class="mt-12 bg-gradient-to-r from-[#102654] to-[#1768B5] text-white rounded-3xl p-8 sm:p-12 shadow-xl flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h2 class="text-2xl sm:text-3xl font-bold">Punya Naskah Artikel Siap Review?</h2>
                <p class="text-slate-200 text-sm sm:text-base mt-2 max-w-xl">
                    Kirimkan draf artikelmu untuk screening awal gratis bersama tim editorial Aether Science Publisher.
                </p>
            </div>
            <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20ingin%20screening%20naskah%20artikel%20jurnal" target="_blank" class="px-8 py-3.5 rounded-2xl font-bold text-[#102654] bg-[#FFD978] hover:bg-[#F6C75A] transition shadow-lg shrink-0">
                Kirim Draf Naskah
            </a>
        </div>
    </div>
</section>
@endsection
