@extends('layouts.public')

@section('title', 'Promo Program & Diskon Bimbingan Olah Data | Aether Data Lab')
@section('meta_description', 'Klaim promo spesial diskon bimbingan olah data skripsi, tesis, cek Turnitin gratis, dan voucher publikasi jurnal ilmiah di Aether Data Lab. Kuota terbatas!')
@section('meta_keywords', 'promo olah data, diskon bimbingan skripsi, voucher turnitin, promo paket tesis, aether data lab')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-[#102654] via-[#1768B5] to-[#102654] text-white py-16 lg:py-24 overflow-hidden">
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#58B7EA_1px,transparent_1px)] [background-size:24px_24px]" aria-hidden="true"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex justify-center items-center space-x-2 text-xs font-semibold text-sky-200 mb-4">
            <a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <span class="text-white">Promo Program</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-[#F6C75A] text-xs font-bold border border-amber-400/30 mb-4">
            🔥 Flash Promo Periode Sidang &amp; Riset 2026
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Promo &amp; Paket Spesial Riset</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Dapatkan penawaran terbaik untuk akselerasi skripsi, tesis, disertasi, dan publikasi ilmiah dengan garansi bimbingan sampai tuntas.
        </p>
    </div>
</section>

<!-- Promo Cards Section -->
<section class="py-16 bg-[#F8FBFE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Promo 1 -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 bg-rose-500 text-white text-xs font-bold px-4 py-1.5 rounded-bl-2xl">
                    Hemat 35%
                </div>
                <div>
                    <span class="text-xs font-extrabold text-[#2B8BD1] tracking-wider uppercase">Paket Bundling Kilat</span>
                    <h3 class="text-2xl font-bold text-[#102654] mt-2">Olah Data + Simulasi Sidang</h3>
                    <p class="text-slate-600 text-sm mt-3">
                        Lengkap dengan olah data statistik (SPSS/SmartPLS/EViews), interpretasi bab 4, plus sesi privat 1-on-1 simulasi tanya jawab penguji sidang.
                    </p>
                    <div class="mt-6 p-4 rounded-2xl bg-sky-50 border border-sky-100">
                        <div class="text-xs text-slate-500 line-through">Rp 850.000</div>
                        <div class="text-3xl font-extrabold text-[#1768B5]">Rp 549.000</div>
                        <div class="text-xs text-slate-500 mt-1">Kode: <span class="font-mono font-bold text-[#102654]">AETHERSIDANG</span></div>
                    </div>
                    <ul class="mt-6 space-y-2.5 text-xs text-slate-600">
                        <li class="flex items-center gap-2">✅ Garansi revisi dosen penguji</li>
                        <li class="flex items-center gap-2">✅ Video tutorial penjelasan output</li>
                        <li class="flex items-center gap-2">✅ Lembar simulasi Q&amp;A sidang</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20mau%20klaim%20Promo%20Bundling%20AETHERSIDANG" target="_blank" class="mt-8 block w-full text-center py-3 rounded-2xl font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:opacity-95 shadow-md">
                    Klaim Promo Ini
                </a>
            </div>

            <!-- Promo 2 -->
            <div class="bg-white rounded-3xl p-8 border-2 border-[#F29B3A] shadow-xl relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 bg-[#F29B3A] text-white text-xs font-bold px-4 py-1.5 rounded-bl-2xl">
                    Paling Populer
                </div>
                <div>
                    <span class="text-xs font-extrabold text-[#F29B3A] tracking-wider uppercase">Paket Publikasi</span>
                    <h3 class="text-2xl font-bold text-[#102654] mt-2">Free Cek Turnitin + Review Jurnal</h3>
                    <p class="text-slate-600 text-sm mt-3">
                        Setiap pendaftaran olah data atau mentoring artikel jurnal, dapatkan gratis 2x cek plagiarisme Turnitin No Repository &amp; screening kesesuaian template.
                    </p>
                    <div class="mt-6 p-4 rounded-2xl bg-amber-50 border border-amber-100">
                        <div class="text-xs text-slate-500 line-through">Rp 450.000</div>
                        <div class="text-3xl font-extrabold text-[#F29B3A]">GRATIS (Bonus)</div>
                        <div class="text-xs text-slate-500 mt-1">Kode: <span class="font-mono font-bold text-[#102654]">AETHERFREEPASS</span></div>
                    </div>
                    <ul class="mt-6 space-y-2.5 text-xs text-slate-600">
                        <li class="flex items-center gap-2">✅ Cek Turnitin No Repository resmi</li>
                        <li class="flex items-center gap-2">✅ Rekomendasi perbaikan parafrase</li>
                        <li class="flex items-center gap-2">✅ Pengecekan sitasi &amp; daftar pustaka</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20mau%20klaim%20Free%20Turnitin%20AETHERFREEPASS" target="_blank" class="mt-8 block w-full text-center py-3 rounded-2xl font-bold text-white bg-gradient-to-r from-[#F29B3A] to-[#F6C75A] hover:opacity-95 shadow-md">
                    Klaim Bonus Ini
                </a>
            </div>

            <!-- Promo 3 -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all relative overflow-hidden flex flex-col justify-between">
                <div class="absolute top-0 right-0 bg-emerald-500 text-white text-xs font-bold px-4 py-1.5 rounded-bl-2xl">
                    Promo Rombongan
                </div>
                <div>
                    <span class="text-xs font-extrabold text-emerald-600 tracking-wider uppercase">Daftar Bareng Teman</span>
                    <h3 class="text-2xl font-bold text-[#102654] mt-2">Diskon Tim &amp; Laboratorium</h3>
                    <p class="text-slate-600 text-sm mt-3">
                        Daftar mentoring penelitian atau olah data berdua atau bertiga dengan teman satu angkatan/lab. Dapatkan potongan langsung hingga Rp 200.000 per orang.
                    </p>
                    <div class="mt-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-100">
                        <div class="text-xs text-slate-500">Potongan Langsung</div>
                        <div class="text-3xl font-extrabold text-emerald-600">Hingga 40%</div>
                        <div class="text-xs text-slate-500 mt-1">Kode: <span class="font-mono font-bold text-[#102654]">AETHERSQUAD</span></div>
                    </div>
                    <ul class="mt-6 space-y-2.5 text-xs text-slate-600">
                        <li class="flex items-center gap-2">✅ Berlaku untuk 2 orang atau lebih</li>
                        <li class="flex items-center gap-2">✅ Sesi konsultasi privat terpisah</li>
                        <li class="flex items-center gap-2">✅ Akses materi workshop gratis</li>
                    </ul>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20mau%20tanya%20Promo%20Squad%20AETHERSQUAD" target="_blank" class="mt-8 block w-full text-center py-3 rounded-2xl font-bold text-white bg-[#102654] hover:bg-[#1768B5] transition shadow-md">
                    Konsultasi Promo Tim
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
