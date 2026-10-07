@extends('layouts.public')

@section('title', 'Layanan Olah Data Riset, Skripsi & Tesis | Aether Data Lab')
@section('meta_description', 'Layanan lengkap konsultasi dan olah data riset Aether Data Lab: Olah Data Kuantitatif (SPSS, EViews, Stata), SEM-PLS (SmartPLS & AMOS), Analisis Kualitatif (NVivo, ATLAS.ti), dan Bimbingan 1-on-1 sampai paham.')
@section('meta_keywords', 'layanan olah data, bimbingan olah data skripsi tesis, smartpls sem, spss analisis data, nvivo kualitatif, konsultasi bab 4 skripsi, aether data lab')
@section('og_image', 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=1200&h=630&q=80')

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
          'name' => 'Layanan Riset & Olah Data',
          'item' => url('/layanan'),
        ],
      ],
    ],
    [
      '@type' => 'Service',
      '@id' => url('/layanan') . '#service',
      'name' => 'Layanan Olah Data & Konsultasi Metodologi Riset',
      'serviceType' => 'Statistik Kuantitatif, SEM-PLS, Ekonometrika, Kualitatif NVivo',
      'provider' => [
        '@type' => 'Organization',
        'name' => 'Aether Data Lab',
        'url' => url('/'),
      ],
      'areaServed' => [
        '@type' => 'Country',
        'name' => 'Indonesia',
      ],
      'hasOfferCatalog' => [
        '@type' => 'OfferCatalog',
        'name' => 'Katalog Layanan Olah Data Aether',
        'itemListElement' => [
          [
            '@type' => 'Offer',
            'itemOffered' => [
              '@type' => 'Service',
              'name' => 'Olah Data Kuantitatif & Ekonometrika',
              'url' => url('/layanan#kuantitatif'),
              'description' => 'Uji asumsi klasik, regresi berganda, data panel, path analysis menggunakan SPSS, EViews, Stata, dan R.',
            ],
          ],
          [
            '@type' => 'Offer',
            'itemOffered' => [
              '@type' => 'Service',
              'name' => 'Pemodelan SEM-PLS & CB-SEM',
              'url' => url('/layanan#sem-pls'),
              'description' => 'Evaluasi outer dan inner model, mediasi, moderasi, dan higher-order model menggunakan SmartPLS, AMOS, dan LISREL.',
            ],
          ],
          [
            '@type' => 'Offer',
            'itemOffered' => [
              '@type' => 'Service',
              'name' => 'Analisis Kualitatif & Thematic Coding',
              'url' => url('/layanan#kualitatif'),
              'description' => 'Pengodean transkrip wawancara mendalam, FGD, studi literatur menggunakan NVivo dan ATLAS.ti.',
            ],
          ],
          [
            '@type' => 'Offer',
            'itemOffered' => [
              '@type' => 'Service',
              'name' => 'Bimbingan 1-on-1 & Simulasi Sidang',
              'url' => url('/layanan#bimbingan-sidang'),
              'description' => 'Bedah interpretasi output bab 4 dan simulasi tanya jawab penguji sidang skripsi/tesis.',
            ],
          ],
        ],
      ],
    ],
  ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
