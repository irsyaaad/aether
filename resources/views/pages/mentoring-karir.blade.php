@extends('layouts.public')

@section('title', 'Mentoring Persiapan Karir Profesional & Data | Aether Data Lab')
@section('meta_description', 'Program mentoring persiapan karir: bedah CV/Resume ATS-friendly standar internasional, optimalisasi profil LinkedIn, simulasi mock interview HR & User, dan portfolio project data analyst.')
@section('meta_keywords', 'mentoring karir, review cv ats, mock interview kerja, karir data analyst, personal branding linkedin, aether data lab')

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
            <span class="text-white">Mentoring Karir</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-4">
            💼 Siap Masuk Dunia Kerja &amp; Industri Global
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Mentoring Persiapan Karir</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Jembatan dari lulusan sarjana menuju karir impian. Dapatkan bimbingan resume ATS-friendly, mock interview dengan praktisi industri, dan portofolio data riil.
        </p>
    </div>
</section>

<!-- Career Services Grid -->
<section class="py-16 bg-[#F8FBFE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-2xl bg-sky-100 flex items-center justify-center text-[#2B8BD1] mb-5 font-bold text-xl">
                    📄
                </div>
                <h3 class="text-xl font-bold text-[#102654]">Review CV ATS &amp; Cover Letter</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Transformasi CV menjadi format ATS-friendly yang lolos sistem screening perusahaan multinasional dan BUMN. Penataan bullet points berbasis action verbs dan angka metrik pencapaian.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 border-2 border-[#2B8BD1] shadow-xl transition-all">
                <div class="w-12 h-12 rounded-2xl bg-sky-500 text-white flex items-center justify-center mb-5 font-bold text-xl">
                    🎙️
                </div>
                <h3 class="text-xl font-bold text-[#102654]">Mock Interview HR &amp; User</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Simulasi wawancara kerja 1-on-1 dengan metode STAR (Situation, Task, Action, Result). Dilengkapi evaluasi body language, jawaban teknis, dan feedback tertulis komprehensif.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center text-[#F29B3A] mb-5 font-bold text-xl">
                    📊
                </div>
                <h3 class="text-xl font-bold text-[#102654]">Portofolio Data &amp; Research</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Bangun portofolio analisis data nyata menggunakan dataset bisnis, visualisasi Tableau/Power BI, atau case study riset pasar yang memikat para recruiter industri.
                </p>
            </div>
        </div>

        <div class="mt-12 text-center">
            <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20tertarik%20dengan%20Mentoring%20Persiapan%20Karir" target="_blank" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] shadow-lg hover:opacity-95">
                <span>Mulai Konsultasi Karir</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>
</section>
@endsection
