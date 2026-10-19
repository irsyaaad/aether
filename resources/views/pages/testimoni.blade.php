@extends('layouts.public')

@section('title', 'Testimoni & Kisah Sukses Klien | Aether Data Lab')
@section('meta_description', '1.200+ mahasiswa S1, S2, dan S3 telah sukses sidang skripsi, tesis, disertasi, dan publikasi jurnal bersama Aether Data Lab. Simak ulasan jujur dan kisah kelulusan mereka.')
@section('meta_keywords', 'testimoni aether data lab, ulasan olah data skripsi, review bimbingan tesis, kisah sukses sidang, aether data lab')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-[#102654] via-[#1768B5] to-[#102654] text-white py-16 lg:py-24 overflow-hidden">
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#58B7EA_1px,transparent_1px)] [background-size:24px_24px]" aria-hidden="true"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex justify-center items-center space-x-2 text-xs font-semibold text-sky-200 mb-4">
            <a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <span class="text-white">Testimoni</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-4">
            ⭐ 4.9/5 Rating Kepuasan dari 1.200+ Mahasiswa
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Kisah Sukses &amp; Ulasan Klien</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Cerita nyata dari mahasiswa dan peneliti yang berhasil melewati masa-masa krisis skripsi, tesis, dan publikasi jurnal bersama tim mentor Aether Data Lab.
        </p>
    </div>
</section>

<!-- Testimonials Grid -->
<section class="py-16 bg-[#F8FBFE]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Testimoni 1 -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-1 text-amber-400 mb-4">
                        ★★★★★
                    </div>
                    <p class="text-slate-700 text-sm leading-relaxed italic">
                        "Sempat stuck 4 bulan karena model SEM-PLS di SmartPLS nilai AVE-nya merah semua. Mentor Aether ngebimbing step-by-step penghapusan indikator bermasalah dan ngajarin cara jelasin ke dosen. Alhamdulillah pas sidang dinyatakan Lulus Cumlaude!"
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-sky-100 text-[#1768B5] font-extrabold flex items-center justify-center">
                        FA
                    </div>
                    <div>
                        <div class="font-bold text-sm text-[#102654]">Fadhil Ardian</div>
                        <div class="text-xs text-slate-500">S1 Manajemen — Universitas Gadjah Mada</div>
                    </div>
                </div>
            </div>

            <!-- Testimoni 2 -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-1 text-amber-400 mb-4">
                        ★★★★★
                    </div>
                    <p class="text-slate-700 text-sm leading-relaxed italic">
                        "Data panel di EViews kena heteroskedastisitas dan autokorelasi berulang kali. Tim Aether bener-bener telaten kasih opsi GLS dan robust standard error. Yang paling ngebantu adalah sesi simulasi sidangnya, penguji nanya persis seperti yang dilatih mentor!"
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-100 text-[#B36A12] font-extrabold flex items-center justify-center">
                        RN
                    </div>
                    <div>
                        <div class="font-bold text-sm text-[#102654]">Rania Nadhira, M.Ak.</div>
                        <div class="text-xs text-slate-500">Magister Akuntansi — Universitas Indonesia</div>
                    </div>
                </div>
            </div>

            <!-- Testimoni 3 -->
            <div class="bg-white rounded-3xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all flex flex-col justify-between">
                <div>
                    <div class="flex items-center gap-1 text-amber-400 mb-4">
                        ★★★★★
                    </div>
                    <p class="text-slate-700 text-sm leading-relaxed italic">
                        "Sebagai mahasiswa S3 yang bekerja, waktu saya sangat terbatas. Pendampingan NVivo dan systematic review di Aether sangat sistematis. Paper saya tembus Scopus Q2 dalam waktu 3 bulan sejak submit. Pelayanan cepat dan sangat profesional."
                    </p>
                </div>
                <div class="mt-6 pt-4 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 font-extrabold flex items-center justify-center">
                        DR
                    </div>
                    <div>
                        <div class="font-bold text-sm text-[#102654]">Dr. Hendra Wijaya</div>
                        <div class="text-xs text-slate-500">Doktoral Ilmu Pendidikan — Universitas Negeri Yogyakarta</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Video & Photo Proof Banner -->
        <div class="mt-12 bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 text-center shadow-sm">
            <h3 class="text-2xl font-bold text-[#102654]">Ingin Menjadi Kisah Sukses Berikutnya?</h3>
            <p class="text-slate-600 text-sm mt-2 max-w-xl mx-auto">
                Jangan biarkan tugas akhir menunda kelulusan dan karir masa depanmu. Konsultasikan judul dan data risetmu sekarang juga!
            </p>
            <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20ingin%20konsultasi%20bimbingan%20skripsi/tesis" target="_blank" class="mt-6 inline-block px-8 py-3.5 rounded-2xl font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] shadow-md hover:opacity-95">
                Konsultasi Bebas Biaya Sekarang
            </a>
        </div>
    </div>
</section>
@endsection