<!-- Hero Section Layanan -->
<section class="relative bg-gradient-to-b from-[#102654] via-[#1768B5] to-[#102654] text-white py-20 lg:py-28 overflow-hidden">
    <div class="absolute inset-0 opacity-15 bg-[radial-gradient(#58B7EA_1px,transparent_1px)] [background-size:24px_24px]" aria-hidden="true"></div>
    <div class="absolute -right-32 -top-32 w-96 h-96 rounded-full bg-[#58B7EA]/20 blur-3xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center lg:text-left">
        <div class="grid lg:grid-cols-12 gap-10 items-center">
            <div class="lg:col-span-8 space-y-5">
                <nav class="flex justify-center lg:justify-start items-center space-x-2 text-xs font-semibold text-sky-200">
                    <a href="{{ url('/') }}" class="hover:text-white transition">Beranda</a>
                    <span>/</span>
                    <span class="text-white">Layanan Data &amp; Riset</span>
                </nav>

                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold border border-white/15">
                    <span class="w-2 h-2 rounded-full bg-[#F6C75A] animate-pulse"></span>
                    Solusi Metodologi &amp; Data Ilmiah Terpadu
                </div>

                <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight">
                    Layanan Olah Data <span class="text-transparent bg-clip-text bg-gradient-to-r from-[#F6C75A] via-[#58B7EA] to-white">Sampai Kamu Paham</span>
                </h1>

                <p class="text-base sm:text-lg text-sky-100 max-w-2xl leading-relaxed">
                    Aether Data Lab mendampingi riset skripsi, tesis, disertasi, dan publikasi jurnal dari validitas instrumen hingga simulasi tanya jawab penguji. Tidak hanya dapat output, kamu juga diajarkan logika di balik angka.
                </p>

                <div class="pt-4 flex flex-wrap justify-center lg:justify-start gap-3">
                    <a href="https://wa.me/6285745143123?text=Halo%20Aether%20Data%20Lab,%20saya%20ingin%20konsultasi%20layanan%20olah%20data" target="_blank" class="px-6 py-3.5 rounded-xl font-bold text-sm text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-lg shadow-amber-500/20 transition">
                        Konsultasi Diagnosis Data Gratis
                    </a>
                    <a href="{{ url('/#data-packages') }}" class="px-6 py-3.5 rounded-xl font-bold text-sm text-white bg-white/10 hover:bg-white/20 border border-white/20 transition">
                        Lihat Paket Biaya
                    </a>
                </div>
            </div>

            <!-- Operational Badges Card -->
            <div class="lg:col-span-4">
                <div class="bg-white/10 backdrop-blur-md border border-white/15 p-6 rounded-3xl shadow-2xl space-y-4">
                    <div class="text-xs font-bold uppercase tracking-widest text-sky-200">Garansi Kualitas Riset</div>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/5 border border-white/10">
                            <span class="w-8 h-8 rounded-xl bg-amber-400/20 text-[#F6C75A] flex items-center justify-center font-bold text-sm">✓</span>
                            <div>
                                <div class="text-xs font-bold text-white">Garansi Re-Run &amp; Revisi</div>
                                <div class="text-[11px] text-sky-200">Bebas revisi jika ada catatan dosen</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/5 border border-white/10">
                            <span class="w-8 h-8 rounded-xl bg-sky-400/20 text-[#58B7EA] flex items-center justify-center font-bold text-sm">✓</span>
                            <div>
                                <div class="text-xs font-bold text-white">Pendampingan 1-on-1 Sesi Privat</div>
                                <div class="text-[11px] text-sky-200">Google Meet interaktif rekam layar</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-white/5 border border-white/10">
                            <span class="w-8 h-8 rounded-xl bg-emerald-400/20 text-emerald-300 flex items-center justify-center font-bold text-sm">✓</span>
                            <div>
                                <div class="text-xs font-bold text-white">Interpretasi Lengkap Bab 4</div>
                                <div class="text-[11px] text-sky-200">Tabel rapi siap copy ke template kampus</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Service List Grid Detail -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-widest text-[#2B8BD1]">Pilihan Metodologi Riset</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-[#102654] mt-2">
                Pilih Layanan Sesuai Kebutuhan Riset Kamu
            </h2>
            <p class="text-slate-600 mt-3 text-sm sm:text-base">
                Dari uji hipotesis sederhana hingga pemodelan kompleks multivariat, kami siap mendampingi setiap tahap riset Anda.
            </p>
        </div>

        <div class="space-y-16">
            <!-- 1. Kuantitatif & Ekonometrika -->
            <div id="kuantitatif" class="grid lg:grid-cols-12 gap-8 items-center bg-[#F8FBFE] p-8 lg:p-12 rounded-3xl border border-slate-200/80">
                <div class="lg:col-span-7 space-y-4">
                    <span class="inline-block text-xs font-extrabold px-3 py-1 rounded-full bg-sky-100 text-[#1768B5]">Kuantitatif &amp; Ekonometrika</span>
                    <h3 class="text-2xl sm:text-3xl font-bold text-[#102654]">Uji Statistik Regresi, Korelasi &amp; Data Panel</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Layanan pengujian data kuantitatif menggunakan software standar (SPSS, EViews, Stata, dan R). Meliputi uji kelayakan instrumen (validitas &amp; reliabilitas), uji asumsi klasik komprehensif (normalitas, multikolinearitas, heteroskedastisitas, autokorelasi), regresi linier berganda, regresi logistik, serta estimasi model data panel (Common Effect, Fixed Effect, Random Effect dengan uji Chow, Hausman, dan LM Test).
                    </p>
                    <div class="grid grid-cols-2 gap-3 pt-2 text-xs font-semibold text-slate-700">
                        <div class="flex items-center gap-2"><span class="text-[#2B8BD1]">●</span> Output SPSS &amp; EViews rapi</div>
                        <div class="flex items-center gap-2"><span class="text-[#2B8BD1]">●</span> Interpretasi nilai t, F, &amp; R-Square</div>
                        <div class="flex items-center gap-2"><span class="text-[#2B8BD1]">●</span> Solusi data outlier &amp; transform</div>
                        <div class="flex items-center gap-2"><span class="text-[#2B8BD1]">●</span> Script sintaks / do-file disertakan</div>
                    </div>
                    <div class="pt-4">
                        <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20ingin%20konsultasi%20olah%20data%20Kuantitatif%20(SPSS/EViews)" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-[#1768B5] hover:bg-[#102654] transition">
                            <span>Konsultasi Kuantitatif</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
                <div class="lg:col-span-5 overflow-hidden rounded-2xl shadow-xl">
                    <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&w=800&q=80" alt="Analisis Kuantitatif Aether" class="w-full h-72 object-cover">
                </div>
            </div>

            <!-- 2. SEM-PLS & CB-SEM -->
            <div id="sem-pls" class="grid lg:grid-cols-12 gap-8 items-center bg-[#FFFDF8] p-8 lg:p-12 rounded-3xl border border-amber-200/80">
                <div class="lg:col-span-5 order-2 lg:order-1 overflow-hidden rounded-2xl shadow-xl">
                    <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?auto=format&fit=crop&w=800&q=80" alt="Pemodelan SEM Aether" class="w-full h-72 object-cover">
                </div>
                <div class="lg:col-span-7 order-1 lg:order-2 space-y-4">
                    <span class="inline-block text-xs font-extrabold px-3 py-1 rounded-full bg-amber-100 text-[#B36A12]">Structural Equation Modeling</span>
                    <h3 class="text-2xl sm:text-3xl font-bold text-[#102654]">Pemodelan SEM-PLS (SmartPLS) &amp; CB-SEM (AMOS)</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Spesialisasi riset manajemen bisnis, pemasaran, SDM, dan psikometri. Kami mengolah model jalur yang melibatkan variabel mediasi (intervening), variabel moderasi, konstruk second-order (higher-order construct), hingga analisis multigroup (PLS-MGA) dengan panduan evaluasi kriteria Hair et al.
                    </p>
                    <div class="grid grid-cols-2 gap-3 pt-2 text-xs font-semibold text-slate-700">
                        <div class="flex items-center gap-2"><span class="text-[#F29B3A]">●</span> Evaluasi Outer &amp; Inner Model</div>
                        <div class="flex items-center gap-2"><span class="text-[#F29B3A]">●</span> Bootstrapping 5.000 subsampel</div>
                        <div class="flex items-center gap-2"><span class="text-[#F29B3A]">●</span> Uji Mediasi Sobel / VAF / p-value</div>
                        <div class="flex items-center gap-2"><span class="text-[#F29B3A]">●</span> HTMT &amp; Fornell-Larcker matrix</div>
                    </div>
                    <div class="pt-4">
                        <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20ingin%20konsultasi%20SEM-PLS%20(SmartPLS/AMOS)" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs text-[#102654] bg-[#F6C75A] hover:bg-amber-300 transition">
                            <span>Konsultasi SEM-PLS</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>

            <!-- 3. Kualitatif & Mixed Methods -->
            <div id="kualitatif" class="grid lg:grid-cols-12 gap-8 items-center bg-[#F8FBFE] p-8 lg:p-12 rounded-3xl border border-slate-200/80">
                <div class="lg:col-span-7 space-y-4">
                    <span class="inline-block text-xs font-extrabold px-3 py-1 rounded-full bg-emerald-100 text-emerald-800">Kualitatif &amp; Thematic Analysis</span>
                    <h3 class="text-2xl sm:text-3xl font-bold text-[#102654]">Coding Kualitatif NVivo, ATLAS.ti &amp; Mixed Methods</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Membantu mengorganisasi ratusan halaman transkrip wawancara, catatan observasi lapangan, artikel berita, dan dokumen kebijakan. Kami menyusun coding tree hierarkis, pemetaan node tematik, matriks perbandingan lintas informan, dan penyajian visual hubungan antar konsep tematik.
                    </p>
                    <div class="grid grid-cols-2 gap-3 pt-2 text-xs font-semibold text-slate-700">
                        <div class="flex items-center gap-2"><span class="text-emerald-600">●</span> Open, Axial, Selective Coding</div>
                        <div class="flex items-center gap-2"><span class="text-emerald-600">●</span> Word Cloud &amp; Hierarchy Charts</div>
                        <div class="flex items-center gap-2"><span class="text-emerald-600">●</span> Triangulasi sumber &amp; metode</div>
                        <div class="flex items-center gap-2"><span class="text-emerald-600">●</span> Audit trail keabsahan data</div>
                    </div>
                    <div class="pt-4">
                        <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20ingin%20konsultasi%20Kualitatif%20(NVivo/ATLAS.ti)" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-emerald-700 hover:bg-emerald-800 transition">
                            <span>Konsultasi Kualitatif</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
                <div class="lg:col-span-5 overflow-hidden rounded-2xl shadow-xl">
                    <img src="https://images.unsplash.com/photo-1434030216411-0b793f4b4173?auto=format&fit=crop&w=800&q=80" alt="Analisis Kualitatif Aether" class="w-full h-72 object-cover">
                </div>
            </div>

            <!-- 4. Mentorship 1-on-1 -->
            <div id="bimbingan-sidang" class="grid lg:grid-cols-12 gap-8 items-center bg-[#FFFDF8] p-8 lg:p-12 rounded-3xl border border-sky-200/80">
                <div class="lg:col-span-5 order-2 lg:order-1 overflow-hidden rounded-2xl shadow-xl">
                    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=800&q=80" alt="Bimbingan Privat Aether" class="w-full h-72 object-cover">
                </div>
                <div class="lg:col-span-7 order-1 lg:order-2 space-y-4">
                    <span class="inline-block text-xs font-extrabold px-3 py-1 rounded-full bg-sky-100 text-[#1768B5]">1-on-1 Mentorship</span>
                    <h3 class="text-2xl sm:text-3xl font-bold text-[#102654]">Bimbingan Privat &amp; Simulasi Tanya Jawab Sidang</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Banyak mahasiswa takut saat sidang bukan karena datanya salah, tetapi karena tidak paham arti angka pada output. Bersama mentor Aether, Anda akan dibimbing membedah alasan pemilihan uji statistik, cara menjawab sanggahan penguji, dan penulisan bab pembahasan yang sinkron dengan kajian pustaka.
                    </p>
                    <div class="grid grid-cols-2 gap-3 pt-2 text-xs font-semibold text-slate-700">
                        <div class="flex items-center gap-2"><span class="text-[#2B8BD1]">●</span> Sesi Privat 60 - 90 Menit</div>
                        <div class="flex items-center gap-2"><span class="text-[#2B8BD1]">●</span> Latihan pertanyaan killer penguji</div>
                        <div class="flex items-center gap-2"><span class="text-[#2B8BD1]">●</span> Panduan pointer slide sidang</div>
                        <div class="flex items-center gap-2"><span class="text-[#2B8BD1]">●</span> Rekaman video sesi bimbingan</div>
                    </div>
                    <div class="pt-4">
                        <a href="https://wa.me/6285745143123?text=Halo%20Aether,%20saya%20ingin%20jadwal%20Bimbingan%20Privat%20Simulasi%20Sidang" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl font-bold text-xs text-white bg-[#102654] hover:bg-[#1768B5] transition">
                            <span>Jadwalkan Sesi Mentor</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Band -->
<section class="py-16 bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] text-white">
    <div class="max-w-5xl mx-auto px-4 text-center space-y-4">
        <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
            Punya Dataset yang Perlu Dianalisis Segera?
        </h2>
        <p class="text-sky-100 text-sm sm:text-base max-w-2xl mx-auto">
            Kirimkan draft bab 3 atau dataset mentah kamu. Tim konsultan metodologi Aether siap melakukan diagnosis awal tanpa biaya.
        </p>
        <div class="pt-2 flex justify-center gap-3">
            <a href="https://wa.me/6285745143123?text=Halo%20Aether%20Data%20Lab,%20mau%20konsultasi%20cek%20dataset" target="_blank" class="px-7 py-3.5 rounded-xl font-bold text-sm text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-xl transition">
                Chat Konsultan Sekarang
            </a>
            <a href="{{ url('/register') }}" class="px-7 py-3.5 rounded-xl font-bold text-sm text-white bg-white/10 hover:bg-white/20 border border-white/20 transition">
                Daftar Akun Klien
            </a>
        </div>
    </div>
</section>
@endsection
