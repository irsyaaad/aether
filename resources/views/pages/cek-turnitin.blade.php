@extends('layouts.public')

@section('title', 'Cek Plagiarisme Turnitin No Repository & Parafrase | Aether Data Lab')
@section('meta_description', 'Layanan cek Turnitin resmi No Repository (file aman 100% tidak tersimpan) dengan hasil PDF berwarna cepat 5 menit, serta layanan parafrase ilmiah lolos similarity di bawah 20%.')
@section('meta_keywords', 'cek turnitin murah, turnitin no repository, jasa parafrase skripsi, turunkan similarity turnitin, aether data lab')

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
            <span class="text-white">Cek Turnitin</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-400/20 text-emerald-300 text-xs font-bold border border-emerald-400/30 mb-4">
            🛡️ 100% Akun Resmi Turnitin No Repository
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Cek Plagiarisme &amp; Parafrase Ilmiah</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Pastikan karya ilmiahmu bebas plagiarisme sebelum sidang atau submit jurnal. File tidak akan masuk database repository sehingga aman dicek berulang kali.
        </p>
    </div>
</section>

<!-- Turnitin Services -->
<section class="py-16 bg-[#F8FBFE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-2 gap-8">
            <!-- Cek Turnitin Cepat -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all">
                <div class="inline-block px-3 py-1 bg-sky-100 text-[#1768B5] rounded-full text-xs font-bold uppercase mb-4">
                    Hasil Cepat 5-10 Menit
                </div>
                <h3 class="text-2xl font-bold text-[#102654]">Cek Turnitin No Repository</h3>
                <p class="text-slate-600 text-sm mt-3 leading-relaxed">
                    Kirimkan dokumen Word / PDF skripsi, tesis, atau naskah jurnalmu. Sistem kami akan memproses ke akun instruktur resmi tanpa menyimpan naskah ke database Turnitin.
                </p>
                <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 text-xs text-slate-700">
                    <div class="flex items-center justify-between">
                        <span>Laporan Digital PDF Berwarna</span>
                        <span class="font-bold text-emerald-600">Include</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Detail Sumber Plagiat &amp; Link</span>
                        <span class="font-bold text-emerald-600">Include</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Filter Kutipan &amp; Daftar Pustaka</span>
                        <span class="font-bold text-emerald-600">Aktif</span>
                    </div>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20mau%20Cek%20Turnitin%20No%20Repository" target="_blank" class="mt-8 block w-full text-center py-3 rounded-2xl font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] shadow-md hover:opacity-95">
                    Kirim Dokumen via WhatsApp
                </a>
            </div>

            <!-- Parafrase Ilmiah -->
            <div class="bg-white rounded-3xl p-8 border-2 border-[#F29B3A] shadow-xl transition-all relative">
                <div class="inline-block px-3 py-1 bg-amber-100 text-[#F29B3A] rounded-full text-xs font-bold uppercase mb-4">
                    Garansi Similarity &lt; 20%
                </div>
                <h3 class="text-2xl font-bold text-[#102654]">Layanan Parafrase Ilmiah</h3>
                <p class="text-slate-600 text-sm mt-3 leading-relaxed">
                    Similarity naskah kamu merah atau melebihi batas toleransi kampus? Tim editor Aether akan merombak struktur kalimat secara manual tanpa mengubah makna konsep penelitian.
                </p>
                <div class="mt-6 p-4 rounded-2xl bg-amber-50/50 border border-amber-100 space-y-2 text-xs text-slate-700">
                    <div class="flex items-center justify-between">
                        <span>Parafrase Human-Edited (Bukan Bot)</span>
                        <span class="font-bold text-emerald-600">100% Manual</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Garansi Lolos Uji Kampus</span>
                        <span class="font-bold text-emerald-600">Bergaransi</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Bonus Cek Ulang Turnitin</span>
                        <span class="font-bold text-emerald-600">Gratis 2x</span>
                    </div>
                </div>
                <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20mau%20konsultasi%20Parafrase%20Similarity%20Turnitin" target="_blank" class="mt-8 block w-full text-center py-3 rounded-2xl font-bold text-white bg-gradient-to-r from-[#F29B3A] to-[#F6C75A] shadow-md hover:opacity-95">
                    Konsultasi Parafrase
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
