<!DOCTYPE html>
<html class="h-full" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <base href="{{ url('') }}/">
    <title>@yield('title', config('app.name', 'Persada Duta Express'))</title>
    <meta charset="utf-8"/>
    <meta name="robots" content="follow, index"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no"/>
    <meta name="description" content="@yield('meta_description', '')"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/png" href="{{ asset('images/logo_aether.png') }}"/>
    <link rel="shortcut icon" type="image/png" href="{{ asset('images/logo_aether.png') }}"/>
    <link rel="apple-touch-icon" href="{{ asset('images/logo_aether.png') }}"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet"/>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Roboto', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                    },
                    colors: {
                        border: "hsl(0 0% 89.8%)",
                        input: "hsl(0 0% 89.8%)",
                        ring: "#e11d48",
                        background: "hsl(0 0% 100%)",
                        foreground: "hsl(0 0% 3.9%)",
                        primary: {
                            DEFAULT: "#e11d48",
                            foreground: "#ffffff",
                        },
                        secondary: {
                            DEFAULT: "hsl(0 0% 96.1%)",
                            foreground: "hsl(0 0% 9%)",
                        },
                        destructive: {
                            DEFAULT: "hsl(0 84.2% 60.2%)",
                            foreground: "#ffffff",
                        },
                        muted: {
                            DEFAULT: "hsl(0 0% 96.1%)",
                            foreground: "hsl(0 0% 45.1%)",
                        },
                        accent: {
                            DEFAULT: "hsl(0 0% 96.1%)",
                            foreground: "hsl(0 0% 9%)",
                        },
                        card: {
                            DEFAULT: "#ffffff",
                            foreground: "hsl(0 0% 3.9%)",
                        },
                    },
                    borderRadius: {
                        lg: "0.5rem",
                        md: "calc(0.5rem - 2px)",
                        sm: "calc(0.5rem - 4px)",
                    },
                },
            },
        }
    </script>
    <link href="{{ asset('assets-logistik/css/app.css') }}?v=1.2" rel="stylesheet"/>
    <link href="{{ asset('assets/vendors/select2/select2.min.css') }}" rel="stylesheet"/>
    <style>
        @keyframes dt-spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        div.dt-container, div.dataTables_wrapper, div[id$="_wrapper"] {
            position: relative !important;
            min-height: 80px;
        }
        table.dataTable.dt-table-loading {
            opacity: 0.35 !important;
            pointer-events: none !important;
            transition: opacity 0.15s ease-in-out !important;
        }
        div.dt-processing, div.dataTables_processing {
            position: absolute !important;
            top: 50% !important;
            left: 50% !important;
            transform: translate(-50%, -50%) !important;
            z-index: 9999 !important;
            margin: 0 !important;
            padding: 0 !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            width: auto !important;
            height: auto !important;
            display: none;
            pointer-events: none !important;
        }
        div.dt-processing.is-loading,
        div.dataTables_processing.is-loading,
        div.dt-processing[style*="display: block"],
        div.dataTables_processing[style*="display: block"] {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        div.dt-processing > div:last-child:not(.dt-spinner-only),
        div.dataTables_processing > div:last-child:not(.dt-spinner-only) {
            display: none !important;
        }
        div.dt-processing::before,
        div.dataTables_processing::before,
        div.dt-processing::after,
        div.dataTables_processing::after {
            display: none !important;
            content: none !important;
        }
        .dt-spinner-only {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            background: transparent !important;
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            pointer-events: none !important;
        }
    </style>

    @stack('styles')
</head>
<body class="h-full bg-gray-50/50 font-sans antialiased text-gray-900">

    <div class="flex h-screen overflow-hidden bg-gray-50/50">
        <!-- Sidebar for Mobile -->
        <div id="logis-sidebar-backdrop" class="fixed inset-0 z-40 bg-gray-900/80 backdrop-blur-sm hidden lg:hidden"></div>

        <!-- Sidebar -->
        <aside id="logis-sidebar" class="fixed inset-y-0 left-0 z-50 w-72 transform -translate-x-full lg:translate-x-0 border-r border-gray-200 bg-white transition-transform duration-300 ease-in-out lg:static shrink-0 flex flex-col justify-between">
            <div>
                <!-- Brand Header -->
                <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200">
                    <a href="{{ route('dashboard') }}" class="flex items-center">
                        <img class="h-9 w-auto object-contain" src="{{ asset('images/logo_aether.png') }}" alt="Aether"/>
                    </a>
                    <button id="logis-sidebar-close" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <i data-lucide="x" class="h-5 w-5"></i>
                    </button>
                </div>

                <!-- Navigation Groups -->
                <div id="logis-sidebar-nav" class="flex flex-col gap-1 p-4 h-[calc(100vh-14rem)] overflow-y-auto scroll-smooth">
                    <!-- Dashboard -->
                    <div class="flex flex-col gap-1 mb-2">
                        <div class="mb-1 px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                            Dashboard
                        </div>
                        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('dashboard*') ? 'active bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="layout-dashboard" class="h-4 w-4 {{ request()->is('dashboard*') ? 'text-primary' : 'text-gray-400' }}"></i>
                            Monitoring Riset Mahasiswa
                        </a>
                    </div>

                    <!-- LAYANAN UTAMA AKADEMIK & MAHASISWA -->
                    @php
                        $hasBimbingan = check_access('bimbingan.index');
                        $hasOlahdata = check_access('olahdata.index');
                        $hasPelatihan = check_access('pelatihan.index');
                        $hasAkademikGroup = $hasBimbingan || $hasOlahdata || $hasPelatihan;
                    @endphp

                    @if($hasAkademikGroup)
                    <div class="flex flex-col gap-1 mb-2">
                        <div class="mb-1 px-2 text-[11px] font-bold uppercase tracking-wider text-sky-600">
                            Layanan Akademik
                        </div>

                        <!-- 1. Bimbingan -->
                        @if($hasBimbingan)
                        <div class="flex flex-col">
                            <a href="{{ route('bimbingan.index') }}" class="flex items-center justify-between px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('bimbingan*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="graduation-cap" class="h-4 w-4 {{ request()->is('bimbingan*') ? 'text-[#1768B5]' : 'text-gray-500' }}"></i>
                                    <span>Bimbingan Skripsi</span>
                                </div>
                                <span class="text-[10px] bg-sky-100 text-[#1768B5] px-1.5 py-0.5 rounded-full font-bold">1-on-1</span>
                            </a>
                            <div class="ml-7 flex flex-col gap-0.5 border-l border-gray-200 pl-2 mt-0.5">
                                <a href="{{ route('bimbingan.programs') }}" class="px-2 py-1 text-xs text-gray-500 hover:text-[#1768B5] transition flex items-center gap-1.5 {{ request()->is('bimbingan/programs*') || request()->is('bimbingan/katalog*') ? 'active text-[#1768B5] font-bold' : '' }}">
                                    <span>• Pilihan &amp; Katalog Program</span>
                                </a>
                                <a href="{{ route('bimbingan.create') }}" class="px-2 py-1 text-xs text-gray-500 hover:text-[#1768B5] transition flex items-center gap-1.5 {{ request()->is('bimbingan/pengajuan*') ? 'active text-[#1768B5] font-bold' : '' }}">
                                    <span>• Ajukan Sesi Bimbingan</span>
                                </a>
                                <a href="{{ route('bimbingan.index') }}" class="px-2 py-1 text-xs text-gray-500 hover:text-[#1768B5] transition flex items-center gap-1.5 {{ (request()->routeIs('bimbingan.index') || request()->is('bimbingan')) && !request()->is('bimbingan/programs*') && !request()->is('bimbingan/katalog*') && !request()->is('bimbingan/pengajuan*') && !request()->is('bimbingan/invoices*') && !request()->is('bimbingan/pembayaran*') ? 'active text-[#1768B5] font-bold' : '' }}">
                                    <span>• Jadwal &amp; Riwayat Konsultasi</span>
                                </a>
                                <a href="{{ route('bimbingan.invoices.index') }}" class="px-2 py-1 text-xs text-gray-500 hover:text-[#1768B5] transition flex items-center gap-1.5 {{ request()->is('bimbingan/invoices*') ? 'active text-[#1768B5] font-bold' : '' }}">
                                    <span>• Rekap Invoice Program</span>
                                </a>
                                <a href="{{ route('bimbingan.pembayaran.index') }}" class="px-2 py-1 text-xs text-gray-500 hover:text-[#1768B5] transition flex items-center gap-1.5 {{ request()->is('bimbingan/pembayaran*') ? 'active text-[#1768B5] font-bold' : '' }}">
                                    <span>• List Pembayaran Program</span>
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- 2. Olah Data -->
                        @if($hasOlahdata)
                        <div class="flex flex-col mt-1">
                            <a href="{{ route('olahdata.index') }}" class="flex items-center justify-between px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('olahdata*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="bar-chart-2" class="h-4 w-4 {{ request()->is('olahdata*') ? 'text-[#1768B5]' : 'text-gray-500' }}"></i>
                                    <span>Olah Data Riset</span>
                                </div>
                                <span class="text-[10px] bg-amber-100 text-amber-800 px-1.5 py-0.5 rounded-full font-bold">SPSS/PLS</span>
                            </a>
                            <div class="ml-7 flex flex-col gap-0.5 border-l border-gray-200 pl-2 mt-0.5">
                                <a href="{{ route('olahdata.create') }}" class="px-2 py-1 text-xs text-gray-500 hover:text-[#1768B5] transition flex items-center gap-1.5 {{ request()->is('olahdata/upload-dataset*') ? 'active text-[#1768B5] font-bold' : '' }}">
                                    <span>• Upload Dataset Baru</span>
                                </a>
                                <a href="{{ route('olahdata.index') }}" class="px-2 py-1 text-xs text-gray-500 hover:text-[#1768B5] transition flex items-center gap-1.5 {{ (request()->routeIs('olahdata.index') || request()->is('olahdata')) && !request()->is('olahdata/upload-dataset*') ? 'active text-[#1768B5] font-bold' : '' }}">
                                    <span>• Progres &amp; Output Bab 4</span>
                                </a>
                            </div>
                        </div>
                        @endif

                        <!-- 3. Pelatihan -->
                        @if($hasPelatihan)
                        <div class="flex flex-col mt-1">
                            <a href="{{ route('pelatihan.index') }}" class="flex items-center justify-between px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('pelatihan*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="award" class="h-4 w-4 {{ request()->is('pelatihan*') ? 'text-[#1768B5]' : 'text-gray-500' }}"></i>
                                    <span>Pelatihan &amp; Workshop</span>
                                </div>
                                <span class="text-[10px] bg-purple-100 text-purple-800 px-1.5 py-0.5 rounded-full font-bold">Kelas</span>
                            </a>
                            <div class="ml-7 flex flex-col gap-0.5 border-l border-gray-200 pl-2 mt-0.5">
                                <a href="{{ route('pelatihan.index') }}" class="px-2 py-1 text-xs text-gray-500 hover:text-[#1768B5] transition flex items-center gap-1.5 {{ (request()->routeIs('pelatihan.index') || request()->is('pelatihan')) && !request()->is('pelatihan/kelas-saya*') && !request()->is('pelatihan/sertifikat*') ? 'active text-[#1768B5] font-bold' : '' }}">
                                    <span>• Katalog Workshop &amp; Kelas</span>
                                </a>
                                <a href="{{ route('pelatihan.kelas-saya') }}" class="px-2 py-1 text-xs text-gray-500 hover:text-[#1768B5] transition flex items-center gap-1.5 {{ request()->is('pelatihan/kelas-saya*') ? 'active text-[#1768B5] font-bold' : '' }}">
                                    <span>• Kelas Terdaftar Saya</span>
                                </a>
                                <a href="{{ route('pelatihan.sertifikat') }}" class="px-2 py-1 text-xs text-gray-500 hover:text-[#1768B5] transition flex items-center gap-1.5 {{ request()->is('pelatihan/sertifikat*') ? 'active text-[#1768B5] font-bold' : '' }}">
                                    <span>• E-Sertifikat Kelulusan</span>
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Manajemen Tugas -->
                    @php
                        $hasMyTasks = check_access('my-tasks.index') || check_access('tasks.my-tasks.index') || check_access('tasks.index') || !empty(Auth::user()->karyawan_id) || (session('role')['nm_role'] ?? '') === 'Karyawan';
                        $hasTasks = check_access('tasks.index');
                        $hasPlotting = check_access('tasks.assignment.index') || check_access('task-assignment.index') || check_access('tasks.update');
                        $hasMonitoring = check_access('tasks.monitoring.index') || check_access('task-monitoring.index') || check_access('tasks.index') || check_access('tasks.update');
                        $hasCategories = check_access('task-categories.index') || check_access('task_categories.index');
                        $hasPriorities = check_access('task-priorities.index') || check_access('task_priorities.index');
                        $hasTaskGroup = false; // Menu-menu pada kelompok Manajemen Tugas di-hide sementara
                    @endphp

                    @if($hasTaskGroup)
                    <div class="flex flex-col gap-1 mb-2">
                        <div class="mb-1 px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                            Manajemen Tugas
                        </div>
                        @if($hasMyTasks)
                        <a href="{{ route('tasks.my-tasks.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('my-tasks*') ? 'active bg-primary/10 text-primary font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="list-todo" class="h-4 w-4 {{ request()->is('my-tasks*') ? 'text-primary' : 'text-gray-400' }}"></i>
                            Tugas Saya
                        </a>
                        @endif
                        @if($hasTasks)
                        <a href="{{ route('tasks.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('tasks*') && !request()->is('task-assignment*') && !request()->is('task-monitoring*') && !request()->is('my-tasks*') ? 'active bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="check-square" class="h-4 w-4 {{ request()->is('tasks*') && !request()->is('task-assignment*') && !request()->is('task-monitoring*') && !request()->is('my-tasks*') ? 'text-primary' : 'text-gray-400' }}"></i>
                            Daftar Tugas
                        </a>
                        @endif
                        @if($hasPlotting)
                        <a href="{{ route('tasks.assignment.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('task-assignment*') ? 'active bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="user-check" class="h-4 w-4 {{ request()->is('task-assignment*') ? 'text-primary' : 'text-gray-400' }}"></i>
                            Plotting Tugas
                        </a>
                        @endif
                        @if($hasMonitoring)
                        <a href="{{ route('tasks.monitoring.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('task-monitoring*') ? 'active bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="clipboard-check" class="h-4 w-4 {{ request()->is('task-monitoring*') ? 'text-primary' : 'text-gray-400' }}"></i>
                            Monitoring Submission
                        </a>
                        @endif
                        @if($hasCategories)
                        <a href="{{ route('task-categories.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('task-categories*') ? 'active bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="tags" class="h-4 w-4 {{ request()->is('task-categories*') ? 'text-primary' : 'text-gray-400' }}"></i>
                            Kategori Tugas
                        </a>
                        @endif
                        @if($hasPriorities)
                        <a href="{{ route('task-priorities.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('task-priorities*') ? 'active bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="alert-circle" class="h-4 w-4 {{ request()->is('task-priorities*') ? 'text-primary' : 'text-gray-400' }}"></i>
                            Prioritas Tugas
                        </a>
                        @endif
                    </div>
                    @endif



                    <!-- Master Data Akademik -->
                    @php
                        $hasMasterPrograms = check_access('master.programs.index');
                        $hasMasterMentors = check_access('master.mentors.index');
                        $hasMasterKelas = check_access('master.kelas.index');
                        $hasMasterMateri = check_access('master.materi.index');
                        $hasMasterMetode = check_access('master.metode.index');
                        $hasMasterGroup = $hasMasterPrograms || $hasMasterMentors || $hasMasterKelas || $hasMasterMateri || $hasMasterMetode;
                    @endphp

                    @if($hasMasterGroup)
                    <div class="flex flex-col gap-1 mb-2">
                        <div class="mb-1 px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                            Master Data
                        </div>

                        <!-- Master Program & Paket Layanan -->
                        @if($hasMasterPrograms)
                        <a href="{{ route('master.programs.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('master/programs*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="layers" class="h-4 w-4 {{ request()->is('master/programs*') ? 'text-[#1768B5]' : 'text-gray-400' }}"></i>
                            Master Program Riset
                        </a>
                        @endif

                        <!-- Master Mentor & Konsultan -->
                        @if($hasMasterMentors)
                        <a href="{{ route('master.mentors.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('master/mentors*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="user-check" class="h-4 w-4 {{ request()->is('master/mentors*') ? 'text-[#1768B5]' : 'text-gray-400' }}"></i>
                            Master Mentor Ahli
                        </a>
                        @endif

                        <!-- Master Kelas & Workshop -->
                        @if($hasMasterKelas)
                        <a href="{{ route('master.kelas.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('master/kelas*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="award" class="h-4 w-4 {{ request()->is('master/kelas*') ? 'text-[#1768B5]' : 'text-gray-400' }}"></i>
                            Master Kelas &amp; Workshop
                        </a>
                        @endif

                        <!-- Master Materi & Silabus -->
                        @if($hasMasterMateri)
                        <a href="{{ route('master.materi.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('master/materi*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="book-open" class="h-4 w-4 {{ request()->is('master/materi*') ? 'text-[#1768B5]' : 'text-gray-400' }}"></i>
                            Master Materi Silabus
                        </a>
                        @endif

                        <!-- Master Metode Riset -->
                        @if($hasMasterMetode)
                        <a href="{{ route('master.metode.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('master/metode*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="microscope" class="h-4 w-4 {{ request()->is('master/metode*') ? 'text-[#1768B5]' : 'text-gray-400' }}"></i>
                            Master Metode Riset
                        </a>
                        @endif
                    </div>
                    @endif

                    <!-- Transaksi Keuangan -->
                    @php
                        $hasKasMasuk = check_access('kas-bank-masuk.index');
                        $hasKasKeluar = check_access('kas-bank-keluar.index');
                        $hasJurnal = check_access('jurnal.index');
                        $hasNeraca = check_access('neraca.index');
                        $hasRugiLaba = check_access('rugi-laba.index');
                        $hasCashFlow = check_access('cashflow.index');
                        $hasKeuanganGroup = $hasKasMasuk || $hasKasKeluar || $hasJurnal || $hasNeraca || $hasRugiLaba || $hasCashFlow;
                    @endphp

                    @if($hasKeuanganGroup)
                    <div class="flex flex-col gap-1 mb-2">
                        <div class="mb-1 px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                            Keuangan &amp; Akuntansi
                        </div>

                        @if($hasKasMasuk)
                        <a href="{{ route('kas-bank-masuk.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('kas-bank-masuk*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="arrow-down-left" class="h-4 w-4 {{ request()->is('kas-bank-masuk*') ? 'text-emerald-600' : 'text-gray-400' }}"></i>
                            Kas / Bank Masuk
                        </a>
                        @endif

                        @if($hasKasKeluar)
                        <a href="{{ route('kas-bank-keluar.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('kas-bank-keluar*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="arrow-up-right" class="h-4 w-4 {{ request()->is('kas-bank-keluar*') ? 'text-rose-600' : 'text-gray-400' }}"></i>
                            Kas / Bank Keluar
                        </a>
                        @endif

                        @if($hasJurnal)
                        <a href="{{ route('jurnal.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('jurnal*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="book-open" class="h-4 w-4 {{ request()->is('jurnal*') ? 'text-[#1768B5]' : 'text-gray-400' }}"></i>
                            Jurnal Umum
                        </a>
                        @endif

                        @if($hasNeraca)
                        <a href="{{ route('neraca.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('neraca*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="scale" class="h-4 w-4 {{ request()->is('neraca*') ? 'text-[#1768B5]' : 'text-gray-400' }}"></i>
                            Neraca Keuangan
                        </a>
                        @endif

                        @if($hasRugiLaba)
                        <a href="{{ route('rugi-laba.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('rugi-laba*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="trending-up" class="h-4 w-4 {{ request()->is('rugi-laba*') ? 'text-[#1768B5]' : 'text-gray-400' }}"></i>
                            Laba Rugi
                        </a>
                        @endif

                        @if($hasCashFlow)
                        <a href="{{ route('cashflow.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('cashflow*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="wallet" class="h-4 w-4 {{ request()->is('cashflow*') ? 'text-[#1768B5]' : 'text-gray-400' }}"></i>
                            Arus Kas (Cash Flow)
                        </a>
                        @endif

                        <a href="{{ route('bimbingan.invoices.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('bimbingan/invoices*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="receipt" class="h-4 w-4 {{ request()->is('bimbingan/invoices*') ? 'text-[#1768B5]' : 'text-gray-400' }}"></i>
                            Rekap Invoice Program
                        </a>

                        <a href="{{ route('bimbingan.pembayaran.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('bimbingan/pembayaran*') ? 'active bg-[#1768B5]/10 text-[#1768B5] font-bold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="credit-card" class="h-4 w-4 {{ request()->is('bimbingan/pembayaran*') ? 'text-[#1768B5]' : 'text-gray-400' }}"></i>
                            List Pembayaran Program
                        </a>
                    </div>
                    @endif

                    <!-- Setting -->
                    @php
                        $hasPerusahaan = check_access('perusahaan.index') || check_access('perusahaan.update');
                        $hasUser = check_access('user.index');
                        $hasRole = check_access('role.index');
                        $hasRoleMenu = check_access('role.index') || check_access('role-menu.index') || check_access('role.update');
                        $hasCoa = check_access('master.coa.index') || check_access('coa.index') || (session('role')['id_role'] ?? 0) == 1;
                        $hasSettingGroup = $hasPerusahaan || $hasUser || $hasRole || $hasRoleMenu || $hasCoa;
                    @endphp

                    @if($hasSettingGroup)
                    <div class="flex flex-col gap-1 mb-2">
                        <div class="mb-1 px-2 text-[11px] font-bold uppercase tracking-wider text-gray-400">
                            Setting
                        </div>
                        @if($hasCoa)
                        <a href="{{ route('master.coa.index') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('master/coa*') ? 'active bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="book-marked" class="h-4 w-4 {{ request()->is('master/coa*') ? 'text-primary' : 'text-gray-400' }}"></i>
                            Master CoA
                        </a>
                        @endif
                        @if($hasPerusahaan)
                        <a href="{{ url('setting/perusahaan') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('setting/perusahaan*') ? 'active bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="settings" class="h-4 w-4 {{ request()->is('setting/perusahaan*') ? 'text-primary' : 'text-gray-400' }}"></i>
                            Setting Akun
                        </a>
                        @endif
                        @if($hasUser)
                        <a href="{{ url('user') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('user*') ? 'active bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="user-cog" class="h-4 w-4 {{ request()->is('user*') ? 'text-primary' : 'text-gray-400' }}"></i>
                            Manajemen User
                        </a>
                        @endif
                        @if($hasRole)
                        <a href="{{ url('roles') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('roles*') ? 'active bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="shield" class="h-4 w-4 {{ request()->is('roles*') ? 'text-primary' : 'text-gray-400' }}"></i>
                            Manajemen Role
                        </a>
                        @endif
                        @if($hasRoleMenu)
                        <a href="{{ url('role-menu') }}" class="flex items-center gap-3 px-3 py-2 rounded-md transition-colors font-medium text-sm {{ request()->is('role-menu*') ? 'active bg-gray-100 text-gray-900 font-semibold' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                            <i data-lucide="key-round" class="h-4 w-4 {{ request()->is('role-menu*') ? 'text-primary' : 'text-gray-400' }}"></i>
                            Hak Akses & Permission
                        </a>
                        @endif
                    </div>
                    @endif
                </div>
            </div>

            <!-- Bottom User Profile Section -->
            <div class="absolute bottom-0 w-full border-t border-gray-200 p-4 bg-white">
                <div class="flex items-center gap-3 rounded-lg border border-gray-200 bg-gray-50 p-3">
                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-gray-200">
                        <i data-lucide="user" class="h-5 w-5 text-gray-600"></i>
                    </div>
                    <div class="flex flex-col overflow-hidden min-w-0">
                        <span class="text-sm font-semibold text-gray-900 truncate" title="{{ Auth::user()->nm_user ?? 'User Persada' }}">{{ Auth::user()->nm_user ?? 'User Persada' }}</span>
                        <span class="text-xs font-medium text-primary truncate flex items-center gap-1" title="Role: {{ Auth::user()->active_role_name ?? (session('role')['nm_role'] ?? 'User') }}">
                            <i data-lucide="shield" class="h-3 w-3 shrink-0"></i>
                            <span class="truncate">{{ Auth::user()->active_role_name ?? (session('role')['nm_role'] ?? 'User') }}</span>
                        </span>
                    </div>
                </div>
                <a href="{{ url('auth/logout') }}" class="mt-3 flex w-full items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-red-600 transition-colors hover:bg-red-50 hover:text-red-700">
                    <i data-lucide="log-out" class="h-4 w-4"></i>
                    Keluar
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            <header class="flex items-center gap-4 h-16 px-4 sm:px-6 lg:px-8 bg-white border-b border-gray-200 shadow-sm z-10 shrink-0">
                <button id="logis-sidebar-toggle" class="lg:hidden text-gray-500 hover:text-gray-700">
                    <i data-lucide="menu" class="h-6 w-6"></i>
                </button>
                
                <div class="flex-1">
                    <h1 class="text-lg font-semibold text-gray-900">
                        @yield('page_title', 'Dashboard')
                    </h1>
                </div>

                <div>
                    @yield('toolbar_actions')
                </div>
            </header>

            <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8 bg-gray-50/50">
                <div class="mx-auto max-w-7xl space-y-6">
                    <!-- Notifications -->
                    @if(Session::has('success'))
                        <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i data-lucide="check-circle-2" class="h-5 w-5 text-emerald-600"></i>
                                <span>{{ Session::get('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(Session::has('error'))
                        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <i data-lucide="alert-circle" class="h-5 w-5 text-red-600"></i>
                                <span>{{ Session::get('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('assets/vendors/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/select2/select2.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/datatables-net/dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets-logistik/js/logistik.js') }}?v=1.3"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        });
    </script>

    <!-- Global DataTables Loading Indicator Setup -->
    <script>
        (function () {
            var dtSpinnerHtml = '<div class="dt-spinner-only" style="display: flex !important; align-items: center !important; justify-content: center !important; pointer-events: none !important; width: 100% !important;">' +
                '<svg style="width: 42px; height: 42px; min-width: 42px; animation: dt-spin 0.75s linear infinite;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">' +
                '<circle cx="12" cy="12" r="10" stroke="#fecdd3" stroke-width="3.5" fill="none"></circle>' +
                '<path fill="#e11d48" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>' +
                '</svg>' +
                '</div>';

            window.dtSpinnerHtml = dtSpinnerHtml;

            function setupDtDefaults() {
                if (typeof jQuery !== 'undefined' && jQuery.fn && jQuery.fn.dataTable) {
                    jQuery.extend(true, jQuery.fn.dataTable.defaults, {
                        processing: true,
                        language: {
                            processing: dtSpinnerHtml
                        }
                    });
                }
            }
            window.setupDtDefaults = setupDtDefaults;

            function getProcessingEl(settings) {
                if (!settings) return null;
                var $proc = jQuery('#' + settings.sTableId + '_processing');
                if (!$proc.length && settings.nTableWrapper) {
                    $proc = jQuery(settings.nTableWrapper).find('.dt-processing, .dataTables_processing');
                }
                if (!$proc.length && settings.nTable) {
                    var $wrapper = jQuery(settings.nTable).closest('.dt-container, .dataTables_wrapper, div[id$="_wrapper"]');
                    $proc = $wrapper.find('.dt-processing, .dataTables_processing');
                    if (!$proc.length && $wrapper.length) {
                        $proc = jQuery('<div id="' + settings.sTableId + '_processing" class="dt-processing" role="status"></div>');
                        $wrapper.prepend($proc);
                    }
                }
                return $proc;
            }

            window.showDtLoading = function (settings) {
                if (!settings || typeof jQuery === 'undefined') return;
                var $table = jQuery(settings.nTable);
                var $proc = getProcessingEl(settings);
                $table.addClass('dt-table-loading');
                if ($proc && $proc.length) {
                    $proc.addClass('is-loading').html(dtSpinnerHtml).attr('style', 'display: flex !important; position: absolute !important; top: 50% !important; left: 50% !important; transform: translate(-50%, -50%) !important; z-index: 9999 !important; margin: 0 !important; padding: 0 !important; background: transparent !important; border: none !important; box-shadow: none !important; width: auto !important; height: auto !important; pointer-events: none !important;');
                }
            };

            window.hideDtLoading = function (settings) {
                if (!settings || typeof jQuery === 'undefined') return;
                var $table = jQuery(settings.nTable);
                var $proc = getProcessingEl(settings);
                $table.removeClass('dt-table-loading');
                if ($proc && $proc.length) {
                    $proc.removeClass('is-loading').attr('style', 'display: none !important;').hide();
                }
            };

            window.bindDtTable = function (settings) {
                if (!settings || !settings.nTable || typeof jQuery === 'undefined') return;
                jQuery(settings.nTable).off('processing.dt.logisDirect').on('processing.dt.logisDirect', function (ev, s, processing) {
                    if (processing) {
                        window.showDtLoading(settings);
                    } else {
                        window.hideDtLoading(settings);
                    }
                });
            };

            setupDtDefaults();

            if (typeof jQuery !== 'undefined') {
                jQuery(document).off('preXhr.dt.logisSpinner').on('preXhr.dt.logisSpinner', function (e, settings) {
                    window.showDtLoading(settings);
                });

                jQuery(document).off('draw.dt.logisSpinner').on('draw.dt.logisSpinner', function (e, settings) {
                    window.hideDtLoading(settings);
                });

                jQuery(document).off('xhr.dt.logisSpinner').on('xhr.dt.logisSpinner', function (e, settings) {
                    window.hideDtLoading(settings);
                });

                jQuery(document).off('init.dt.logisSpinner').on('init.dt.logisSpinner', function (e, settings) {
                    window.bindDtTable(settings);
                });
            }
        })();
    </script>

    @stack('scripts')

    <!-- Post-Script DataTables Sweep & Reinforce -->
    <script>
        (function () {
            if (typeof jQuery !== 'undefined') {
                if (typeof window.setupDtDefaults === 'function') {
                    window.setupDtDefaults();
                }
                jQuery(document).off('preXhr.dt.logisSpinner').on('preXhr.dt.logisSpinner', function (e, settings) {
                    if (typeof window.showDtLoading === 'function') window.showDtLoading(settings);
                });
                jQuery(document).off('draw.dt.logisSpinner').on('draw.dt.logisSpinner', function (e, settings) {
                    if (typeof window.hideDtLoading === 'function') window.hideDtLoading(settings);
                });
                jQuery(document).off('xhr.dt.logisSpinner').on('xhr.dt.logisSpinner', function (e, settings) {
                    if (typeof window.hideDtLoading === 'function') window.hideDtLoading(settings);
                });
                jQuery(document).off('init.dt.logisSpinner').on('init.dt.logisSpinner', function (e, settings) {
                    if (typeof window.bindDtTable === 'function') window.bindDtTable(settings);
                });

                jQuery(document).ready(function () {
                    if (jQuery.fn && jQuery.fn.dataTable && jQuery.fn.dataTable.settings) {
                        jQuery.each(jQuery.fn.dataTable.settings, function (i, settings) {
                            if (typeof window.bindDtTable === 'function') {
                                window.bindDtTable(settings);
                            }
                        });
                    }
                });
            }
        })();
    </script>
</body>
</html>
