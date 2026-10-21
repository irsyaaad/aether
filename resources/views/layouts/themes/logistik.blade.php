<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <base href="{{ url('') }}/">
    <title>@yield('title', config('app.name', 'Persada Duta Express'))</title>
    <meta charset="utf-8"/>
    <meta name="robots" content="follow, index"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content="@yield('meta_description', 'Sistem Operasional Logistik & Kargo Persada Duta Express')"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/logo_aether.png') }}"/>
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_aether.png') }}"/>
    <link rel="apple-touch-icon" href="{{ asset('images/logo_aether.png') }}"/>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Roboto:wght@400;500;700&display=swap" rel="stylesheet"/>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: ["class"],
            theme: {
                extend: {
                    colors: {
                        primary: {
                            DEFAULT: '#e11d48',
                            foreground: '#ffffff',
                            50: '#fff1f2',
                            100: '#ffe4e6',
                            500: '#f43f5e',
                            600: '#e11d48',
                            700: '#be123c',
                            800: '#9f1239',
                            900: '#881337',
                        },
                        border: 'hsl(214.3, 31.8%, 91.4%)',
                        input: 'hsl(214.3, 31.8%, 91.4%)',
                    }
                }
            }
        }
    </script>
    <link href="{{ asset('assets-logistik/css/app.css') }}" rel="stylesheet"/>

    <script>
        // Apply collapsed state before render to prevent FOUC
        if (localStorage.getItem('sidebar_collapsed') === 'true' && window.innerWidth >= 1024) {
            document.documentElement.classList.add('sidebar-collapsed');
        }
    </script>
    <style>
        @media (min-width: 1024px) {
            #logis-sidebar {
                transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            }
            #logis-sidebar.is-collapsed,
            html.sidebar-collapsed #logis-sidebar {
                margin-left: -18.25rem !important; /* -288px - 1px border */
            }
        }
    </style>

    @stack('styles')
