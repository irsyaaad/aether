@extends('layouts.public')

@section('title', 'Mentoring Lolos Beasiswa S2 & S3 | Aether Data Lab')
@section('meta_description', 'Bimbingan intensif lolos beasiswa bergengsi: LPDP, Chevening, AAS, Fulbright, MEXT, dan BIM. Bedah esai, study plan, research proposal, dan mock interview bersama awardee.')
@section('meta_keywords', 'mentoring beasiswa lpdp, bedah essay lpdp, mock interview beasiswa, tips lolos chevening aas, aether data lab')

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
            <span class="text-white">Mentoring Beasiswa</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#F6C75A] text-xs font-bold border border-white/15 mb-4">
            🌍 Pendampingan Lolos Beasiswa Dalam &amp; Luar Negeri
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Mentoring Beasiswa S2 &amp; S3</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Wujudkan impian studi lanjut ke kampus top dunia bersama mentor alumni dan awardee beasiswa bergengsi (LPDP, AAS, Chevening, Fulbright).
        </p>
    </div>
</section>

<!-- Scholarships Focus -->
<section class="py-16 bg-[#F8FBFE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all">
                <div class="text-3xl mb-4">✍️</div>
                <h3 class="text-xl font-bold text-[#102654]">Bedah Essay &amp; Personal Statement</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Bimbingan menyusun narasi esai kontribusi, personal statement, dan rencana karir yang tajam, autentik, dan menyentuh nilai-nilai utama yang dicari komite beasiswa.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 border-2 border-[#2B8BD1] shadow-xl transition-all">
                <div class="text-3xl mb-4">🔬</div>
                <h3 class="text-xl font-bold text-[#102654]">Research Proposal Review</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Spesialisasi bagi pendaftar riset magister atau doktoral. Tim konsultan metodologi Aether membantu mematangkan rumusan masalah, research gap, dan justifikasi metodologi riset.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all">
                <div class="text-3xl mb-4">🎯</div>
                <h3 class="text-xl font-bold text-[#102654]">Mock Interview Panelis</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Simulasi wawancara substansi akademis dan psikologi secara intensif. Kupas tuntas cara menjawab pertanyaan jebakan panelis dengan percaya diri.
                </p>
            </div>
        </div>

        <div class="mt-12 bg-white rounded-3xl p-8 border border-slate-200 text-center max-w-3xl mx-auto shadow-sm">
            <h3 class="text-xl font-bold text-[#102654]">Siap Menjadi Awardee Beasiswa Selanjutnya?</h3>
            <p class="text-slate-600 text-sm mt-2">Daftar sekarang untuk sesi review awal profil dan esai beasiswamu.</p>
            <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20mau%20konsultasi%20Mentoring%20Beasiswa" target="_blank" class="mt-6 inline-block px-8 py-3 rounded-2xl font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] shadow">
                Hubungi Mentor Beasiswa
            </a>
        </div>
    </div>
</section>
@endsection
