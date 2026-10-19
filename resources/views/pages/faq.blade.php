@extends('layouts.public')

@section('title', 'Frequently Asked Questions (FAQ) | Aether Data Lab')
@section('meta_description', 'Pertanyaan yang sering diajukan mengenai layanan olah data statistik, bimbingan skripsi, tesis, garansi revisi dosen, metode pembayaran, dan keamanan data di Aether Data Lab.')
@section('meta_keywords', 'faq olah data, tanya jawab skripsi tesis, garansi revisi aether, alur bimbingan olah data, aether data lab')

@section('content')
<!-- Hero Section -->
<section class="relative bg-gradient-to-b from-[#102654] via-[#1768B5] to-[#102654] text-white py-16 lg:py-24 overflow-hidden">
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#58B7EA_1px,transparent_1px)] [background-size:24px_24px]" aria-hidden="true"></div>
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <nav class="flex justify-center items-center space-x-2 text-xs font-semibold text-sky-200 mb-4">
            <a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a>
            <span>/</span>
            <span class="text-white">FAQ</span>
        </nav>
        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15 mb-4">
            💡 Pusat Bantuan &amp; Informasi
        </span>
        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight">Frequently Asked Questions (FAQ)</h1>
        <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto">
            Temukan jawaban lengkap seputar alur bimbingan, sistem garansi revisi, durasi pengerjaan, dan legalitas integritas akademik di Aether Data Lab.
        </p>
    </div>
</section>

<!-- FAQ Accordion Section -->
<section class="py-16 bg-[#F8FBFE]" x-data="{ openFaq: 1 }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">
        <!-- FAQ 1 -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm transition">
            <button @click="openFaq = openFaq === 1 ? null : 1" class="w-full text-left px-6 py-5 flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-[#102654] hover:text-[#2B8BD1] transition">
                <span>1. Bagaimana alur memulai bimbingan atau olah data di Aether Data Lab?</span>
                <span class="text-xl transition-transform duration-200" :class="{ 'rotate-180': openFaq === 1 }">▼</span>
            </button>
            <div x-show="openFaq === 1" x-collapse class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-100 pt-4">
                Alurnya sangat mudah: (1) Hubungi admin via WhatsApp dan kirimkan proposal/bab 1-3 serta tabulasi datamu, (2) Tim konsultan kami melakukan diagnosis metodologi dan estimasi durasi, (3) Diskusi kesepakatan paket &amp; pembayaran DP, (4) Pengerjaan olah data dan interpretasi bab 4, (5) Sesi bimbingan 1-on-1 penjelasan output hingga kamu paham, (6) Garansi pendampingan revisi jika ada catatan dari dosen pembimbing/penguji.
            </div>
        </div>

        <!-- FAQ 2 -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm transition">
            <button @click="openFaq = openFaq === 2 ? null : 2" class="w-full text-left px-6 py-5 flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-[#102654] hover:text-[#2B8BD1] transition">
                <span>2. Apakah ada garansi jika dosen pembimbing meminta revisi?</span>
                <span class="text-xl transition-transform duration-200" :class="{ 'rotate-180': openFaq === 2 }">▼</span>
            </button>
            <div x-show="openFaq === 2" x-collapse class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-100 pt-4">
                <strong>Ya, 100% Bergaransi Revisi!</strong> Kami mendampingi perbaikan analisis data selama masih sesuai dengan hipotesis dan ruang lingkup awal tanpa ada biaya tambahan hingga naskah skripsi/tesis kamu disetujui dosen dan lulus ujian sidang.
            </div>
        </div>

        <!-- FAQ 3 -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm transition">
            <button @click="openFaq = openFaq === 3 ? null : 3" class="w-full text-left px-6 py-5 flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-[#102654] hover:text-[#2B8BD1] transition">
                <span>3. Berapa lama proses pengerjaan olah data statistik?</span>
                <span class="text-xl transition-transform duration-200" :class="{ 'rotate-180': openFaq === 3 }">▼</span>
            </button>
            <div x-show="openFaq === 3" x-collapse class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-100 pt-4">
                Untuk layanan reguler, proses pengerjaan rata-rata memakan waktu <strong>2 hingga 4 hari kerja</strong>. Kami juga menyediakan <strong>Layanan Kilat / Fast-Track</strong> (1 x 24 jam) bagi kamu yang memiliki tenggat waktu mepet untuk daftar ujian sidang.
            </div>
        </div>

        <!-- FAQ 4 -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm transition">
            <button @click="openFaq = openFaq === 4 ? null : 4" class="w-full text-left px-6 py-5 flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-[#102654] hover:text-[#2B8BD1] transition">
                <span>4. Apakah kerahasiaan dan hak cipta data penelitian saya aman?</span>
                <span class="text-xl transition-transform duration-200" :class="{ 'rotate-180': openFaq === 4 }">▼</span>
            </button>
            <div x-show="openFaq === 4" x-collapse class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-100 pt-4">
                Kerahasiaan data adalah prioritas utama kami. Seluruh dataset kuesioner, transkrip wawancara, dan naskah penelitian klien dilindungi kebijakan privasi non-disclosure (NDA) yang ketat. Data tidak akan pernah disebarluaskan, dijual, atau digunakan untuk keperluan lain.
            </div>
        </div>

        <!-- FAQ 5 -->
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm transition">
            <button @click="openFaq = openFaq === 5 ? null : 5" class="w-full text-left px-6 py-5 flex items-center justify-between gap-4 font-bold text-base sm:text-lg text-[#102654] hover:text-[#2B8BD1] transition">
                <span>5. Bagaimana sistem bimbingan dan penjelasannya jika saya belum paham statistik?</span>
                <span class="text-xl transition-transform duration-200" :class="{ 'rotate-180': openFaq === 5 }">▼</span>
            </button>
            <div x-show="openFaq === 5" x-collapse class="px-6 pb-6 text-slate-600 text-sm leading-relaxed border-t border-slate-100 pt-4">
                Kamu tidak perlu khawatir! Tim mentor Aether mengajarkan mulai dari nol: cara membaca tabel angka output, arti signifikansi p-value, logika penerimaan/penolakan hipotesis, hingga simulasi tanya jawab dosen penguji secara interaktif via Google Meet / Zoom sampai kamu benar-benar percaya diri.
            </div>
        </div>
    </div>

    <!-- Contact Support CTA -->
    <div class="mt-12 text-center max-w-xl mx-auto px-4">
        <p class="text-slate-600 text-sm">Masih punya pertanyaan lain yang belum terjawab di sini?</p>
        <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20mau%20tanya%20seputar%20layanan%20olah%20data" target="_blank" class="mt-4 inline-flex items-center gap-2 px-6 py-3 rounded-2xl font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] shadow hover:opacity-95 text-sm">
            <span>Tanya Langsung ke Tim Aether</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
        </a>
    </div>
</section>
@endsection
