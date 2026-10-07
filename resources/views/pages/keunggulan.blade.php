@extends('layouts.public')

@section('title', 'Keunggulan & Metodologi Olah Data Riset | Aether Data Lab')
@section('meta_description', 'Mengapa mempercayakan olah data riset kepada Aether Data Lab? 1.200+ klien terbantu, mentor lulusan pascasarjana statistika & metodologi, garansi revisi dosen, dan penjelasan sampai paham.')
@section('meta_keywords', 'keunggulan aether data lab, jasa olah data terpercaya, bimbingan statistik skripsi, mentor metodologi riset, garansi revisi olah data')
@section('og_image', 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=1200&h=630&q=80')

@push('schema_json')
<script type="application/ld+json">
{!! json_encode([
  '@context' => 'https://schema.org',
  '@graph' => [
    [
      '@type' => 'BreadcrumbList',
      'itemListElement' => [
        [
          '@type' => 'ListItem',
          'position' => 1,
          'name' => 'Beranda',
          'item' => url('/'),
        ],
        [
          '@type' => 'ListItem',
          'position' => 2,
          'name' => 'Keunggulan Kami',
          'item' => url('/keunggulan'),
        ],
      ],
    ],
    [
      '@type' => 'AboutPage',
      '@id' => url('/keunggulan') . '#about',
      'name' => 'Keunggulan & Standar Kualitas Aether Data Lab',
      'url' => url('/keunggulan'),
      'description' => 'Standar kualitas analisis data Aether Data Lab mencakup tingkat kelulusan sidang 99.4%, 35+ tools analisis kuantitatif dan kualitatif, serta garansi revisi bebas biaya.',
      'mainEntity' => [
        '@type' => 'Organization',
        'name' => 'Aether Data Lab',
        'url' => url('/'),
        'knowsAbout' => [
          'Partial Least Squares SEM (SmartPLS)',
          'Covariance Based SEM (AMOS/LISREL)',
          'Ekonometrika & Regresi Panel (EViews/Stata)',
          'Analisis Data Kualitatif (NVivo/ATLAS.ti)',
          'Statistik Non-Parametrik & Uji Beda',
        ],
      ],
    ],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<!-- Hero Keunggulan -->
<section class="relative bg-gradient-to-b from-[#102654] via-[#1768B5] to-[#102654] text-white py-20 lg:py-28 overflow-hidden">
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#58B7EA_1px,transparent_1px)] [background-size:24px_24px]" aria-hidden="true"></div>
    <div class="absolute -left-32 -bottom-32 w-96 h-96 rounded-full bg-[#58B7EA]/20 blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center lg:text-left">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-8 space-y-5">
                <nav class="flex justify-center lg:justify-start items-center space-x-2 text-xs font-semibold text-sky-200">
                    <a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a>
                    <span>/</span>
                    <span class="text-white">Keunggulan &amp; Standar Mutu</span>
                </nav>

                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15">
                    <span class="w-2 h-2 rounded-full bg-[#F6C75A] animate-pulse"></span>
                    Standar Metodologi &amp; Integritas Akademik
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                    Bukan Sekadar Olah Data, <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#F6C75A] via-[#58B7EA] to-white">Kami Bimbing Sampai Lulus Sidang</span>
                </h1>

                <p class="text-base sm:text-lg text-sky-100 max-w-2xl leading-relaxed">
                    Di Aether Data Lab, kami percaya bahwa riset yang baik berawal dari pemahaman yang kuat. Kami tidak hanya menyerahkan output software yang membingungkan, tetapi mengajarkan logika pembacaan tabel dan pertanggungjawaban ilmiahnya.
                </p>

                <div class="pt-4 flex flex-wrap justify-center lg:justify-start gap-3">
                    <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20tertarik%20dengan%20pendampingan%20riset%20Aether" target="_blank" class="px-6 py-3.5 rounded-xl font-bold text-sm text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-lg shadow-amber-500/20 transition">
                        Konsultasi dengan Mentor Ahli
                    </a>
                    <a href="{{ url('/layanan') }}" class="px-6 py-3.5 rounded-xl font-bold text-sm text-white bg-white/10 hover:bg-white/20 border border-white/20 transition">
                        Eksplorasi Layanan Kami
                    </a>
                </div>
            </div>

            <!-- Operational Metrics Card -->
            <div class="lg:col-span-4">
                <div class="grid grid-cols-2 gap-3.5">
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 p-5 rounded-3xl text-center">
                        <div class="text-3xl sm:text-4xl font-black text-[#F6C75A]">1.200+</div>
                        <div class="text-xs font-bold text-white mt-1">Klien Tuntas</div>
                        <div class="text-[11px] text-sky-200 mt-0.5">Skripsi, Tesis, Disertasi</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 p-5 rounded-3xl text-center">
                        <div class="text-3xl sm:text-4xl font-black text-emerald-300">99.4%</div>
                        <div class="text-xs font-bold text-white mt-1">Lolos Sidang</div>
                        <div class="text-[11px] text-sky-200 mt-0.5">Ujian Tugas Akhir</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 p-5 rounded-3xl text-center">
                        <div class="text-3xl sm:text-4xl font-black text-[#58B7EA]">35+</div>
                        <div class="text-xs font-bold text-white mt-1">Metode &amp; Tools</div>
                        <div class="text-[11px] text-sky-200 mt-0.5">Kuantitatif &amp; Kualitatif</div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md border border-white/15 p-5 rounded-3xl text-center">
                        <div class="text-3xl sm:text-4xl font-black text-amber-200">4.9/5</div>
                        <div class="text-xs font-bold text-white mt-1">Kepuasan Klien</div>
                        <div class="text-[11px] text-sky-200 mt-0.5">Ulasan Terverifikasi</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- 6 Pilar Keunggulan Aether -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-widest text-[#2B8BD1]">Value Proposition Kami</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-[#102654] mt-2">
                6 Alasan Memilih Aether Data Lab
            </h2>
            <p class="text-slate-600 mt-3 text-sm sm:text-base">
                Komitmen kami adalah kenyamanan belajar dan kepastian kelulusan Anda dengan standar akademis tinggi.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Pilar 1 -->
            <div class="p-8 rounded-3xl bg-[#F8FBFE] border border-slate-200/80 hover:border-[#2B8BD1] transition-all group">
                <div class="w-14 h-14 rounded-2xl bg-sky-100 text-[#1768B5] flex items-center justify-center font-black text-xl mb-6 group-hover:scale-110 transition-transform">
                    💡
                </div>
                <h3 class="text-xl font-bold text-[#102654] mb-2">Filosofi "Sampai Paham"</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Bukan sekadar mengirimkan file .sav atau output tabel. Kami menyediakan sesi bimbingan privat agar Anda mampu menjelaskan setiap angka dengan percaya diri di hadapan penguji.
                </p>
            </div>

            <!-- Pilar 2 -->
            <div class="p-8 rounded-3xl bg-[#FFFDF8] border border-amber-200/80 hover:border-[#F29B3A] transition-all group">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-[#B36A12] flex items-center justify-center font-black text-xl mb-6 group-hover:scale-110 transition-transform">
                    🎓
                </div>
                <h3 class="text-xl font-bold text-[#102654] mb-2">Konsultan Lulusan Magister Statistik</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Tim mentor kami berlatar belakang pendidikan statistika, ekonometrika, dan metodologi riset dari perguruan tinggi terkemuka dengan pengalaman publikasi jurnal Scopus &amp; Sinta.
                </p>
            </div>

            <!-- Pilar 3 -->
            <div class="p-8 rounded-3xl bg-[#F8FBFE] border border-slate-200/80 hover:border-[#2B8BD1] transition-all group">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-800 flex items-center justify-center font-black text-xl mb-6 group-hover:scale-110 transition-transform">
                    🛡️
                </div>
                <h3 class="text-xl font-bold text-[#102654] mb-2">Garansi Revisi Dosen Tanpa Biaya</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Dosen pembimbing meminta penambahan indikator, eliminasi data outlier, atau perubahan variabel kontrol? Kami dampingi proses re-run data hingga naskah bab 4 disetujui.
                </p>
            </div>

            <!-- Pilar 4 -->
            <div class="p-8 rounded-3xl bg-[#FFFDF8] border border-amber-200/80 hover:border-[#F29B3A] transition-all group">
                <div class="w-14 h-14 rounded-2xl bg-purple-100 text-purple-800 flex items-center justify-center font-black text-xl mb-6 group-hover:scale-110 transition-transform">
                    🔒
                </div>
                <h3 class="text-xl font-bold text-[#102654] mb-2">Kerahasiaan Dataset &amp; Privasi 100%</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Dataset mentah, kuesioner responden, dan draft penelitian Anda dijamin kerahasiaannya dengan perjanjian Non-Disclosure Agreement (NDA) ketat. Kami tidak pernah membagikan data Anda.
                </p>
            </div>

            <!-- Pilar 5 -->
            <div class="p-8 rounded-3xl bg-[#F8FBFE] border border-slate-200/80 hover:border-[#2B8BD1] transition-all group">
                <div class="w-14 h-14 rounded-2xl bg-rose-100 text-rose-800 flex items-center justify-center font-black text-xl mb-6 group-hover:scale-110 transition-transform">
                    🎯
                </div>
                <h3 class="text-xl font-bold text-[#102654] mb-2">Solusi Data Bermasalah Sah Ilmiah</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Data tidak normal, timbul multikolinearitas, atau hipotesis tidak signifikan? Kami memberikan solusi berbasis literatur metodologis resmi, bukan manipulasi data ilegal.
                </p>
            </div>

            <!-- Pilar 6 -->
            <div class="p-8 rounded-3xl bg-[#FFFDF8] border border-amber-200/80 hover:border-[#F29B3A] transition-all group">
                <div class="w-14 h-14 rounded-2xl bg-teal-100 text-teal-800 flex items-center justify-center font-black text-xl mb-6 group-hover:scale-110 transition-transform">
                    ⚡
                </div>
                <h3 class="text-xl font-bold text-[#102654] mb-2">Pengerjaan Terjadwal &amp; Tepat Waktu</h3>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Dikejar deadline pendaftaran sidang atau seminar proposal? Kami menyediakan opsi pengerjaan kilat terukur (2 - 3 hari kerja) tanpa mengurangi kualitas analisis dan interpretasi.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Alur Kerja Aether Data Flow -->
<section class="py-20 bg-[#F8FBFE] border-t border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-widest text-[#2B8BD1]">Alur Kolaborasi</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-[#102654] mt-2">
                4 Langkah Mudah Pendampingan Riset
            </h2>
            <p class="text-slate-600 mt-3 text-sm sm:text-base">
                Proses konsultasi yang transparan, terstruktur, dan mudah dipantau kapan saja.
            </p>
        </div>

        <div class="grid md:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs relative">
                <div class="w-12 h-12 rounded-2xl bg-[#F6C75A] text-[#102654] font-black flex items-center justify-center text-xl mb-4">
                    1
                </div>
                <h3 class="text-lg font-bold text-[#102654] mb-2">Kirim Dataset &amp; Bab 3</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                    Unggah data mentah kuesioner (Excel/Google Form) serta model kerangka konseptual hipotesis Anda.
                </p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs relative">
                <div class="w-12 h-12 rounded-2xl bg-[#58B7EA] text-white font-black flex items-center justify-center text-xl mb-4">
                    2
                </div>
                <h3 class="text-lg font-bold text-[#102654] mb-2">Diagnosis Awal Gratis</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                    Konsultan kami mengecek kelayakan data, pola distribusi, dan kesesuaian alat uji tanpa pungutan biaya.
                </p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs relative">
                <div class="w-12 h-12 rounded-2xl bg-[#2B8BD1] text-white font-black flex items-center justify-center text-xl mb-4">
                    3
                </div>
                <h3 class="text-lg font-bold text-[#102654] mb-2">Pengolahan &amp; Interpretasi</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                    Data diolah dengan software terkait. Output disusun dalam naskah bab 4 lengkap dengan narasi pembahasan teoritis.
                </p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-slate-200 shadow-xs relative">
                <div class="w-12 h-12 rounded-2xl bg-[#102654] text-white font-black flex items-center justify-center text-xl mb-4">
                    4
                </div>
                <h3 class="text-lg font-bold text-[#102654] mb-2">Sesi Bimbingan 1-on-1</h3>
                <p class="text-slate-600 text-xs sm:text-sm leading-relaxed">
                    Bedah hasil bersama mentor melalui Google Meet, simulasi tanya jawab, hingga siap menghadapi ujian sidang.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Band -->
<section class="py-16 bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] text-white">
    <div class="max-w-5xl mx-auto px-4 text-center space-y-4">
        <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
            Ingin Tahu Kelayakan Dataset Kamu Saat Ini?
        </h2>
        <p class="text-sky-100 text-sm sm:text-base max-w-2xl mx-auto">
            Jangan tunggu sampai waktu sidang mendesak. Konsultasikan rancangan model riset Anda bersama tim mentor Aether sekarang juga.
        </p>
        <div class="pt-2 flex justify-center gap-3">
            <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20ingin%20cek%20kelayakan%20dataset%20riset%20saya" target="_blank" class="px-7 py-3.5 rounded-xl font-bold text-sm text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-xl transition">
                Mulai Diagnosis Sekarang
            </a>
            <a href="{{ url('/layanan') }}" class="px-7 py-3.5 rounded-xl font-bold text-sm text-white bg-white/10 hover:bg-white/20 border border-white/20 transition">
                Lihat Semua Layanan
            </a>
        </div>
    </div>
</section>
@endsection
