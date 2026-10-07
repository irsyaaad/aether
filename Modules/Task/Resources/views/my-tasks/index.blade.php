@extends('layouts.app')

@section('title', 'Tugas Saya - ' . config('app.name'))
@section('page_title', 'Tugas Saya')

@section('toolbar_actions')
<div class="flex items-center gap-2">
    @if($employee)
    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-white border border-gray-200 rounded-lg shadow-2xs">
        <div class="w-6 h-6 rounded-full bg-primary/10 text-primary font-bold text-[11px] flex items-center justify-center">
            {{ strtoupper(substr($employee->display_name ?? 'K', 0, 1)) }}
        </div>
        <div class="flex flex-col text-left">
            <span class="text-xs font-bold text-gray-800 leading-tight">{{ $employee->display_name }}</span>
            <span class="text-[10px] text-gray-400 leading-tight">{{ $employee->position->name ?? ($employee->jabatan ?? 'Staf Operasional') }}</span>
        </div>
    </div>
    @endif
    <button type="button" onclick="table.ajax.reload()" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-2xs">
        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-gray-500"></i>
        Refresh
    </button>
</div>
@endsection

@push('styles')
<style>
    #my-tasks-table_wrapper { width: 100% !important; }
    #my-tasks-table_wrapper .dt-layout-row { width: 100% !important; }
    #my-tasks-table_wrapper .dt-layout-cell { padding: 0 !important; }
    #my-tasks-table_wrapper .dt-layout-table {
        width: 100% !important;
        overflow-x: auto !important;
        display: block !important;
    }
    #my-tasks-table_wrapper table { width: 100% !important; border-collapse: collapse !important; }

    #my-tasks-table_wrapper .dt-search { display: none; }
    #my-tasks-table_wrapper .dt-length { display: none; }

    #my-tasks-table_wrapper .dt-layout-row:first-child:not(:has(.dt-paging)):not(:has(.dt-info)) {
        display: none !important;
    }

    /* Bottom Controls: Info & Pagination */
    #my-tasks-table_wrapper .dt-layout-table + .dt-layout-row,
    #my-tasks-table_wrapper .dt-layout-row:has(.dt-info),
    #my-tasks-table_wrapper .dt-layout-row:has(.dt-paging),
    #my-tasks-table_wrapper .dt-layout-row:last-child {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 1rem !important;
        padding: 1.25rem 1.5rem !important;
        border-top: 1px solid #e5e7eb !important;
        background-color: #ffffff !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    #my-tasks-table_wrapper .dt-info {
        font-size: 0.8125rem !important;
        color: #4b5563 !important;
        font-weight: 500 !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.5 !important;
    }

    #my-tasks-table_wrapper .dt-paging {
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 0.25rem !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    #my-tasks-table_wrapper .dt-paging button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 2rem !important;
        height: 2rem !important;
        padding: 0 0.5rem !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        border-radius: 0.375rem !important;
        border: 1px solid #e5e7eb !important;
        background-color: #ffffff !important;
        color: #374151 !important;
        cursor: pointer !important;
        transition: all 0.15s ease-in-out !important;
    }
    #my-tasks-table_wrapper .dt-paging button:hover:not(.current):not(:disabled) {
        background-color: #f9fafb !important;
        border-color: #d1d5db !important;
        color: #111827 !important;
    }
    #my-tasks-table_wrapper .dt-paging button.current {
        background-color: var(--color-primary, #e11d48) !important;
        border-color: var(--color-primary, #e11d48) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    #my-tasks-table_wrapper .dt-paging button:disabled {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- Greeting Banner -->
    <div class="bg-gradient-to-r from-rose-600 via-pink-600 to-primary rounded-2xl p-6 text-white shadow-sm relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-white/20 text-white text-xs font-semibold backdrop-blur-xs mb-2">
                <i data-lucide="sparkles" class="w-3.5 h-3.5 text-amber-300"></i>
                Workspace Pengerjaan Tugas
            </span>
            <h2 class="text-xl sm:text-2xl font-bold">
                Semangat Kerja, {{ $employee->display_name ?? 'Rekan Tim' }}!
            </h2>
            <p class="text-white/80 text-xs sm:text-sm mt-1 leading-relaxed">
                Berikut adalah daftar tugas yang telah diploting kepada Anda. Kerjakan tugas harian, catat realisasi target, dan laporkan hasil update operasional secara realtime.
            </p>
        </div>
        <div class="absolute -right-8 -bottom-10 opacity-15 pointer-events-none hidden md:block">
            <i data-lucide="check-check" class="w-64 h-64 text-white"></i>
        </div>
    </div>

    @if(!$employee)
        <div class="p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-900 text-xs sm:text-sm flex items-start sm:items-center gap-3 shadow-xs">
            <i data-lucide="alert-circle" class="w-5 h-5 text-amber-600 shrink-0 mt-0.5 sm:mt-0"></i>
            <div>
                <p class="font-semibold text-amber-800">Akun Belum Terhubung dengan Data Karyawan</p>
                <p class="text-amber-700 text-xs mt-0.5 leading-relaxed">Akun login Anda saat ini belum ditautkan dengan data karyawan di sistem. Tugas di halaman ini hanya muncul untuk user karyawan yang sudah diplotingkan tugasnya.</p>
            </div>
        </div>
    @elseif($stats['total'] === 0)
        <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-900 text-xs sm:text-sm flex items-start sm:items-center gap-3 shadow-xs">
            <i data-lucide="info" class="w-5 h-5 text-blue-600 shrink-0 mt-0.5 sm:mt-0"></i>
            <div>
                <p class="font-semibold text-blue-800">Belum Ada Tugas yang Diploting</p>
                <p class="text-blue-700 text-xs mt-0.5 leading-relaxed">Halo, <strong>{{ $employee->display_name }}</strong>. Saat ini belum ada tugas yang diplotingkan kepada Anda melalui menu Plotting Tugas.</p>
            </div>
        </div>
    @endif

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between cursor-pointer hover:border-primary/50 transition-colors" onclick="setTab('active')">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Tugas Aktif</p>
                <h3 class="text-2xl font-bold text-primary mt-0.5">{{ $stats['active'] }}</h3>
            </div>
            <div class="p-3 bg-rose-50 text-primary rounded-xl">
                <i data-lucide="zap" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between cursor-pointer hover:border-cyan-400 transition-colors" onclick="$('#filter-task-type').val('harian'); table.draw();">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Tugas Harian Hari Ini</p>
                <h3 class="text-2xl font-bold text-cyan-600 mt-0.5">{{ $stats['daily_today'] }}</h3>
            </div>
            <div class="p-3 bg-cyan-50 text-cyan-600 rounded-xl">
                <i data-lucide="repeat" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between cursor-pointer hover:border-emerald-400 transition-colors" onclick="setTab('completed')">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Telah Selesai</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-0.5">{{ $stats['completed'] }}</h3>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="bg-white p-4 sm:p-5 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between cursor-pointer hover:border-slate-400 transition-colors" onclick="setTab('expired')">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-400">Kadaluarsa</p>
                <h3 class="text-2xl font-bold text-slate-700 mt-0.5">{{ $stats['expired'] }}</h3>
            </div>
            <div class="p-3 bg-slate-100 text-slate-600 rounded-xl">
                <i data-lucide="clock-alert" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-xs space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-xs font-bold text-gray-800 uppercase tracking-wider flex items-center gap-1.5">
                <i data-lucide="filter" class="w-4 h-4 text-primary"></i>
                Filter Pengerjaan Tugas
            </h4>
            <button type="button" id="btn-reset-filters" class="text-xs text-gray-500 hover:text-primary transition-colors flex items-center gap-1 font-medium">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                Reset Filter
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-medium text-gray-600 mb-1">Cari Tugas</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                    <input type="text" id="filter-search" placeholder="Cari nomor atau judul tugas..." class="w-full pl-9 pr-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                </div>
            </div>

            <!-- Tipe Tugas Filter -->
            <div>
                <label class="block text-[11px] font-medium text-gray-600 mb-1">Tipe / Sifat</label>
                <select id="filter-task-type" class="w-full py-2 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Tipe</option>
                    @foreach($taskTypes as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Bentuk Hasil Filter -->
            <div>
                <label class="block text-[11px] font-medium text-gray-600 mb-1">Bentuk Hasil</label>
                <select id="filter-output-type" class="w-full py-2 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Bentuk</option>
                    <option value="checklist">Checklist (Selesai/Belum)</option>
                    <option value="target">Target Angka / Nilai</option>
                    <option value="data_list">List Jawaban / Data</option>
                    <option value="stock_update">Update Stock Kosong</option>
                    <option value="full_custom">Full Custom</option>
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-[11px] font-medium text-gray-600 mb-1">Status</label>
                <select id="filter-status" class="w-full py-2 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-semibold">
        <button type="button" class="my-tasks-tab px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 bg-primary text-white shadow-2xs" data-tab="active">
            <i data-lucide="zap" class="w-3.5 h-3.5"></i>
            Tugas Aktif
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-white/20 text-white font-bold ml-0.5">{{ $stats['active'] }}</span>
        </button>
        <button type="button" class="my-tasks-tab px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 bg-white text-gray-600 border border-gray-200 hover:bg-gray-50" data-tab="completed">
            <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
            Telah Selesai
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-50 text-emerald-700 font-bold ml-0.5 border border-emerald-200">{{ $stats['completed'] }}</span>
        </button>
        <button type="button" class="my-tasks-tab px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 bg-white text-gray-600 border border-gray-200 hover:bg-gray-50" data-tab="expired">
            <i data-lucide="clock-alert" class="w-3.5 h-3.5 text-slate-500"></i>
            Kadaluarsa
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-100 text-slate-700 font-bold ml-0.5 border border-slate-200">{{ $stats['expired'] }}</span>
        </button>
        <button type="button" class="my-tasks-tab px-4 py-2.5 rounded-xl transition-all flex items-center gap-2 bg-white text-gray-600 border border-gray-200 hover:bg-gray-50" data-tab="all">
            <i data-lucide="layers" class="w-3.5 h-3.5 text-blue-500"></i>
            Semua Tugas
            <span class="px-2 py-0.5 rounded-full text-[10px] bg-gray-100 text-gray-600 font-bold ml-0.5">{{ $stats['total'] }}</span>
        </button>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left" id="my-tasks-table">
                <thead class="bg-gray-50 text-gray-600 font-semibold border-b border-gray-200 uppercase text-[10px] tracking-wider">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center">#</th>
                        <th class="py-3.5 px-4 w-28">No. Tugas</th>
                        <th class="py-3.5 px-4 min-w-[220px]">Judul Tugas</th>
                        <th class="py-3.5 px-4 w-28">Prioritas</th>
                        <th class="py-3.5 px-4 w-32">Tenggat Waktu</th>
                        <th class="py-3.5 px-4 w-28">Progress</th>
                        <th class="py-3.5 px-4 w-28">Status</th>
                        <th class="py-3.5 px-4 w-28 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Loaded via DataTables -->
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let table;
    let currentTab = 'active';

    function setTab(tabName) {
        currentTab = tabName;
        $('.my-tasks-tab').removeClass('bg-primary text-white shadow-2xs').addClass('bg-white text-gray-600 border border-gray-200 hover:bg-gray-50');
        $(`.my-tasks-tab[data-tab="${tabName}"]`).removeClass('bg-white text-gray-600 border border-gray-200 hover:bg-gray-50').addClass('bg-primary text-white shadow-2xs');
        if (typeof table !== 'undefined') {
            table.draw();
        }
    }

    $(document).ready(function() {
        table = $('#my-tasks-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('tasks.my-tasks.get-data') }}",
                data: function(d) {
                    d.tab = currentTab;
                    d.search.value = $('#filter-search').val();
                    d.task_type = $('#filter-task-type').val();
                    d.output_type = $('#filter-output-type').val();
                    d.status = $('#filter-status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-medium text-gray-500 py-3' },
                { data: 'task_number', name: 'task_number', className: 'py-3' },
                { data: 'title', name: 'title', className: 'py-3' },
                { data: 'priority_badge', name: 'priority.name', className: 'py-3' },
                { data: 'due_date', name: 'due_date', className: 'py-3' },
                { data: 'progress', name: 'progress', className: 'py-3' },
                { data: 'status', name: 'status', className: 'py-3' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right py-3' },
            ],
            language: {
                emptyTable: "Belum ada tugas yang diploting kepada Anda pada kategori ini.",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ tugas",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 tugas",
                infoFiltered: "(difilter dari _MAX_ total tugas)",
                lengthMenu: "Tampilkan _MENU_ data",
                loadingRecords: "&nbsp;",
                zeroRecords: "Tidak ada tugas yang sesuai dengan filter."
            },
            drawCallback: function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });

        // Tab click
        $('.my-tasks-tab').on('click', function() {
            setTab($(this).data('tab'));
        });

        // Search trigger
        let searchTimer;
        $('#filter-search').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => {
                table.draw();
            }, 300);
        });

        // Select filters trigger
        $('#filter-task-type, #filter-output-type, #filter-status').on('change', function() {
            table.draw();
        });

        // Reset button
        $('#btn-reset-filters').on('click', function() {
            $('#filter-search').val('');
            $('#filter-task-type').val('');
            $('#filter-output-type').val('');
            $('#filter-status').val('');
            setTab('active');
        });
    });
</script>
@endpush
