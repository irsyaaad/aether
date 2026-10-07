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
    <meta property="og:image" content="@yield('og_image', asset('images/logo_aether.jpeg'))">

    <!-- Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('title', 'Aether Data Lab | Solusi Riset & Olah Data Terpercaya')">
    <meta name="twitter:description" content="@yield('meta_description', 'Bimbingan dan olah data skripsi, tesis, dan riset akademik sampai paham dengan mentor ahli.')">
    <meta name="twitter:image" content="@yield('og_image', asset('images/logo_aether.jpeg'))">

    <!-- Favicon & Touch Icons -->
    <link rel="icon" type="image/jpeg" href="{{ asset('images/logo_aether.jpeg') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('images/logo_aether.jpeg') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo_aether.jpeg') }}">

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
            'url' => asset('images/logo_aether.jpeg'),
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

<!-- Top Banner Ticker -->
<div class="bg-gradient-to-r from-[#2B8BD1] via-[#58B7EA] to-[#F29B3A] text-white text-xs font-semibold py-2 px-4 shadow-xs">
    <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-2 overflow-hidden">
            <span class="bg-[#FFF5D8] text-[#B36A12] text-[10px] font-extrabold px-2 py-0.5 rounded-full uppercase shrink-0">Aether Update</span>
            <p class="truncate text-white/95">
                Pendaftaran Bimbingan Olah Data Skripsi &amp; Tesis Periode Sidang Aktif Dibuka — Konsultasi Awal &amp; Diagnosis Data Gratis!
            </p>
        </div>
        <a href="https://wa.me/6285745143123?text=Halo%20Aether%20Data%20Lab,%20saya%20ingin%20konsultasi%20olah%20data" target="_blank" class="hidden sm:inline-flex items-center gap-1 text-[11px] font-bold text-white hover:underline shrink-0">
            <span>Chat Mentor</span>
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        </a>
    </div>
</div>