</head>
<body class="app-theme antialiased h-full text-slate-900 bg-slate-50/50 font-sans">

    <div class="flex h-screen overflow-hidden bg-slate-50/50">
        <!-- Sidebar Backdrop for Mobile -->
        <div id="logis-sidebar-backdrop" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm hidden lg:hidden"></div>

        <!-- Sidebar -->
        <aside id="logis-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 transform -translate-x-full lg:translate-x-0 border-r border-slate-200 bg-white transition-transform duration-300 ease-in-out lg:static shrink-0 flex flex-col justify-between">
            <div>
                <!-- Brand Header -->
                <div class="flex items-center justify-between h-16 px-6 border-b border-slate-200">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img class="h-9 w-auto object-contain" src="{{ asset('images/logo_aether.png') }}" alt="Aether"/>
                    </a>
                    <button id="logis-sidebar-close" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition-colors" title="Tutup / Sembunyikan Sidebar">
                        <i data-lucide="x" class="h-5 w-5 lg:hidden"></i>
                        <i data-lucide="panel-left-close" class="h-5 w-5 hidden lg:block"></i>
                    </button>
                </div>

                <!-- Navigation Groups -->
                <div id="logis-sidebar-nav" class="flex flex-col gap-1 p-4 h-[calc(100vh-14rem)] overflow-y-auto custom-scrollbar scroll-smooth">
                    <!-- Dashboard -->
                    <div class="flex flex-col gap-1 mb-2">
                        <div class="mb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Dashboard
                        </div>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm {{ request()->is('dashboard') && !request()->is('dashboard/omset*') ? 'bg-rose-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="layout-dashboard" class="h-4 w-4 {{ request()->is('dashboard') && !request()->is('dashboard/omset*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Monitoring Kepegawaian
                        </a>
                        <a href="{{ route('dashboard.omset') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm {{ request()->is('dashboard/omset*') ? 'bg-rose-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="trending-up" class="h-4 w-4 {{ request()->is('dashboard/omset*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Pemantauan Omset
                        </a>
                    </div>


                    <!-- Master Data -->
                    @php
                        $hasCompanies = check_access('companies.index');
                        $hasDepartemens = check_access('departemens.index');
                        $hasPositions = check_access('positions.index');
                        $hasPlatforms = check_access('platforms.index');
                        $hasTokos = check_access('tokos.index') || check_access('toko.index');
                        $hasProduks = check_access('produks.index') || check_access('produk.index');
                        $hasKaryawan = check_access('karyawan.index');
                        $hasMasterGroup = $hasCompanies || $hasDepartemens || $hasPositions || $hasPlatforms || $hasTokos || $hasProduks || $hasKaryawan;
                    @endphp

                    @if($hasMasterGroup)
                    <div class="flex flex-col gap-1 mb-3">
                        <div class="mb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Master Data
                        </div>
                        @if($hasCompanies)
                        <a href="{{ route('companies.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm {{ request()->is('master/companies*') ? 'bg-rose-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="building-2" class="h-4 w-4 {{ request()->is('master/companies*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Companies
                        </a>
                        @endif
                        @if($hasDepartemens)
                        <a href="{{ route('departemens.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm {{ request()->is('master/departemens*') || request()->is('master/departments*') ? 'bg-rose-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="network" class="h-4 w-4 {{ request()->is('master/departemens*') || request()->is('master/departments*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Departemen
                        </a>
                        @endif
                        @if($hasPlatforms)
                        <a href="{{ route('platforms.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm {{ request()->is('master/platform*') ? 'bg-rose-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="layout-grid" class="h-4 w-4 {{ request()->is('master/platform*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Platform
                        </a>
                        @endif
                        @if($hasPositions)
                        <a href="{{ route('positions.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm {{ request()->is('master/positions*') ? 'bg-rose-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="briefcase" class="h-4 w-4 {{ request()->is('master/positions*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Jabatan
                        </a>
                        @endif
                        @if($hasTokos)
                        <a href="{{ route('tokos.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm {{ request()->is('master/tokos*') || request()->is('master/toko*') ? 'bg-rose-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="store" class="h-4 w-4 {{ request()->is('master/tokos*') || request()->is('master/toko*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Toko
                        </a>
                        @endif
                        @if($hasProduks)
                        <a href="{{ route('produks.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm {{ request()->is('master/produks*') || request()->is('master/produk*') ? 'bg-rose-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="package" class="h-4 w-4 {{ request()->is('master/produks*') || request()->is('master/produk*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Produk
                        </a>
                        @endif
                        @if($hasKaryawan)
                        <a href="{{ route('karyawan.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm {{ request()->is('master/karyawan*') || request()->is('master/karyawans*') ? 'bg-rose-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="users" class="h-4 w-4 {{ request()->is('master/karyawan*') || request()->is('master/karyawans*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Karyawan
                        </a>
                        @endif
                    </div>
                    @endif

                    <!-- Keuangan -->
                    @php
                        $hasKasMasuk = check_access('kas-bank-masuk.index');
                        $hasKasKeluar = check_access('kas-bank-keluar.index');
                        $hasKeuanganGroup = $hasKasMasuk || $hasKasKeluar;
                    @endphp

                    @if($hasKeuanganGroup)
                    <div class="flex flex-col gap-1 mb-3">
                        <div class="mb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Keuangan
                        </div>
                        @if($hasKasMasuk)
                        <a href="{{ route('kas-bank-masuk.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm {{ request()->is('kas-bank-masuk*') ? 'bg-rose-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="arrow-down-left" class="h-4 w-4 {{ request()->is('kas-bank-masuk*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Kas / Bank Masuk
                        </a>
                        @endif
                        @if($hasKasKeluar)
                        <a href="{{ route('kas-bank-keluar.index') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm {{ request()->is('kas-bank-keluar*') ? 'bg-rose-50 text-primary font-semibold' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                            <i data-lucide="arrow-up-right" class="h-4 w-4 {{ request()->is('kas-bank-keluar*') ? 'text-primary' : 'text-slate-400' }}"></i>
                            Kas / Bank Keluar
                        </a>
                        @endif
                    </div>
                    @endif

                    <!-- Setting -->
                    @php
                        $hasPerusahaan = check_access('perusahaan.index') || check_access('perusahaan.update');
                        $hasUser = check_access('user.index');
                        $hasSettingGroup = $hasPerusahaan || $hasUser;
                    @endphp

                    @if($hasSettingGroup)
                    <div class="flex flex-col gap-1 mb-3">
                        <div class="mb-1 px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400">
                            Pengaturan
                        </div>
                        @if($hasPerusahaan)
                        <a href="{{ url('setting/perusahaan') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900">
                            <i data-lucide="settings" class="h-4 w-4 text-slate-400"></i>
                            Setting Perusahaan
                        </a>
                        @endif
                        @if($hasUser)
                        <a href="{{ url('user') }}" class="flex items-center gap-3 px-3.5 py-2 rounded-xl transition-colors font-medium text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900">
                            <i data-lucide="user-cog" class="h-4 w-4 text-slate-400"></i>
                            Manajemen User
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Bottom User Profile Section -->
            <div class="border-t border-slate-200 p-4 bg-white">
                <div class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50/80 p-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-200 text-slate-700 font-bold shrink-0">
                        {{ strtoupper(substr(Auth::user()->nm_user ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex flex-col overflow-hidden min-w-0 flex-1">
                        <span class="text-sm font-semibold text-slate-900 truncate" title="{{ Auth::user()->nm_user ?? 'User Persada' }}">{{ Auth::user()->nm_user ?? 'User Persada' }}</span>
                        <span class="text-xs text-primary font-medium truncate flex items-center gap-1" title="Role: {{ Auth::user()->active_role_name ?? (session('role')['nm_role'] ?? 'User') }}">
                            <i data-lucide="shield" class="h-3 w-3 shrink-0"></i>
                            <span class="truncate">{{ Auth::user()->active_role_name ?? (session('role')['nm_role'] ?? 'User') }}</span>
                        </span>
                    </div>
                </div>
                <a href="{{ url('auth/logout') }}" class="mt-3 flex w-full items-center justify-center gap-2 rounded-xl px-3 py-2 text-sm font-medium text-rose-600 transition-colors hover:bg-rose-50 hover:text-rose-700">
                    <i data-lucide="log-out" class="h-4 w-4"></i>
                    Keluar Sistem
                </a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <!-- Header -->
            <header class="flex items-center justify-between gap-4 h-16 px-4 sm:px-6 lg:px-8 bg-white border-b border-slate-200 shadow-sm z-10 shrink-0">
                <div class="flex items-center gap-3">
                    <button id="logis-sidebar-toggle" class="text-slate-500 hover:text-slate-700 p-2 rounded-lg hover:bg-slate-100 transition-colors" title="Toggle Sidebar">
                        <i data-lucide="menu" class="h-6 w-6"></i>
                    </button>
                    
                    <div>
                        <h1 class="text-lg font-bold text-slate-900 leading-tight">
                            @yield('page_title', 'Dashboard Logistik')
                        </h1>
                        <div class="text-xs text-slate-500 flex items-center gap-1">
                            @yield('breadcrumb')
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3">


                    <!-- Toolbar Actions Slot -->
                    <div>
                        @yield('toolbar_actions')
                    </div>
                </div>
            </header>

            <!-- Main Page Scrollable -->
            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-slate-50/50 custom-scrollbar">
                <div class="mx-auto max-w-7xl space-y-6">
                    <!-- Global Notifications -->
                    @if(Session::has('success'))
                        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-3">
                                <i data-lucide="check-circle-2" class="h-5 w-5 text-emerald-600"></i>
                                <span>{{ Session::get('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(Session::has('error'))
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center justify-between shadow-sm">
                            <div class="flex items-center gap-3">
                                <i data-lucide="alert-circle" class="h-5 w-5 text-rose-600"></i>
                                <span>{{ Session::get('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
                            <div class="flex items-center gap-3 mb-2 font-semibold">
                                <i data-lucide="alert-triangle" class="h-5 w-5 text-rose-600"></i>
                                <span>Terjadi Kesalahan Validasi:</span>
                            </div>
                            <ul class="list-disc list-inside space-y-1 text-xs text-rose-700 ml-6">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Page Content -->
                    <div class="logis-content-wrapper">
                        @yield('content')
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets-logistik/js/logistik.js') }}?v=1.4"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
