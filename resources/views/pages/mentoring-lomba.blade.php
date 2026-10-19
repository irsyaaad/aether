@extends('layouts.public')

@section('title', 'Mentoring Lomba Karya Tulis Ilmiah (LKTI) & PKM | Aether Data Lab')
@section('meta_description', 'Bimbingan juara kompetisi ilmiah: LKTI Nasional/Internasional, PKM Dikti tembus PIMNAS, Business Plan, dan Essay Competition bersama mentor berpengalaman peraih medali.')
@section('meta_keywords', 'mentoring lkti, bimbingan pkm dikti, juara pimnas, pembimbing karya tulis ilmiah, aether data lab')

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
            <span class="text-white">Mentoring Lomba</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-[#F6C75A] text-xs font-bold border border-amber-400/30 mb-4">
            🏆 Raih Prestasi &amp; Medali Juara Kompetisi Ilmiah
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Mentoring Lomba Karya Tulis &amp; PKM</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Bimbingan dari ideasi gagasan kreatif, validasi data, sistematika penulisan format juri, hingga teknik presentasi memukau di hadapan dewan juri.
        </p>
    </div>
</section>

<!-- Competition Categories -->
<section class="py-16 bg-[#F8FBFE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all">
                <div class="text-3xl mb-4">🥇</div>
                <h3 class="text-xl font-bold text-[#102654]">PKM &amp; PIMNAS Dikti</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Pendampingan Program Kreativitas Mahasiswa (PKM-RE, PKM-RSH, PKM-K, PKM-PM, PKM-PI, PKM-KC, PKM-VGK, PKM-GFT) agar lolos pendanaan dan melaju ke PIMNAS.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 border-2 border-[#2B8BD1] shadow-xl transition-all">
                <div class="text-3xl mb-4">💡</div>
                <h3 class="text-xl font-bold text-[#102654]">LKTI Nasional &amp; Internasional</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Bimbingan riset aplikatif, perumusan solusi solutif berbasis data ilmiah, serta review draft paper agar memenuhi kriteria penilaian dewan juri kompetisi.
                </p>
            </div>

            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all">
                <div class="text-3xl mb-4">🎤</div>
                <h3 class="text-xl font-bold text-[#102654]">Pitch Deck &amp; Presentation Coaching</h3>
                <p class="text-slate-600 text-sm mt-3">
                    Pembuatan slide presentasi visual resolusi tinggi, desain poster ilmiah, serta pelatihan public speaking dan simulasi tanya-jawab juri yang tajam.
                </p>
            </div>
        </div>

        <div class="mt-12 text-center">
            <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20mau%20konsultasi%20Mentoring%20Lomba%20LKTI%20PKM" target="_blank" class="inline-flex items-center gap-2 px-8 py-3.5 rounded-2xl font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] shadow-lg hover:opacity-95">
                <span>Konsultasi Ide Lomba</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </div>
</section>
@endsection