<!-- Header Sticky Navbar -->
<header class="sticky top-0 z-50 bg-white/95 backdrop-blur-md border-b border-slate-200/80 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
            <!-- Brand Logo -->
            <div class="flex items-center">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3" aria-label="Beranda Aether Data Lab">
                    <img src="{{ asset('images/logo_aether.jpeg') }}"
                         alt="Logo Resmi Aether Data Lab"
                         width="180"
                         height="48"
                         loading="eager"
                         class="h-11 sm:h-12 w-auto object-contain rounded-lg">
                    <div class="hidden sm:block leading-tight">
                        <div class="text-base font-extrabold text-[#102654] tracking-tight">Aether Data Lab</div>
                        <div class="text-[11px] font-bold text-[#2B8BD1] tracking-wider uppercase">Research &amp; Analytics</div>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation Links -->
            <nav class="hidden md:flex items-center space-x-7" aria-label="Navigasi Utama">
                <a href="{{ url('/') }}" class="text-sm font-semibold {{ request()->is('/') ? 'text-[#1768B5] font-bold border-b-2 border-[#2B8BD1] pb-1' : 'text-slate-700 hover:text-[#2B8BD1]' }} transition-colors" aria-label="Navigasi ke Beranda">Home</a>
                <a href="{{ url('/layanan') }}" class="text-sm font-semibold {{ request()->is('layanan*') ? 'text-[#1768B5] font-bold border-b-2 border-[#2B8BD1] pb-1' : 'text-slate-700 hover:text-[#2B8BD1]' }} transition-colors" aria-label="Navigasi ke Layanan Riset">Layanan Data</a>
                <a href="{{ url('/keunggulan') }}" class="text-sm font-semibold {{ request()->is('keunggulan*') ? 'text-[#1768B5] font-bold border-b-2 border-[#2B8BD1] pb-1' : 'text-slate-700 hover:text-[#2B8BD1]' }} transition-colors" aria-label="Navigasi ke Keunggulan">Keunggulan &amp; Alur</a>
                <a href="{{ url('/berita') }}" class="text-sm font-semibold {{ request()->is('berita*') ? 'text-[#1768B5] font-bold border-b-2 border-[#2B8BD1] pb-1' : 'text-slate-700 hover:text-[#2B8BD1]' }} transition-colors" aria-label="Navigasi ke Wawasan Riset">Wawasan &amp; Artikel</a>
            </nav>

            <!-- Actions Desktop -->
            <div class="hidden md:flex items-center space-x-3">
                <a href="https://wa.me/6285745143123?text=Halo%20Aether%20Data%20Lab,%20mau%20konsultasi%20olah%20data" target="_blank" class="text-xs font-bold text-slate-700 hover:text-[#2B8BD1] flex items-center gap-1.5 px-3 py-2 rounded-xl hover:bg-slate-50 transition" aria-label="Konsultasi cepat via WhatsApp">
                    <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.299.144.347.491 1.2.534 1.287.043.087.072.188.014.303-.058.116-.087.188-.173.289l-.26.303c-.087.087-.179.182-.077.357.101.174.453.748.971 1.209.667.592 1.229.775 1.402.862.173.087.275.072.376-.043.101-.116.433-.506.549-.679.116-.173.231-.144.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/>
                    </svg>
                    <span>Konsultasi WA</span>
                </a>

                @if(auth()->check())
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold text-sky-900 bg-sky-100 hover:bg-sky-200 border border-sky-300 transition-all">
                        <svg class="w-4 h-4 mr-1 text-[#2B8BD1]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                        Dashboard
                    </a>
                @else
                    <a href="{{ url('/login') }}" class="inline-flex items-center justify-center px-3.5 py-2.5 rounded-xl text-xs font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300/80 transition-all">
                        Masuk
                    </a>
                    <a href="{{ url('/register') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] shadow-md shadow-sky-500/20 transition-all">
                        Daftar Akun
                    </a>
                @endif
            </div>

            <!-- Hamburger Button for Mobile -->
            <div class="flex md:hidden">
                <button type="button" @click="mobileMenu = !mobileMenu" class="p-2 text-slate-700 hover:text-[#2B8BD1] rounded-lg focus:outline-none" aria-label="Buka menu navigasi seluler" :aria-expanded="mobileMenu">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path x-show="!mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        <path x-show="mobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" style="display: none;"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu Drawer -->
    <div x-show="mobileMenu" x-transition.opacity class="md:hidden border-t border-slate-100 bg-white px-4 pt-3 pb-6 space-y-3 shadow-xl" style="display: none;">
        <a href="{{ url('/') }}" @click="mobileMenu = false" class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->is('/') ? 'bg-sky-50 text-[#1768B5] font-bold' : 'text-slate-800 hover:bg-sky-50 hover:text-[#2B8BD1]' }}">Home</a>
        <a href="{{ url('/layanan') }}" @click="mobileMenu = false" class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->is('layanan*') ? 'bg-sky-50 text-[#1768B5] font-bold' : 'text-slate-800 hover:bg-sky-50 hover:text-[#2B8BD1]' }}">Layanan Data</a>
        <a href="{{ url('/keunggulan') }}" @click="mobileMenu = false" class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->is('keunggulan*') ? 'bg-sky-50 text-[#1768B5] font-bold' : 'text-slate-800 hover:bg-sky-50 hover:text-[#2B8BD1]' }}">Keunggulan &amp; Alur</a>
        <a href="{{ url('/berita') }}" @click="mobileMenu = false" class="block px-3 py-2 rounded-xl text-base font-medium {{ request()->is('berita*') ? 'bg-sky-50 text-[#1768B5] font-bold' : 'text-slate-800 hover:bg-sky-50 hover:text-[#2B8BD1]' }}">Wawasan &amp; Artikel</a>

        <div class="pt-3 border-t border-slate-100 space-y-2">
            <a href="https://wa.me/6285745143123?text=Halo%20Aether%20Data%20Lab,%20mau%20konsultasi%20olah%20data" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-3 rounded-xl text-sm font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] text-center">
                Konsultasi Data via WhatsApp
            </a>
            @if(auth()->check())
                <a href="{{ route('dashboard') }}" class="w-full inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-sky-900 bg-sky-100 hover:bg-sky-200 border border-sky-300 text-center">
                    Buka Dashboard Klien
                </a>
            @else
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ url('/login') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 text-center">
                        Masuk
                    </a>
                    <a href="{{ url('/register') }}" class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl text-sm font-bold text-white bg-[#102654] hover:bg-[#1768B5] text-center">
                        Daftar Akun
                    </a>
                </div>
            @endif
        </div>
    </div>
</header>

<main>
    @yield('content')
</main>

<!-- Footer -->
<footer class="bg-[#102654] text-slate-300 pt-16 pb-12 border-t border-white/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-10 mb-12">
            <div class="lg:col-span-2 space-y-4">
                <a href="{{ url('/') }}" class="inline-block bg-white p-2.5 rounded-2xl shadow-md" aria-label="Kembali ke Beranda Aether Data Lab">
                    <img src="{{ asset('images/logo_aether.jpeg') }}"
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
