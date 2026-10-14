        <!-- Dashboard - Dropdown -->
        @php
            $dashboardActive = request()->routeIs('dashboard') || request()->routeIs('dashboard.omset') || request()->is('dashboard*') || request()->is('/');
        @endphp
        <div class="kt-menu-item{{ $dashboardActive ? ' active' : '' }}"
             data-kt-menu-item-offset="-10px, 14px"
             data-kt-menu-item-overflow="true"
             data-kt-menu-item-placement="right-start"
             data-kt-menu-item-toggle="dropdown"
             data-kt-menu-item-trigger="click|lg:hover">
            <div class="kt-menu-link rounded-[9px] border border-transparent kt-menu-item-here:border-border kt-menu-item-here:bg-background kt-menu-link-hover:bg-background kt-menu-link-hover:border-border w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                <span class="kt-menu-icon kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary text-secondary-foreground">
                    <i class="ki-filled ki-chart-line-star text-xl"></i>
                </span>
                <span class="kt-menu-title kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary font-medium text-xs text-secondary-foreground">
                    Dashboard
                </span>
            </div>
            <div class="kt-menu-default kt-menu-dropdown gap-0.5 w-[220px] kt-scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                <div class="kt-menu-item{{ (request()->routeIs('dashboard') || request()->is('dashboard') || request()->is('/')) && !request()->is('dashboard/omset*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('dashboard') }}">
                        <span class="kt-menu-title">Monitoring Kepegawaian</span>
                    </a>
                </div>
                <div class="kt-menu-item{{ request()->routeIs('dashboard.omset') || request()->is('dashboard/omset*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('dashboard.omset') }}">
                        <span class="kt-menu-title">Pemantauan Omset</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Manajemen Tugas - Dropdown -->
        @php
            $hasMyTasks = check_access('my-tasks.index') || check_access('tasks.my-tasks.index') || check_access('tasks.index') || !empty(Auth::user()->karyawan_id) || (session('role')['nm_role'] ?? '') === 'Karyawan';
            $hasTasks = check_access('tasks.index');
            $hasPlotting = check_access('tasks.assignment.index') || check_access('task-assignment.index') || check_access('tasks.update');
            $hasMonitoring = check_access('tasks.monitoring.index') || check_access('task-monitoring.index') || check_access('tasks.index') || check_access('tasks.update');
            $hasCategories = check_access('task-categories.index') || check_access('task_categories.index');
            $hasPriorities = check_access('task-priorities.index') || check_access('task_priorities.index');
            $hasTaskMenu = false; // Menu-menu pada kelompok Manajemen Tugas di-hide sementara
            $taskActive = request()->is('my-tasks*') || (request()->is('tasks*') && !request()->is('task-assignment*')) || request()->is('task-assignment*') || request()->is('task-monitoring*') || request()->is('task-categories*') || request()->is('task-priorities*');
        @endphp
        @if($hasTaskMenu)
        <div class="kt-menu-item{{ $taskActive ? ' active' : '' }}"
             data-kt-menu-item-offset="-10px, 14px"
             data-kt-menu-item-overflow="true"
             data-kt-menu-item-placement="right-start"
             data-kt-menu-item-toggle="dropdown"
             data-kt-menu-item-trigger="click|lg:hover">
            <div class="kt-menu-link rounded-[9px] border border-transparent kt-menu-item-here:border-border kt-menu-item-here:bg-background kt-menu-link-hover:bg-background kt-menu-link-hover:border-border w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                <span class="kt-menu-icon kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary text-secondary-foreground">
                    <i class="ki-filled ki-check-circle text-xl"></i>
                </span>
                <span class="kt-menu-title kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary font-medium text-xs text-secondary-foreground">
                    Tugas
                </span>
            </div>
            <div class="kt-menu-default kt-menu-dropdown gap-0.5 w-[220px] kt-scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                @if($hasMyTasks)
                <div class="kt-menu-item{{ request()->is('my-tasks*') ? ' active' : '' }}">
                    <a class="kt-menu-link font-bold text-primary" href="{{ route('tasks.my-tasks.index') }}">
                        <span class="kt-menu-title">Tugas Saya</span>
                    </a>
                </div>
                @endif
                @if($hasTasks)
                <div class="kt-menu-item{{ request()->is('tasks*') && !request()->is('task-assignment*') && !request()->is('my-tasks*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('tasks.index') }}">
                        <span class="kt-menu-title">Daftar Tugas</span>
                    </a>
                </div>
                @endif
                @if($hasPlotting)
                <div class="kt-menu-item{{ request()->is('task-assignment*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('tasks.assignment.index') }}">
                        <span class="kt-menu-title">Plotting Tugas</span>
                    </a>
                </div>
                @endif
                @if($hasMonitoring)
                <div class="kt-menu-item{{ request()->is('task-monitoring*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('tasks.monitoring.index') }}">
                        <span class="kt-menu-title">Monitoring Submission</span>
                    </a>
                </div>
                @endif
                @if($hasCategories)
                <div class="kt-menu-item{{ request()->is('task-categories*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('task-categories.index') }}">
                        <span class="kt-menu-title">Kategori Tugas</span>
                    </a>
                </div>
                @endif
                @if($hasPriorities)
                <div class="kt-menu-item{{ request()->is('task-priorities*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('task-priorities.index') }}">
                        <span class="kt-menu-title">Prioritas Tugas</span>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Manajemen Omset - Dropdown -->
        @php
            $hasInputOmset = check_access('input-omset.index');
            $hasSettingFee = check_access('setting-fee-omset.index');
            $hasOmsetMenu = $hasInputOmset || $hasSettingFee;
            $omsetActive = request()->is('input-omset*') || request()->is('setting-fee-omset*');
        @endphp
        @if($hasOmsetMenu)
        <div class="kt-menu-item{{ $omsetActive ? ' active' : '' }}"
             data-kt-menu-item-offset="-10px, 14px"
             data-kt-menu-item-overflow="true"
             data-kt-menu-item-placement="right-start"
             data-kt-menu-item-toggle="dropdown"
             data-kt-menu-item-trigger="click|lg:hover">
            <div class="kt-menu-link rounded-[9px] border border-transparent kt-menu-item-here:border-border kt-menu-item-here:bg-background kt-menu-link-hover:bg-background kt-menu-link-hover:border-border w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                <span class="kt-menu-icon kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary text-secondary-foreground">
                    <i class="ki-filled ki-finance-calculator text-xl"></i>
                </span>
                <span class="kt-menu-title kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary font-medium text-xs text-secondary-foreground">
                    Omset
                </span>
            </div>
            <div class="kt-menu-default kt-menu-dropdown gap-0.5 w-[220px] kt-scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                @if($hasInputOmset)
                <div class="kt-menu-item{{ request()->is('input-omset*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('input-omset.index') }}">
                        <span class="kt-menu-title">Input Omset</span>
                    </a>
                </div>
                @endif
                @if($hasSettingFee)
                <div class="kt-menu-item{{ request()->is('setting-fee-omset*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('setting-fee-omset.index') }}">
                        <span class="kt-menu-title">Setting Fee Omset</span>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Keuangan - Dropdown -->
        @php
            $hasKasMasuk = check_access('kas-bank-masuk.index');
            $hasKasKeluar = check_access('kas-bank-keluar.index');
            $hasJurnal = check_access('jurnal.index');
            $hasNeraca = check_access('neraca.index');
            $hasRugiLaba = check_access('rugilaba.index');
            $hasKeuangan = $hasKasMasuk || $hasKasKeluar || $hasJurnal || $hasNeraca || $hasRugiLaba;
            $keuanganActive = request()->is('do-keuangan*') || request()->is('invoice*') || request()->is('kas-bank-masuk*') || request()->is('kas-bank-keluar*') || request()->is('jurnal*') || request()->is('neraca*') || request()->is('rugilaba*');
        @endphp
        @if($hasKeuangan)
        <div class="kt-menu-item{{ $keuanganActive ? ' active' : '' }}"
             data-kt-menu-item-offset="-10px, 14px"
             data-kt-menu-item-overflow="true"
             data-kt-menu-item-placement="right-start"
             data-kt-menu-item-toggle="dropdown"
             data-kt-menu-item-trigger="click|lg:hover">
            <div class="kt-menu-link rounded-[9px] border border-transparent kt-menu-item-here:border-border kt-menu-item-here:bg-background kt-menu-link-hover:bg-background kt-menu-link-hover:border-border w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                <span class="kt-menu-icon kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary text-secondary-foreground">
                    <i class="ki-filled ki-credit-cart text-xl"></i>
                </span>
                <span class="kt-menu-title kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary font-medium text-xs text-secondary-foreground">
                    Keuangan
                </span>
            </div>
            <div class="kt-menu-default kt-menu-dropdown gap-0.5 w-[220px] kt-scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                @if($hasKasMasuk)
                <div class="kt-menu-item{{ request()->is('kas-bank-masuk*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ url('kas-bank-masuk') }}">
                        <span class="kt-menu-title">Kas/Bank Masuk</span>
                    </a>
                </div>
                @endif
                @if($hasKasKeluar)
                <div class="kt-menu-item{{ request()->is('kas-bank-keluar*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ url('kas-bank-keluar') }}">
                        <span class="kt-menu-title">Kas/Bank Keluar</span>
                    </a>
                </div>
                @endif
                @if($hasJurnal)
                <div class="kt-menu-item{{ request()->is('jurnal*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ url('jurnal') }}">
                        <span class="kt-menu-title">Jurnal</span>
                    </a>
                </div>
                @endif
                @if($hasNeraca)
                <div class="kt-menu-item{{ request()->is('neraca*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ url('neraca') }}">
                        <span class="kt-menu-title">Neraca</span>
                    </a>
                </div>
                @endif
                @if($hasRugiLaba)
                <div class="kt-menu-item{{ request()->is('rugilaba*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ url('rugilaba') }}">
                        <span class="kt-menu-title">Rugi Laba</span>
                    </a>
                </div>
                @endif
                <div class="kt-menu-item{{ request()->is('bimbingan/invoices*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('bimbingan.invoices.index') }}">
                        <span class="kt-menu-title">Rekap Invoice Program</span>
                    </a>
                </div>
                <div class="kt-menu-item{{ request()->is('bimbingan/pembayaran*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('bimbingan.pembayaran.index') }}">
                        <span class="kt-menu-title">List Pembayaran Program</span>
                    </a>
                </div>
            </div>
        </div>
        @endif

        <!-- SDM - Dropdown -->
        @php
            $hasKaryawanSdm = check_access('karyawan.index');
            $hasAbsensi = check_access('absensi.index');
            $hasPayroll = check_access('payroll-period.index') || check_access('payroll-slip.index');
            $hasPositionsSdm = check_access('positions.index') || check_access('jabatan.index');
            $hasKpi = check_access('employee-activities.index') || check_access('daily-report.index') || check_access('kpi.index');
            $hasSdm = $hasKaryawanSdm || $hasAbsensi || $hasPayroll || $hasPositionsSdm || $hasKpi;
            $sdmActive = request()->is('karyawan*') || request()->is('absensi*') || request()->is('payroll-*') || request()->is('jabatan*') || request()->is('kpi*');
        @endphp
        @if($hasSdm)
        <div class="kt-menu-item{{ $sdmActive ? ' active' : '' }}"
             data-kt-menu-item-offset="-10px, 14px"
             data-kt-menu-item-overflow="true"
             data-kt-menu-item-placement="right-start"
             data-kt-menu-item-toggle="dropdown"
             data-kt-menu-item-trigger="click|lg:hover">
            <div class="kt-menu-link rounded-[9px] border border-transparent kt-menu-item-here:border-border kt-menu-item-here:bg-background kt-menu-link-hover:bg-background kt-menu-link-hover:border-border w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                <span class="kt-menu-icon kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary text-secondary-foreground">
                    <i class="ki-filled ki-users text-xl"></i>
                </span>
                <span class="kt-menu-title kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary font-medium text-xs text-secondary-foreground">
                    SDM
                </span>
            </div>
            <div class="kt-menu-default kt-menu-dropdown gap-0.5 w-[220px] kt-scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                @if($hasKaryawanSdm)
                <div class="kt-menu-item{{ request()->is('karyawan*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ url('karyawan') }}">
                        <span class="kt-menu-title">Karyawan</span>
                    </a>
                </div>
                @endif
                @if($hasAbsensi)
                <div class="kt-menu-item{{ request()->is('absensi*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ url('absensi') }}">
                        <span class="kt-menu-title">Absensi</span>
                    </a>
                </div>
                @endif
                @if($hasPayroll)
                <div class="kt-menu-item{{ request()->is('payroll-*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ url('payroll-period') }}">
                        <span class="kt-menu-title">Payroll</span>
                    </a>
                </div>
                @endif
                @if($hasPositionsSdm)
                <div class="kt-menu-item{{ request()->is('jabatan*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ url('jabatan') }}">
                        <span class="kt-menu-title">Jabatan</span>
                    </a>
                </div>
                @endif
                @if($hasKpi)
                <div class="kt-menu-item{{ request()->is('kpi*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ url('kpi') }}">
                        <span class="kt-menu-title">KPI</span>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif




        <!-- Master Data - Dropdown -->
        @php
            $hasCompanies = check_access('companies.index');
            $hasDepartemens = check_access('departemens.index');
            $hasPositions = check_access('positions.index');
            $hasPlatforms = check_access('platforms.index');
            $hasTokos = check_access('tokos.index') || check_access('toko.index');
            $hasProduks = check_access('produks.index') || check_access('produk.index');
            $hasKaryawan = check_access('karyawan.index');
            $hasWilayah = check_access('wilayah-provinsi.index') || check_access('wilayah-kota-kab.index') || check_access('wilayah-kecamatan.index') || check_access('wilayah-desa.index');
            $hasMaster = $hasCompanies || $hasDepartemens || $hasPositions || $hasPlatforms || $hasTokos || $hasProduks || $hasKaryawan || $hasWilayah;
            $masterActive = request()->is('master/companies*') || request()->is('master/departemens*') || request()->is('master/departments*') || request()->is('master/positions*') || request()->is('master/platforms*') || request()->is('master/platform*') || request()->is('master/tokos*') || request()->is('master/toko*') || request()->is('master/produks*') || request()->is('master/produk*') || request()->is('master/karyawan*') || request()->is('master/karyawans*') || request()->is('master/*');
        @endphp
        @if($hasMaster)
        <div class="kt-menu-item{{ $masterActive ? ' active' : '' }}"
             data-kt-menu-item-offset="-10px, 14px"
             data-kt-menu-item-overflow="true"
             data-kt-menu-item-placement="right-start"
             data-kt-menu-item-toggle="dropdown"
             data-kt-menu-item-trigger="click|lg:hover">
            <div class="kt-menu-link rounded-[9px] border border-transparent kt-menu-item-here:border-border kt-menu-item-here:bg-background kt-menu-link-hover:bg-background kt-menu-link-hover:border-border w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow">
                <span class="kt-menu-icon kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary text-secondary-foreground">
                    <i class="ki-filled ki-document text-xl"></i>
                </span>
                <span class="kt-menu-title kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary font-medium text-xs text-secondary-foreground">
                    Master
                </span>
            </div>
            <div class="kt-menu-default kt-menu-dropdown gap-0.5 w-[220px] kt-scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                @if($hasCompanies)
                <div class="kt-menu-item{{ request()->is('master/companies*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('companies.index') }}">
                        <span class="kt-menu-title">Companies</span>
                    </a>
                </div>
                @endif
                @if($hasDepartemens)
                <div class="kt-menu-item{{ request()->is('master/departemens*') || request()->is('master/departments*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('departemens.index') }}">
                        <span class="kt-menu-title">Departemen</span>
                    </a>
                </div>
                @endif
                @if($hasPlatforms)
                <div class="kt-menu-item{{ request()->is('master/platform*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('platforms.index') }}">
                        <span class="kt-menu-title">Platform</span>
                    </a>
                </div>
                @endif
                @if($hasPositions)
                <div class="kt-menu-item{{ request()->is('master/positions*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('positions.index') }}">
                        <span class="kt-menu-title">Jabatan</span>
                    </a>
                </div>
                @endif
                @if($hasTokos)
                <div class="kt-menu-item{{ request()->is('master/toko*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('tokos.index') }}">
                        <span class="kt-menu-title">Toko</span>
                    </a>
                </div>
                @endif
                @if($hasProduks)
                <div class="kt-menu-item{{ request()->is('master/produk*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('produks.index') }}">
                        <span class="kt-menu-title">Produk</span>
                    </a>
                </div>
                @endif
                @if($hasKaryawan)
                <div class="kt-menu-item{{ request()->is('master/karyawan*') || request()->is('master/karyawans*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('karyawan.index') }}">
                        <span class="kt-menu-title">Karyawan</span>
                    </a>
                </div>
                @endif
                @if($hasWilayah)
                <div class="kt-menu-divider my-2"></div>
                <div class="kt-menu-item{{ request()->is('master/provinsi*') || request()->is('master/kota-kab*') || request()->is('master/kecamatan*') || request()->is('master/desa*') || request()->is('master/wilayah*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('wilayah.index') }}">
                        <span class="kt-menu-title">Wilayah</span>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif

        <!-- Settings - Dropdown -->
        @php
            $hasPerusahaan = check_access('perusahaan.index') || check_access('perusahaan.update');
            $hasUser = check_access('user.index');
            $hasRole = check_access('role.index');
            $hasRoleMenu = check_access('role.index') || check_access('role-menu.index') || check_access('role.update');
            $hasCoa = check_access('master.coa.index') || check_access('coa.index') || (session('role')['id_role'] ?? 0) == 1;
            $hasSettings = $hasPerusahaan || $hasUser || $hasRole || $hasRoleMenu || $hasCoa;
            $settingsActive = request()->is('master/coa*') || request()->is('perusahaan*') || request()->is('setting/perusahaan*') || request()->is('user*') || request()->is('roles*') || request()->is('role-menu*');
        @endphp
        @if($hasSettings)
        <div class="kt-menu-item{{ $settingsActive ? ' active' : '' }}"
             data-kt-menu-item-offset="-10px, 14px"
             data-kt-menu-item-overflow="true"
             data-kt-menu-item-placement="right-start"
             data-kt-menu-item-toggle="dropdown"
             data-kt-menu-item-trigger="click|lg:hover">
            <div class="kt-menu-link rounded-[9px] border border-transparent kt-menu-item-here:border-border kt-menu-item-here:bg-background kt-menu-link-hover:bg-background kt-menu-link-hover:border-border w-[62px] h-[60px] flex flex-col justify-center items-center gap-1 p-2 grow cursor-pointer">
                <span class="kt-menu-icon kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary text-secondary-foreground">
                    <i class="ki-filled ki-setting-2 text-xl"></i>
                </span>
                <span class="kt-menu-title kt-menu-item-here:text-primary kt-menu-item-active:text-primary kt-menu-link-hover:text-primary font-medium text-xs text-secondary-foreground">
                    Setting
                </span>
            </div>
            <div class="kt-menu-default kt-menu-dropdown gap-0.5 w-[220px] kt-scrollable-y-auto lg:overflow-visible max-h-[50vh]">
                @if($hasCoa)
                <div class="kt-menu-item{{ request()->is('master/coa*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('master.coa.index') }}">
                        <span class="kt-menu-title">Master CoA</span>
                    </a>
                </div>
                @endif
                @if($hasPerusahaan)
                <div class="kt-menu-item{{ request()->is('perusahaan*') || request()->is('setting/perusahaan*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ url('setting/perusahaan') }}">
                        <span class="kt-menu-title">Setting Perusahaan</span>
                    </a>
                </div>
                @endif
                @if($hasUser)
                <div class="kt-menu-item{{ request()->is('user*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('users.index') }}">
                        <span class="kt-menu-title">Manajemen User</span>
                    </a>
                </div>
                @endif
                @if($hasRole)
                <div class="kt-menu-item{{ request()->is('roles*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('roles.index') }}">
                        <span class="kt-menu-title">Manajemen Role</span>
                    </a>
                </div>
                @endif
                @if($hasRoleMenu)
                <div class="kt-menu-item{{ request()->is('role-menu*') ? ' active' : '' }}">
                    <a class="kt-menu-link" href="{{ route('role-menu.index') }}">
                        <span class="kt-menu-title">Hak Akses & Permission</span>
                    </a>
                </div>
                @endif
            </div>
        </div>
        @endif
