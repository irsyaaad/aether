<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aether Data Lab | Solusi Riset & Olah Data Skripsi, Tesis, Disertasi')</title>
    <meta name="description" content="@yield('meta_description', 'Aether Data Lab membantu konsultasi olah data statistik, SEM-PLS, ekonometrika, dan analisis kualitatif sampai paham untuk skripsi, tesis, dan publikasi ilmiah bereputasi.')">
    <meta name="keywords" content="@yield('meta_keywords', 'olah data skripsi, jasa olah data tesis, smartpls sem, spss amos, analisis data kualitatif nvivo, konsultasi bab 4 skripsi, aether data lab')">
    <meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="author" content="Aether Data Lab">
    <meta name="theme-color" content="#102654">

    <!-- Canonical URL -->
    <link rel="canonical" href="@yield('canonical', url()->current())">
    <link rel="alternate" hreflang="id" href="@yield('canonical', url()->current())">
    <link rel="alternate" hreflang="x-default" href="@yield('canonical', url()->current())">

    <!-- Open Graph / Facebook -->
    <meta property="og:locale" content="id_ID">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', 'Aether Data Lab | Solusi Riset & Olah Data Terpercaya')">
    <meta property="og:description" content="@yield('meta_description', 'Bimbingan dan olah data skripsi, tesis, dan riset akademik sampai paham dengan mentor ahli.')">
    <meta property="og:url" content="@yield('canonical', url()->current())">
    <meta property="og:site_name" content="Aether Data Lab">
    <meta property="og:image" content="@yield('og_image', asset('images/logo_aether.png'))">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Aether Data Lab | Solusi Riset & Olah Data Terpercaya')">
    <meta name="twitter:description" content="@yield('meta_description', 'Bimbingan dan olah data skripsi, tesis, dan riset akademik sampai paham dengan mentor ahli.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/logo_aether.png'))">

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo_aether.png') }}">
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_aether.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_aether.png') }}">

    <!-- Schema.org Base Organization & WebSite JSON-LD -->
    <script type="application/ld+json">
    {!! json_encode([
      '@context' => 'https://schema.org',
      '@graph' => [
        [
          '@type' => 'Organization',
          '@id' => url('/') . '/#organization',
          'name' => 'Aether Data Lab',
          'legalName' => 'Aether Data Lab Research & Analytics',
          'url' => url('/'),
          'logo' => [
            '@type' => 'ImageObject',
            '@id' => url('/') . '/#logo',
            'url' => asset('images/logo_aether.png'),
            'caption' => 'Aether Data Lab Logo',
          ],
          'contactPoint' => [
            [
              '@type' => 'ContactPoint',
              'telephone' => '+6285745143123',
              'contactType' => 'customer service & consultation',
              'areaServed' => 'ID',
              'availableLanguage' => ['Indonesian', 'English'],
            ],
          ],
        ],
        [
          '@type' => 'WebSite',
          '@id' => url('/') . '/#website',
          'url' => url('/'),
          'name' => 'Aether Data Lab',
          'description' => 'Konsultasi & Jasa Olah Data Riset, Skripsi, Tesis, dan Analitik Akademik',
          'publisher' => [
            '@id' => url('/') . '/#organization',
          ],
          'inLanguage' => 'id-ID',
        ],
      ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @stack('schema_json')

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS & Alpine.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    aspectRatio: {
                        '16/10': '16 / 10',
                        '4/3': '4 / 3',
                        '16/9': '16 / 9'
                    },
                    colors: {
                        aether: {
                            blue: '#2B8BD1',
                            blue2: '#58B7EA',
                            deep: '#1768B5',
                            navy: '#102654',
                            gold: '#F6C75A',
                            orange: '#F29B3A',
                            ivory: '#FFFDF8',
                            cream: '#FBF3E5',
                        },
                        brand: {
                            navy: '#102654',
                            deepnavy: '#0b1b3d',
                            gold: '#F6C75A',
                            ivory: '#FFFDF8',
                            white: '#FFFFFF',
                            emerald: '#2B8BD1',
                            forest: '#1768B5',
                        }
                    },
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body class="bg-[#F8FBFE] text-[#17324D] font-sans antialiased selection:bg-[#2B8BD1] selection:text-white" x-data="{ mobileMenu: false }">

@include('partials.public-header')

<main>
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-[#102654] text-slate-300 pt-16 pb-12 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">
            <div class="lg:col-span-2 space-y-4">
                <a href="{{ url('/') }}" class="inline-block bg-white p-2.5 rounded-2xl shadow-md" aria-label="Kembali ke Beranda Aether Data Lab">
                    <img src="{{ asset('images/logo_aether.png') }}"
                         alt="Logo Aether Data Lab"
                         width="200"
                         height="40"
                         loading="lazy"
                         class="h-10 w-auto object-contain rounded-lg">
                </a>
                <p class="text-xs sm:text-sm text-slate-200 leading-relaxed max-w-sm">
                    <strong>Aether Data Lab</strong> berdedikasi membimbing mahasiswa, akademisi, dan profesional dalam pengolahan data statistik, metodologi riset ilmiah, hingga interpretasi bab 4 skripsi &amp; tesis sampai tuntas.
                </p>
                <div class="flex items-center gap-3 pt-2">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 border border-white/15 text-[#F6C75A] text-xs font-bold">
                        ⭐ 4.9/5 Klien Puas
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/10 border border-white/15 text-sky-300 text-xs font-bold">
                        1.200+ Riset Tuntas
                    </span>
                </div>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-4">Navigasi Utama</h3>
                <ul class="space-y-2 text-xs sm:text-sm text-slate-300">
                    <li><a href="{{ url('/') }}" class="hover:text-[#58B7EA] transition">Beranda Utama</a></li>
                    <li><a href="{{ url('/layanan') }}" class="hover:text-[#58B7EA] transition">Layanan Olah Data</a></li>
                    <li><a href="{{ url('/keunggulan') }}" class="hover:text-[#58B7EA] transition">Keunggulan &amp; Alur</a></li>
                    <li><a href="{{ url('/berita') }}" class="hover:text-[#58B7EA] transition">Wawasan &amp; Artikel</a></li>
                    <li><a href="{{ url('/#faq') }}" class="hover:text-[#58B7EA] transition">Tanya Jawab (FAQ)</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-4">Layanan Populer</h3>
                <ul class="space-y-2 text-xs sm:text-sm text-slate-300">
                    <li><a href="{{ url('/layanan#kuantitatif') }}" class="hover:text-[#58B7EA] transition">Uji Regresi &amp; Ekonometrika (SPSS)</a></li>
                    <li><a href="{{ url('/layanan#sem-pls') }}" class="hover:text-[#58B7EA] transition">Pemodelan SEM-PLS (SmartPLS)</a></li>
                    <li><a href="{{ url('/layanan#kualitatif') }}" class="hover:text-[#58B7EA] transition">Coding Kualitatif (NVivo/ATLAS.ti)</a></li>
                    <li><a href="{{ url('/layanan#bimbingan-sidang') }}" class="hover:text-[#58B7EA] transition">Simulasi Tanya Jawab Penguji</a></li>
                    <li><a href="{{ url('/#quiz') }}" class="hover:text-[#58B7EA] transition">Kuis Diagnosa Kesiapan Data</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-white mb-4">Pusat Konsultasi</h3>
                <address class="not-italic text-xs sm:text-sm text-slate-300 space-y-2">
                    <p>Research &amp; Analytics Consulting Hub — Layanan Bimbingan Online se-Indonesia &amp; Offline Sesi Terjadwal</p>
                    <p class="pt-2"><strong class="text-white">WhatsApp:</strong> +62 857-4514-3123</p>
                    <p><strong class="text-white">Email:</strong> contact@aetherdatalab.com</p>
                    <p><strong class="text-white">Jam Bimbingan:</strong> Setiap Hari 08.00 - 22.00 WIB</p>
                </address>
            </div>
        </div>

        <div class="pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
            <p>© {{ date('Y') }} Aether Data Lab. Hak Cipta Dilindungi. Data kamu ada, insight-nya kami bantu olah sampai paham.</p>
            <div class="flex items-center space-x-6">
                <a href="{{ url('/#faq') }}" class="hover:text-[#F6C75A] transition">Kebijakan Privasi</a>
                <a href="{{ url('/#faq') }}" class="hover:text-[#F6C75A] transition">Ketentuan Garansi</a>
                <a href="{{ url('sitemap.xml') }}" class="hover:text-[#F6C75A] transition">Sitemap</a>
            </div>
        </div>
    </div>
</footer>

@stack('scripts')
</body>
</html>
