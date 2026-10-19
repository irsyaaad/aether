@extends('layouts.public')

@section('title', 'AI Research & Academic Intelligence | Aether Data Lab')
@section('meta_description', 'Pemanfaatan AI untuk riset akademik secara etis & legal: systematic literature review berbasis AI, visualisasi sitasi bibliometrik VOSviewer, prompt engineering ilmiah, dan deteksi bias.')
@section('meta_keywords', 'ai research tools, literature review ai, vosviewer bibliometric, prompt engineering riset, ai etis akademik, aether data lab')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-[#102654] via-[#1768B5] to-[#102654] text-white py-16 lg:py-24 overflow-hidden">
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#58B7EA_1px,transparent_1px)] [background-size:24px_24px]" aria-hidden="true"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex justify-center items-center space-x-2 text-xs font-semibold text-sky-200 mb-4">
            <a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <span class="text-white">AI Research</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-sky-300 text-xs font-bold border border-white/15 mb-4">
            🤖 Ethical &amp; Academic Integrity Compliant AI
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">AI Research &amp; Academic Analytics</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Akselerasi riset ilmiah menggunakan kecerdasan buatan secara bertanggung jawab untuk pemetaan literatur, analisis bibliometrik, dan sintesis teori tanpa melanggar etika akademik.
        </p>
    </div>
</section>

<!-- AI Tools & Services -->
<section class="py-16 bg-[#F8FBFE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-2xl bg-sky-100 flex items-center justify-center text-[#2B8BD1] mb-5 font-bold text-xl">
                    🧠
                </div>
                <h3 class="text-xl font-bold text-[#102654]">Systematic Literature Review</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Eksplorasi ratusan paper Scopus &amp; WoS dalam hitungan menit. Ekstraksi otomatis sintesis matriks, perbandingan metodologi riset terdahulu, dan penemuan research gap yang valid.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 border-2 border-[#2B8BD1] shadow-xl transition-all">
                <div class="w-12 h-12 rounded-2xl bg-sky-500 text-white flex items-center justify-center mb-5 font-bold text-xl">
                    🌐
                </div>
                <h3 class="text-xl font-bold text-[#102654]">Bibliometrik &amp; VOSviewer Mapping</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Visualisasi jaringan sitasi, co-authorship, dan co-occurrence kata kunci menggunakan VOSviewer dan Biblioshiny untuk memetakan tren riset global paling mutakhir.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-[#F29B3A] mb-5 font-bold text-xl">
                    ⚖️
                </div>
                <h3 class="text-xl font-bold text-[#102654]">Etika &amp; Deteksi AI Hallucination</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Verifikasi sitasi palsu (halusinasi AI), uji orisinalitas argumen, dan panduan sitasi etis penggunaan AI pada naskah skripsi/tesis sesuai panduan resmi Ditjen Dikti.
                </p>
            </div>
        </div>

        <div class="mt-12 bg-white rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm flex flex-col md:flex-row items-center justify-between gap-6">
            <div>
                <h3 class="text-2xl font-bold text-[#102654]">Tertarik Belajar AI untuk Riset Akademik?</h3>
                <p class="text-slate-600 text-sm mt-1">Konsultasikan kebutuhan tools AI dan bibliometrik untuk riset tesis atau artikel jurnalmu.</p>
            </div>
            <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20tertarik%20dengan%20Layanan%20AI%20Research" target="_blank" class="px-8 py-3 rounded-2xl font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] shadow shrink-0">
                Konsultasi AI Research
            </a>
        </div>
    </div>
</section>
@endsection
