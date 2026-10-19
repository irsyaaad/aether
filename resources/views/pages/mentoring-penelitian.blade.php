@extends('layouts.public')

@section('title', 'Mentoring Penelitian: Skripsi, Tesis, & Disertasi | Aether Data Lab')
@section('meta_description', 'Program pendampingan dan bimbingan 1-on-1 skripsi, tesis, dan disertasi bersama mentor ahli: bedah topik & novelty, penulisan proposal Bab 1-3, metodologi riset, analisis Bab 4, hingga simulasi sidang.')
@section('meta_keywords', 'mentoring skripsi, bimbingan tesis, pembimbing disertasi, konsultasi bab 4, simulasi ujian sidang, aether data lab')

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
            <span class="text-white">Mentoring Penelitian</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-4">
            🎯 Pendampingan 1-on-1 Bersama Konsultan Metodologi
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Mentoring Penelitian Akademik</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Bimbingan personal untuk mahasiswa S1, S2, dan S3. Atasi kebuntuan riset mulai dari penentuan topik hingga lulus ujian sidang dengan nilai terbaik.
        </p>
    </div>
</section>

<!-- Mentoring Steps & Levels -->
<section class="py-16 bg-[#F8FBFE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Jenjang S1 -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <span class="px-3 py-1 bg-sky-100 text-[#1768B5] rounded-full text-xs font-bold uppercase">Program S1</span>
                    <h3 class="text-2xl font-bold text-[#102654] mt-3">Mentoring Skripsi</h3>
                    <p class="text-slate-600 text-sm mt-3">
                        Fokus pada perumusan masalah, kuesioner, metodologi kuantitatif/kualitatif, analisis data bab 4, dan persiapan ujian komprehensif.
                    </p>
                    <ul class="mt-6 space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-4">
                        <li>✓ Bedah revisi dosen pembimbing</li>
                        <li>✓ Pemahaman logika antar-variabel</li>
                        <li>✓ Simulasi tanya jawab sidang 1-on-1</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20butuh%20Mentoring%20Skripsi%20S1" target="_blank" class="mt-8 block w-full text-center py-2.5 rounded-xl font-bold text-xs text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] shadow">
                    Konsultasi Skripsi
                </a>
            </div>

            <!-- Jenjang S2 -->
            <div class="bg-white rounded-3xl p-8 border-2 border-[#2B8BD1] shadow-xl transition-all flex flex-col justify-between relative">
                <div class="absolute -top-3.5 right-6 bg-[#2B8BD1] text-white text-[10px] font-extrabold uppercase px-3 py-1 rounded-full">
                    Intensif
                </div>
                <div>
                    <span class="px-3 py-1 bg-sky-100 text-[#1768B5] rounded-full text-xs font-bold uppercase">Program S2</span>
                    <h3 class="text-2xl font-bold text-[#102654] mt-3">Mentoring Tesis</h3>
                    <p class="text-slate-600 text-sm mt-3">
                        Pendalaman theoretical framework, gap riset empiris, pemodelan multivariat kompleks (SEM-PLS, CB-SEM, Ekonometrika Panel), dan persiapan publikasi jurnal.
                    </p>
                    <ul class="mt-6 space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-4">
                        <li>✓ Novelty &amp; state-of-the-art riset</li>
                        <li>✓ Uji model struktural bertingkat</li>
                        <li>✓ Garansi pendampingan sampai wisuda</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20butuh%20Mentoring%20Tesis%20S2" target="_blank" class="mt-8 block w-full text-center py-2.5 rounded-xl font-bold text-xs text-white bg-[#102654] hover:bg-[#1768B5] transition shadow">
                    Konsultasi Tesis
                </a>
            </div>

            <!-- Jenjang S3 -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <span class="px-3 py-1 bg-amber-100 text-[#F29B3A] rounded-full text-xs font-bold uppercase">Program S3</span>
                    <h3 class="text-2xl font-bold text-[#102654] mt-3">Mentoring Disertasi</h3>
                    <p class="text-slate-600 text-sm mt-3">
                        Konsultasi tingkat lanjut bagi kandidat doktor. Pengembangan model baru, validasi pakar, mixed methods, serta persiapan sidang promosi doktor dan publikasi Scopus.
                    </p>
                    <ul class="mt-6 space-y-2 text-xs text-slate-600 border-t border-slate-100 pt-4">
                        <li>✓ Konsultan bergelar Doktor / Post-Doc</li>
                        <li>✓ Systematic literature review &amp; bibliometrik</li>
                        <li>✓ Bantuan penulisan artikel Scopus Q1/Q2</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20butuh%20Mentoring%20Disertasi%20S3" target="_blank" class="mt-8 block w-full text-center py-2.5 rounded-xl font-bold text-xs text-[#102654] bg-[#FFD978] hover:bg-[#F6C75A] transition shadow">
                    Konsultasi Disertasi
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
