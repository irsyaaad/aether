@extends('layouts.app')

@section('title', 'Monitoring Submission Tugas - ' . config('app.name'))
@section('page_title', 'Monitoring Submission Tugas')

@section('toolbar_actions')
<div class="flex items-center gap-2">
    <a href="{{ route('tasks.assignment.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
        <i data-lucide="user-check" class="w-4 h-4 text-primary"></i>
        Plotting Tugas
    </a>
    <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
        <i data-lucide="list" class="w-4 h-4 text-gray-500"></i>
        Daftar Master Tugas
    </a>
</div>
@endsection

@push('styles')
<style>
    #monitoring-table_wrapper { width: 100% !important; }
    #monitoring-table_wrapper .dt-layout-row { width: 100% !important; }
    #monitoring-table_wrapper .dt-layout-cell { padding: 0 !important; }
    #monitoring-table_wrapper .dt-layout-table {
        width: 100% !important;
        overflow-x: auto !important;
        display: block !important;
    }
    #monitoring-table_wrapper table { width: 100% !important; border-collapse: collapse !important; }

    /* Top Controls: Length Menu */
    #monitoring-table_wrapper .dt-layout-row:first-child,
    #monitoring-table_wrapper .dt-layout-row:has(.dt-length) {
        display: flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        justify-content: space-between !important;
        gap: 1rem !important;
        padding: 1.25rem 1.5rem !important;
        border-bottom: 1px solid #e5e7eb !important;
        background-color: #ffffff !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    #monitoring-table_wrapper .dt-length {
        display: flex !important;
        align-items: center !important;
    }
    #monitoring-table_wrapper .dt-length label {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        font-size: 0.8125rem !important;
        color: #4b5563 !important;
        font-weight: 500 !important;
        margin-bottom: 0 !important;
    }
    #monitoring-table_wrapper .dt-length select {
        padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        font-size: 0.8125rem !important;
        border-radius: 0.5rem !important;
        border: 1px solid #d1d5db !important;
        background-color: #ffffff !important;
        color: #374151 !important;
        outline: none !important;
        cursor: pointer !important;
    }
    #monitoring-table_wrapper .dt-length select:focus {
        border-color: var(--color-primary, #e11d48) !important;
        box-shadow: 0 0 0 1px var(--color-primary, #e11d48) !important;
    }

    #monitoring-table_wrapper .dt-search {
        display: none !important;
    }

    /* Bottom Controls: Info & Pagination */
    #monitoring-table_wrapper .dt-layout-table + .dt-layout-row,
    #monitoring-table_wrapper .dt-layout-row:has(.dt-info),
    #monitoring-table_wrapper .dt-layout-row:has(.dt-paging),
    #monitoring-table_wrapper .dt-layout-row:last-child {
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

    #monitoring-table_wrapper .dt-info {
        font-size: 0.875rem !important;
        color: #4b5563 !important;
        font-weight: 500 !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.5 !important;
    }
    #monitoring-table_wrapper .dt-layout-start,
    #monitoring-table_wrapper .dt-start {
        display: flex !important;
        align-items: center !important;
        gap: 1rem !important;
    }
    #monitoring-table_wrapper .dt-layout-end,
    #monitoring-table_wrapper .dt-end {
        margin-left: auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 0.5rem !important;
    }
    #monitoring-table_wrapper .dt-paging {
        display: flex !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    #monitoring-table_wrapper .dt-paging nav {
        display: flex !important;
        gap: 0.375rem !important;
        align-items: center !important;
    }
    #monitoring-table_wrapper .dt-paging button,
    #monitoring-table_wrapper .dt-paging .dt-paging-button {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 2.25rem !important;
        height: 2.25rem !important;
        padding: 0 0.625rem !important;
        font-size: 0.8125rem !important;
        font-weight: 500 !important;
        border-radius: 0.5rem !important;
        border: 1px solid #e5e7eb !important;
        background-color: #ffffff !important;
        color: #374151 !important;
        cursor: pointer !important;
        transition: all 0.15s ease-in-out !important;
    }
    #monitoring-table_wrapper .dt-paging button:hover:not(.current):not(:disabled),
    #monitoring-table_wrapper .dt-paging .dt-paging-button:hover:not(.current):not(.disabled) {
        background-color: #f9fafb !important;
        border-color: #d1d5db !important;
        color: #111827 !important;
    }
    #monitoring-table_wrapper .dt-paging button.current,
    #monitoring-table_wrapper .dt-paging .dt-paging-button.current {
        background-color: var(--color-primary, #e11d48) !important;
        border-color: var(--color-primary, #e11d48) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    #monitoring-table_wrapper .dt-paging button:disabled,
    #monitoring-table_wrapper .dt-paging .dt-paging-button:disabled {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
    }

    #monitoring-table th {
        font-size: 0.75rem !important;
        text-transform: uppercase !important;
        letter-spacing: 0.05em !important;
        font-weight: 600 !important;
        color: #4b5563 !important;
        background-color: #f9fafb !important;
        border-bottom: 1px solid #e5e7eb !important;
        padding: 0.875rem 1.25rem !important;
        white-space: nowrap !important;
    }
    #monitoring-table td {
        padding: 0.875rem 1.25rem !important;
        font-size: 0.875rem !important;
        border-bottom: 1px solid #f3f4f6 !important;
        color: #1f2937 !important;
        vertical-align: middle !important;
    }
    #monitoring-table tbody tr:hover {
        background-color: #f9fafb !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- KPI / Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Total Plotting</p>
                <h3 class="text-xl font-bold text-gray-900 mt-1">{{ $stats['total'] }}</h3>
            </div>
            <div class="p-2.5 bg-blue-50 text-blue-600 rounded-lg">
                <i data-lucide="clipboard-list" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Belum Dikerjakan</p>
                <h3 class="text-xl font-bold text-sky-600 mt-1">{{ $stats['assigned'] }}</h3>
            </div>
            <div class="p-2.5 bg-sky-50 text-sky-600 rounded-lg">
                <i data-lucide="inbox" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Sedang Berjalan</p>
                <h3 class="text-xl font-bold text-amber-600 mt-1">{{ $stats['in_progress'] }}</h3>
            </div>
            <div class="p-2.5 bg-amber-50 text-amber-600 rounded-lg">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Selesai Disubmit</p>
                <h3 class="text-xl font-bold text-emerald-600 mt-1">{{ $stats['completed'] }}</h3>
            </div>
            <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-lg">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider text-gray-500">Terlambat</p>
                <h3 class="text-xl font-bold text-rose-600 mt-1">{{ $stats['overdue'] }}</h3>
            </div>
            <div class="p-2.5 bg-rose-50 text-rose-600 rounded-lg">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-4">
        <!-- Row 1: Date Range Filter -->
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3 pb-3 border-b border-gray-100">
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                    <i data-lucide="calendar-range" class="w-4 h-4 text-primary"></i>
                    Range Tanggal Penugasan:
                </span>
                <div class="flex items-center gap-1.5">
                    <input type="date" id="filter-start-date" value="{{ $defaultStartDate }}" class="py-1.5 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary font-mono">
                    <span class="text-xs text-gray-400">s/d</span>
                    <input type="date" id="filter-end-date" value="{{ $defaultEndDate }}" class="py-1.5 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary font-mono">
                </div>
                <button type="button" onclick="applyFilters()" class="px-3 py-1.5 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm">
                    Terapkan
                </button>
            </div>

            <!-- Quick Preset Buttons -->
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-[11px] text-gray-400 font-medium mr-1">Preset:</span>
                <button type="button" onclick="setDatePreset('today')" class="px-2.5 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    Hari Ini
                </button>
                <button type="button" onclick="setDatePreset('this_week')" class="px-2.5 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    Minggu Ini
                </button>
                <button type="button" onclick="setDatePreset('this_month')" class="px-2.5 py-1 text-xs font-semibold bg-primary/10 hover:bg-primary/20 text-primary rounded-lg transition-colors">
                    Bulan Ini
                </button>
                <button type="button" onclick="setDatePreset('last_month')" class="px-2.5 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    Bulan Lalu
                </button>
                <button type="button" onclick="setDatePreset('all')" class="px-2.5 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    Semua
                </button>
            </div>
        </div>

        <!-- Row 2: Search, Status, Output Type, Dept & Company -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search Input -->
            <div class="lg:col-span-2">
                <div class="relative">
                    <input type="text" id="filter-search" placeholder="Cari judul tugas, nama pegawai, NIK..." class="w-full pl-8 pr-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5"></i>
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <select id="filter-status" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $stKey => $stLabel)
                        <option value="{{ $stKey }}">{{ $stLabel }}</option>
                    @endforeach
                    <option value="overdue">Terlambat (Overdue)</option>
                </select>
            </div>

            <!-- Output Type Filter -->
            <div>
                <select id="filter-output-type" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Bentuk Hasil</option>
                    @foreach($outputTypes as $otKey => $otLabel)
                        <option value="{{ $otKey }}">{{ $otLabel }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Department Filter & Reset Button -->
            <div class="flex items-center gap-2">
                <select id="filter-department" class="flex-1 py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>

                <!-- Reset Button -->
                <button type="button" onclick="resetFilters()" class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors shrink-0" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table id="monitoring-table" class="w-full text-left">
            <thead>
                <tr>
                    <th class="w-12 text-center">#</th>
                    <th class="min-w-[180px]">Tugas</th>
                    <th class="min-w-[180px]">Pegawai</th>
                    <th>Tenggat Waktu</th>
                    <th class="text-center">Status</th>
                    <th class="w-28">Progress</th>
                    <th class="min-w-[160px]">Bukti & Submitan</th>
                    <th class="text-right w-28">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<!-- Modal Detail Bukti & Hasil Submission -->
<div id="modal-submission-detail" class="fixed inset-0 z-50 hidden bg-gray-900/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl border border-gray-200 overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/90 shrink-0">
            <div class="flex items-center gap-3">
                <div id="modal-avatar" class="w-10 h-10 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center border border-primary/20 shrink-0">
                    --
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-gray-900" id="modal-emp-name">Nama Pegawai</h3>
                        <span id="modal-status-badge"></span>
                    </div>
                    <p class="text-xs text-gray-500" id="modal-emp-subtitle">NIK • Departemen</p>
                </div>
            </div>
            <button type="button" onclick="closeSubmissionModal()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-5 space-y-4 overflow-y-auto flex-1" id="modal-content">
            <div class="py-12 text-center text-gray-400 text-xs">
                <i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto mb-2 text-primary"></i>
                Memuat rincian bukti submitan...
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between shrink-0">
            <a href="#" id="modal-task-link" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-semibold text-primary hover:underline">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                Buka Halaman Master Tugas
            </a>
            <button type="button" onclick="closeSubmissionModal()" class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let monitoringTable;

    $(document).ready(function() {
        monitoringTable = $('#monitoring-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('tasks.monitoring.get-data') }}",
                data: function(d) {
                    d.search.value = $('#filter-search').val();
                    d.start_date = $('#filter-start-date').val();
                    d.end_date = $('#filter-end-date').val();
                    d.status = $('#filter-status').val();
                    d.output_type = $('#filter-output-type').val();
                    d.department_id = $('#filter-department').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-medium text-gray-500' },
                { data: 'task_info', name: 'task.title', orderable: false },
                { data: 'employee_info', name: 'employee.name', orderable: false },
                { data: 'due_date', name: 'due_date' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'progress', name: 'progress' },
                { data: 'submission_preview', name: 'submission_preview', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ],
            order: [],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                emptyTable: "Belum ada plotting tugas yang terdata pada periode ini",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ plotting tugas",
                infoEmpty: "Menampilkan 0 data",
                lengthMenu: "Tampilkan _MENU_ data",
                loadingRecords: "&nbsp;",
                zeroRecords: "Tidak ada data yang sesuai filter"
            },
            drawCallback: function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });

        // Filter events with debounce on search
        let searchTimer;
        $('#filter-search').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                monitoringTable.draw();
            }, 300);
        });

        $('#filter-status, #filter-output-type, #filter-department').on('change', function() {
            monitoringTable.draw();
        });
    });

    function applyFilters() {
        monitoringTable.draw();
    }

    function setDatePreset(preset) {
        const today = new Date();
        let startDate = '';
        let endDate = '';

        if (preset === 'today') {
            startDate = formatDateISO(today);
            endDate = formatDateISO(today);
        } else if (preset === 'this_week') {
            const firstDay = new Date(today);
            const day = today.getDay() || 7;
            firstDay.setDate(today.getDate() - day + 1);
            const lastDay = new Date(firstDay);
            lastDay.setDate(firstDay.getDate() + 6);
            startDate = formatDateISO(firstDay);
            endDate = formatDateISO(lastDay);
        } else if (preset === 'this_month') {
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            startDate = formatDateISO(firstDay);
            endDate = formatDateISO(lastDay);
        } else if (preset === 'last_month') {
            const firstDay = new Date(today.getFullYear(), today.getMonth() - 1, 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth(), 0);
            startDate = formatDateISO(firstDay);
            endDate = formatDateISO(lastDay);
        } else if (preset === 'all') {
            startDate = '';
            endDate = '';
        }

        $('#filter-start-date').val(startDate);
        $('#filter-end-date').val(endDate);
        monitoringTable.draw();
    }

    function resetFilters() {
        $('#filter-search').val('');
        $('#filter-status').val('');
        $('#filter-output-type').val('');
        $('#filter-department').val('');
        setDatePreset('this_month');
    }

    function formatDateISO(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function showSubmissionModal(assigneeId) {
        $('#modal-content').html(`
            <div class="py-12 text-center text-gray-400 text-xs">
                <i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto mb-2 text-primary"></i>
                Memuat rincian bukti submitan...
            </div>
        `);
        $('#modal-submission-detail').removeClass('hidden');

        $.ajax({
            url: `/task-monitoring/${assigneeId}/detail`,
            type: 'GET',
            success: function(res) {
                if (res.status === 'success') {
                    const data = res.data;
                    const emp = data.employee;
                    const sub = data.submission;

                    $('#modal-emp-name').text(emp.name);
                    $('#modal-emp-subtitle').text(`${emp.nik} • ${emp.position} (${emp.department})`);
                    $('#modal-status-badge').html(`
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold border ${data.status_badge}">
                            ${data.status_label}
                        </span>
                    `);

                    if (emp.avatar_url) {
                        $('#modal-avatar').html(`<img src="${emp.avatar_url}" alt="${emp.name}" class="w-10 h-10 rounded-full object-cover">`);
                    } else {
                        const initials = (emp.name || 'EM').substring(0, 2).toUpperCase();
                        $('#modal-avatar').html(initials);
                    }

                    $('#modal-task-link').attr('href', data.task_url);

                    // Build modal content
                    let html = `
                        <!-- Task Overview Box -->
                        <div class="p-3.5 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <span class="text-xs font-bold text-gray-900">${data.task_title}</span>
                                <div class="flex items-center gap-1.5">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold border ${data.priority_badge}">
                                        ${data.priority}
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-medium bg-white text-gray-700 border border-gray-200">
                                        ${data.output_type_label}
                                    </span>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 text-[11px] text-gray-500 flex-wrap">
                                <span>Kategori: <strong class="text-gray-700">${data.category}</strong></span>
                                <span>•</span>
                                <span>Mulai: <strong class="text-gray-700">${data.start_date}</strong></span>
                                <span>•</span>
                                <span>Deadline: <strong class="${data.is_overdue ? 'text-red-600 font-bold' : 'text-gray-700'}">${data.due_date}</strong></span>
                                ${data.is_overdue ? '<span class="text-[10px] text-red-600 font-bold bg-red-100 px-1.5 py-0.5 rounded">Terlambat</span>' : ''}
                            </div>
                        </div>

                        <!-- Progress Bar Box -->
                        <div class="space-y-1.5 bg-white p-3.5 rounded-xl border border-gray-200">
                            <div class="flex items-center justify-between text-xs font-semibold text-gray-700">
                                <span>Progress Penyelesaian Pegawai</span>
                                <span class="text-primary font-bold font-mono">${data.progress}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="${data.progress == 100 ? 'bg-emerald-500' : 'bg-primary'} h-2 rounded-full transition-all" style="width: ${data.progress}%"></div>
                            </div>
                        </div>
                    `;

                    // Output Type Specific Display
                    if (data.output_type === 'target' && sub) {
                        html += `
                            <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-200">
                                <span class="text-xs font-semibold text-amber-800 uppercase tracking-wider block mb-1">Capaian Realisasi Target</span>
                                <span class="text-lg font-bold font-mono text-amber-900">${sub.achieved_formatted}</span>
                            </div>
                        `;
                    }

                    if (data.output_type === 'stock_update' && sub && sub.stock_updates && sub.stock_updates.length > 0) {
                        html += `
                            <div class="space-y-2">
                                <h4 class="text-xs font-bold text-gray-800 flex items-center gap-1.5">
                                    <i data-lucide="package-x" class="w-4 h-4 text-rose-600"></i>
                                    Produk Kosong yang Dilaporkan (${sub.stock_updates.length})
                                </h4>
                                <div class="overflow-x-auto border border-gray-200 rounded-lg">
                                    <table class="w-full text-[11px] text-left">
                                        <thead class="bg-gray-50 text-gray-600 font-semibold border-b">
                                            <tr>
                                                <th class="p-2">Toko</th>
                                                <th class="p-2">Produk</th>
                                                <th class="p-2 text-center">Status</th>
                                                <th class="p-2 text-center">Sisa Stok</th>
                                                <th class="p-2">Catatan</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                        `;
                        sub.stock_updates.forEach(function(item) {
                            html += `
                                <tr>
                                    <td class="p-2 font-medium">${item.toko}</td>
                                    <td class="p-2">${item.produk}</td>
                                    <td class="p-2 text-center">
                                        <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold border ${item.badge_class}">
                                            ${item.status_label}
                                        </span>
                                    </td>
                                    <td class="p-2 text-center font-mono font-bold">${item.stock}</td>
                                    <td class="p-2 text-gray-500">${item.notes}</td>
                                </tr>
                            `;
                        });
                        html += `
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        `;
                    if (data.output_type === 'full_custom') {
                        const customFields = data.custom_fields || [];
                        let rawData = (sub && sub.custom_data) ? sub.custom_data : [];
                        let rows = [];
                        if (Array.isArray(rawData)) {
                            rows = rawData;
                        } else if (typeof rawData === 'object' && rawData !== null) {
                            rows = [rawData];
                        }

                        html += `
                            <div class="space-y-3">
                                <h4 class="text-xs font-bold text-gray-800 flex items-center justify-between gap-1.5">
                                    <span class="flex items-center gap-1.5">
                                        <i data-lucide="sliders-horizontal" class="w-4 h-4 text-purple-600"></i>
                                        Data Form Full Custom (${customFields.length} Field)
                                    </span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-purple-100 text-purple-800">
                                        ${rows.length} Baris Data
                                    </span>
                                </h4>
                                <div class="space-y-3">
                        `;
                        if (rows.length > 0) {
                            rows.forEach(function(rowVal, rIdx) {
                                html += `
                                    <div class="bg-purple-50/60 p-3.5 rounded-xl border border-purple-200 space-y-2.5">
                                        <div class="flex items-center justify-between border-b border-purple-200/60 pb-1.5">
                                            <span class="text-xs font-bold text-purple-900 flex items-center gap-1.5">
                                                <span class="w-4 h-4 rounded-full bg-purple-600 text-white text-[10px] font-bold flex items-center justify-center">
                                                    ${rIdx + 1}
                                                </span>
                                                Baris ke-${rIdx + 1}
                                            </span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                `;
                                customFields.forEach(function(cf) {
                                    const val = rowVal ? rowVal[cf.key] : undefined;
                                    let valHtml = '<span class="text-gray-400 italic">Belum diisi</span>';
                                    if (val !== undefined && val !== null && val !== '') {
                                        if (cf.type === 'file' && typeof val === 'object' && val.url) {
                                            valHtml = `<a href="${val.url}" target="_blank" class="text-primary hover:underline font-semibold flex items-center gap-1"><i data-lucide="download" class="w-3.5 h-3.5"></i> ${val.original_name || 'Buka File'}</a>`;
                                        } else if (cf.type === 'textarea') {
                                            valHtml = `<div class="text-xs text-gray-700 bg-white p-2 rounded border border-purple-100 whitespace-pre-wrap">${val}</div>`;
                                        } else if (cf.type === 'number') {
                                            const num = Number(val);
                                            valHtml = `<span class="font-bold font-mono text-gray-800">${!isNaN(num) ? num.toLocaleString('id-ID') : escapeHtml(String(val))}</span>`;
                                        } else if (cf.type === 'url') {
                                            const rawUrl = String(val);
                                            const href = (rawUrl.startsWith('http://') || rawUrl.startsWith('https://')) ? rawUrl : 'https://' + rawUrl;
                                            valHtml = `<a href="${encodeURI(href)}" target="_blank" class="text-primary hover:underline font-semibold inline-flex items-center gap-1 break-all">${escapeHtml(rawUrl)} <i data-lucide="external-link" class="w-3 h-3 shrink-0"></i></a>`;
                                        } else if (cf.type === 'email') {
                                            const rawEmail = String(val);
                                            valHtml = `<a href="mailto:${encodeURI(rawEmail)}" class="text-primary hover:underline font-semibold inline-flex items-center gap-1 break-all"><i data-lucide="mail" class="w-3 h-3 shrink-0"></i> ${escapeHtml(rawEmail)}</a>`;
                                        } else if (cf.type === 'date') {
                                            valHtml = `<span class="inline-flex items-center gap-1 font-medium text-gray-800"><i data-lucide="calendar" class="w-3.5 h-3.5 text-gray-400"></i> ${escapeHtml(String(val))}</span>`;
                                        } else if (cf.type === 'time') {
                                            valHtml = `<span class="inline-flex items-center gap-1 font-medium text-gray-800 font-mono"><i data-lucide="clock" class="w-3.5 h-3.5 text-gray-400"></i> ${escapeHtml(String(val))}</span>`;
                                        } else if (cf.type === 'datetime') {
                                            valHtml = `<span class="inline-flex items-center gap-1 font-medium text-gray-800 font-mono"><i data-lucide="calendar-clock" class="w-3.5 h-3.5 text-gray-400"></i> ${escapeHtml(String(val).replace('T', ' '))}</span>`;
                                        } else {
                                            valHtml = `<span class="font-medium text-gray-800">${escapeHtml(String(val))}</span>`;
                                        }
                                    }
                                    html += `
                                        <div class="bg-white p-2.5 rounded-lg border border-purple-100 space-y-0.5 ${cf.type === 'textarea' ? 'sm:col-span-2' : ''}">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[10px] font-bold text-purple-900">${cf.label}</span>
                                                <span class="text-[9px] text-gray-400 font-mono">${cf.type}</span>
                                            </div>
                                            <div class="text-xs pt-0.5">${valHtml}</div>
                                        </div>
                                    `;
                                });
                                html += `
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            html += `<p class="text-xs text-gray-400 italic p-3 bg-purple-50 rounded-lg">Belum ada data yang diisi.</p>`;
                        }
                        html += `
                                </div>
                            </div>
                        `;
                    }

                    // Proof of Work: Link, Photo, Notes
                    html += `<div class="space-y-3 bg-white p-4 rounded-xl border border-gray-200">
                        <h4 class="text-xs font-bold text-gray-800 flex items-center gap-1.5 pb-2 border-b border-gray-100">
                            <i data-lucide="paperclip" class="w-4 h-4 text-primary"></i>
                            Bukti & Keterangan Penyelesaian
                        </h4>
                    `;

                    if (sub && (sub.completion_file_url || sub.completion_url || sub.completion_notes)) {
                        // URL Link
                        if (sub.completion_url) {
                            html += `
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Tautan Link Bukti</label>
                                    <a href="${sub.completion_url}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-sky-700 bg-sky-50 border border-sky-200 rounded-lg hover:bg-sky-100 transition-colors w-full break-all">
                                        <i data-lucide="external-link" class="w-4 h-4 shrink-0"></i>
                                        <span>${sub.completion_url}</span>
                                    </a>
                                </div>
                            `;
                        }

                        // Photo Proof
                        if (sub.completion_file_url) {
                            html += `
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Foto Bukti Pengerjaan</label>
                                    <div class="relative group rounded-xl overflow-hidden border border-gray-200 bg-slate-100 max-h-72 flex items-center justify-center">
                                        <img src="${sub.completion_file_url}" alt="Foto Bukti" class="max-h-72 object-contain w-full">
                                        <a href="${sub.completion_file_url}" target="_blank" class="absolute bottom-2 right-2 px-3 py-1.5 text-xs font-semibold text-white bg-black/70 hover:bg-black rounded-lg transition-colors flex items-center gap-1.5 backdrop-blur-xs">
                                            <i data-lucide="maximize-2" class="w-3.5 h-3.5"></i>
                                            Buka Ukuran Penuh
                                        </a>
                                    </div>
                                </div>
                            `;
                        }

                        // Notes
                        if (sub.completion_notes) {
                            html += `
                                <div>
                                    <label class="block text-[11px] font-bold text-gray-500 mb-1">Catatan dari Pegawai</label>
                                    <div class="p-3 bg-gray-50 rounded-lg border border-gray-200 text-xs text-gray-700 whitespace-pre-wrap leading-relaxed">
                                        "${sub.completion_notes}"
                                    </div>
                                </div>
                            `;
                        }

                        if (sub.completed_at) {
                            html += `
                                <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-500">
                                    <span>Waktu Selesai: <strong class="font-mono text-gray-800">${sub.completed_at}</strong></span>
                                </div>
                            `;
                        }
                    } else {
                        html += `
                            <div class="py-6 text-center text-xs text-gray-400 italic">
                                Belum ada bukti pengerjaan (foto, tautan, atau catatan) yang diunggah oleh pegawai ini.
                            </div>
                        `;
                    }

                    html += `</div>`;

                    $('#modal-content').html(html);

                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            },
            error: function() {
                $('#modal-content').html(`
                    <div class="py-8 text-center text-red-500 text-xs">
                        Gagal memuat rincian bukti submitan. Silakan coba kembali.
                    </div>
                `);
            }
        });
    }

    function closeSubmissionModal() {
        $('#modal-submission-detail').addClass('hidden');
    }
</script>
@endpush
