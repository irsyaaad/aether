@extends('layouts.app')

@section('title', 'Plotting Tugas Karyawan - ' . config('app.name'))
@section('page_title', 'Plotting Tugas Karyawan')

@section('toolbar_actions')
<div class="flex items-center gap-2">
    <a href="{{ route('tasks.assignment.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm">
        <i data-lucide="user-plus" class="w-4 h-4"></i>
        Plot Tugas ke Karyawan
    </a>
    <a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
        <i data-lucide="list" class="w-4 h-4 text-gray-500"></i>
        Daftar Master Tugas
    </a>
</div>
@endsection

@push('styles')
<style>
    #assignment-table_wrapper, #employee-recap-table_wrapper { width: 100% !important; }
    #assignment-table_wrapper .dt-layout-row, #employee-recap-table_wrapper .dt-layout-row { width: 100% !important; }
    #assignment-table_wrapper .dt-layout-cell, #employee-recap-table_wrapper .dt-layout-cell { padding: 0 !important; }
    #assignment-table_wrapper .dt-layout-table, #employee-recap-table_wrapper .dt-layout-table {
        width: 100% !important;
        overflow-x: auto !important;
        display: block !important;
    }
    #assignment-table_wrapper table, #employee-recap-table_wrapper table { width: 100% !important; border-collapse: collapse !important; }

    /* Top Controls: Length Menu */
    #assignment-table_wrapper .dt-layout-row:first-child,
    #assignment-table_wrapper .dt-layout-row:has(.dt-length),
    #employee-recap-table_wrapper .dt-layout-row:first-child,
    #employee-recap-table_wrapper .dt-layout-row:has(.dt-length) {
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

    #assignment-table_wrapper .dt-length,
    #employee-recap-table_wrapper .dt-length {
        display: flex !important;
        align-items: center !important;
    }
    #assignment-table_wrapper .dt-length label,
    #employee-recap-table_wrapper .dt-length label {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        font-size: 0.8125rem !important;
        color: #4b5563 !important;
        font-weight: 500 !important;
        margin-bottom: 0 !important;
    }
    #assignment-table_wrapper .dt-length select,
    #employee-recap-table_wrapper .dt-length select {
        padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        font-size: 0.8125rem !important;
        border-radius: 0.5rem !important;
        border: 1px solid #d1d5db !important;
        background-color: #ffffff !important;
        color: #374151 !important;
        outline: none !important;
        cursor: pointer !important;
    }
    #assignment-table_wrapper .dt-length select:focus,
    #employee-recap-table_wrapper .dt-length select:focus {
        border-color: var(--color-primary, #e11d48) !important;
        box-shadow: 0 0 0 1px var(--color-primary, #e11d48) !important;
    }

    #assignment-table_wrapper .dt-search,
    #employee-recap-table_wrapper .dt-search {
        display: none !important;
    }

    /* Bottom Controls: Info & Pagination */
    #assignment-table_wrapper .dt-layout-table + .dt-layout-row,
    #assignment-table_wrapper .dt-layout-row:has(.dt-info),
    #assignment-table_wrapper .dt-layout-row:has(.dt-paging),
    #assignment-table_wrapper .dt-layout-row:last-child,
    #employee-recap-table_wrapper .dt-layout-table + .dt-layout-row,
    #employee-recap-table_wrapper .dt-layout-row:has(.dt-info),
    #employee-recap-table_wrapper .dt-layout-row:has(.dt-paging),
    #employee-recap-table_wrapper .dt-layout-row:last-child {
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

    #assignment-table_wrapper .dt-info,
    #employee-recap-table_wrapper .dt-info {
        font-size: 0.875rem !important;
        color: #4b5563 !important;
        font-weight: 500 !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.5 !important;
    }
    #assignment-table_wrapper .dt-layout-start,
    #assignment-table_wrapper .dt-start,
    #employee-recap-table_wrapper .dt-layout-start,
    #employee-recap-table_wrapper .dt-start {
        display: flex !important;
        align-items: center !important;
        gap: 1rem !important;
    }
    #assignment-table_wrapper .dt-layout-end,
    #assignment-table_wrapper .dt-end,
    #employee-recap-table_wrapper .dt-layout-end,
    #employee-recap-table_wrapper .dt-end {
        margin-left: auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 0.5rem !important;
    }
    #assignment-table_wrapper .dt-paging,
    #employee-recap-table_wrapper .dt-paging {
        display: flex !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    #assignment-table_wrapper .dt-paging nav,
    #employee-recap-table_wrapper .dt-paging nav {
        display: flex !important;
        gap: 0.375rem !important;
        align-items: center !important;
    }
    #assignment-table_wrapper .dt-paging button,
    #assignment-table_wrapper .dt-paging .dt-paging-button,
    #employee-recap-table_wrapper .dt-paging button,
    #employee-recap-table_wrapper .dt-paging .dt-paging-button {
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
    #assignment-table_wrapper .dt-paging button:hover:not(.current):not(:disabled),
    #assignment-table_wrapper .dt-paging .dt-paging-button:hover:not(.current):not(.disabled),
    #employee-recap-table_wrapper .dt-paging button:hover:not(.current):not(:disabled),
    #employee-recap-table_wrapper .dt-paging .dt-paging-button:hover:not(.current):not(.disabled) {
        background-color: #f9fafb !important;
        border-color: #d1d5db !important;
        color: #111827 !important;
    }
    #assignment-table_wrapper .dt-paging button.current,
    #assignment-table_wrapper .dt-paging .dt-paging-button.current,
    #employee-recap-table_wrapper .dt-paging button.current,
    #employee-recap-table_wrapper .dt-paging .dt-paging-button.current {
        background-color: var(--color-primary, #e11d48) !important;
        border-color: var(--color-primary, #e11d48) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    #assignment-table_wrapper .dt-paging button:disabled,
    #assignment-table_wrapper .dt-paging .dt-paging-button:disabled,
    #employee-recap-table_wrapper .dt-paging button:disabled,
    #employee-recap-table_wrapper .dt-paging .dt-paging-button:disabled {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
    }

    #assignment-table th, #employee-recap-table th {
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
    #assignment-table td, #employee-recap-table td {
        padding: 0.875rem 1.25rem !important;
        font-size: 0.875rem !important;
        border-bottom: 1px solid #f3f4f6 !important;
        color: #1f2937 !important;
        vertical-align: middle !important;
    }
    #assignment-table tbody tr:hover, #employee-recap-table tbody tr:hover {
        background-color: #f9fafb !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- View Switcher Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200">
        <button type="button" onclick="switchAssignmentView('assignments')" id="tab-assignments" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-bold border-b-2 border-primary text-primary bg-primary/5 rounded-t-lg transition-all">
            <i data-lucide="clipboard-check" class="w-4 h-4"></i>
            Daftar Penugasan Tugas
        </button>
        <button type="button" onclick="switchAssignmentView('employees')" id="tab-employees" class="inline-flex items-center gap-2 px-4 py-2.5 text-xs font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 rounded-t-lg transition-all">
            <i data-lucide="users" class="w-4 h-4"></i>
            Rekap per Karyawan
        </button>
    </div>

    <!-- Filter Bar: Date Range, Search, Status, Company, Department -->
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
                <button type="button" onclick="applyDateFilter()" class="px-3 py-1.5 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm">
                    Terapkan
                </button>
            </div>

            <!-- Quick Preset Buttons -->
            <div class="flex flex-wrap items-center gap-1.5">
                <span class="text-[11px] text-gray-400 font-medium mr-1">Preset:</span>
                <button type="button" onclick="setDateRangePreset('today')" id="btn-preset-today" class="preset-btn px-2.5 py-1 text-xs font-semibold bg-primary/10 hover:bg-primary/20 text-primary rounded-lg transition-colors">
                    Hari Ini
                </button>
                <button type="button" onclick="setDateRangePreset('this_week')" id="btn-preset-this_week" class="preset-btn px-2.5 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    Minggu Ini
                </button>
                <button type="button" onclick="setDateRangePreset('this_month')" id="btn-preset-this_month" class="preset-btn px-2.5 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    Bulan Ini
                </button>
                <button type="button" onclick="setDateRangePreset('last_month')" id="btn-preset-last_month" class="preset-btn px-2.5 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    Bulan Lalu
                </button>
                <button type="button" onclick="setDateRangePreset('all')" id="btn-preset-all" class="preset-btn px-2.5 py-1 text-xs font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-colors">
                    Semua
                </button>
            </div>
        </div>

        <!-- Row 2: Search, Status, Company & Department Filter -->
        <div class="flex flex-wrap items-center gap-3">
            <!-- Search Input -->
            <div class="flex-1 min-w-[220px]">
                <div class="relative">
                    <input type="text" id="filter-search" placeholder="Cari judul tugas, karyawan, NIK..." class="w-full pl-8 pr-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5"></i>
                </div>
            </div>

            <!-- Status Filter (for Assignments Grid) -->
            <div id="container-filter-status" class="w-full sm:w-48">
                <select id="filter-status" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Status</option>
                    @if(isset($statuses))
                        @foreach($statuses as $stKey => $stLabel)
                            <option value="{{ $stKey }}">{{ $stLabel }}</option>
                        @endforeach
                    @endif
                    <option value="overdue">Terlambat (Overdue)</option>
                </select>
            </div>

            <!-- Company Filter (for Super Admin) -->
            @if(empty($userCompanyId))
            <div class="w-full sm:w-52">
                <select id="filter-company" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Perusahaan</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            <!-- Department Filter -->
            <div class="w-full sm:w-48">
                <select id="filter-department" class="w-full py-2 px-3 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Departemen</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Reset Button -->
            <button type="button" onclick="resetFilters()" class="px-3 py-2 text-xs font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors" title="Reset Filter">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 inline"></i>
                Reset
            </button>
        </div>
    </div>

    <!-- Panel 1: Primary Task Assignment Grid (with Tenggat Waktu, Progress, Status) -->
    <div id="panel-assignments" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table id="assignment-table" class="w-full text-left">
            <thead>
                <tr>
                    <th class="w-12 text-center">#</th>
                    <th>Tugas</th>
                    <th>Karyawan</th>
                    <th>Tenggat Waktu</th>
                    <th class="w-32">Progress</th>
                    <th class="text-center">Status</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <!-- Panel 2: Employee Summary / Recap Grid -->
    <div id="panel-employees" class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hidden">
        <table id="employee-recap-table" class="w-full text-left">
            <thead>
                <tr>
                    <th class="w-12 text-center">#</th>
                    <th>Karyawan</th>
                    <th>Jabatan & Departemen</th>
                    <th class="min-w-[180px]">Jumlah Tugas Diberikan</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>

<!-- Modal Detail Tugas Karyawan -->
<div id="modal-employee-detail" class="fixed inset-0 z-50 hidden bg-gray-900/60 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl border border-gray-200 overflow-hidden flex flex-col max-h-[85vh]">
        <!-- Modal Header -->
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/80 shrink-0">
            <div class="flex items-center gap-3">
                <div id="modal-emp-avatar" class="w-10 h-10 rounded-full bg-primary/10 text-primary font-bold flex items-center justify-center border border-primary/20 shrink-0">
                    --
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900" id="modal-emp-name">Nama Karyawan</h3>
                    <p class="text-xs text-gray-500" id="modal-emp-subtitle">NIK • Departemen</p>
                </div>
            </div>
            <button type="button" onclick="closeDetailModal()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <!-- Modal Body: Tasks List -->
        <div class="p-5 space-y-3 overflow-y-auto flex-1" id="modal-detail-content">
            <div class="py-10 text-center text-gray-400 text-xs">
                <i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto mb-2 text-primary"></i>
                Memuat rincian tugas...
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between shrink-0">
            <span class="text-xs text-gray-500 font-medium" id="modal-emp-task-count">0 Tugas tercatat</span>
            <div class="flex items-center gap-2">
                <a href="#" id="modal-btn-edit-plot" class="px-3 py-1.5 text-xs font-semibold text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors">
                    <i data-lucide="clipboard-edit" class="w-3.5 h-3.5 inline mr-1"></i> Edit Plotting
                </a>
                <button type="button" onclick="closeDetailModal()" class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let assignmentTable = null;
    let employeeTable = null;
    let currentView = 'assignments';

    $(document).ready(function() {
        // Initialize Primary Assignment Table
        assignmentTable = $('#assignment-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('tasks.assignment.get-data') }}",
                data: function(d) {
                    d.view = 'assignments';
                    d.search.value = $('#filter-search').val();
                    d.start_date = $('#filter-start-date').val();
                    d.end_date = $('#filter-end-date').val();
                    d.company_id = $('#filter-company').val();
                    d.department_id = $('#filter-department').val();
                    d.status = $('#filter-status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center font-medium text-gray-500' },
                { data: 'task_info', name: 'task.title', orderable: false },
                { data: 'employee_info', name: 'employee.name', orderable: false },
                { data: 'due_date', name: 'due_date' },
                { data: 'progress', name: 'progress' },
                { data: 'status', name: 'status', className: 'text-center' },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ],
            order: [],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                emptyTable: "Belum ada penugasan tugas pada periode ini",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ penugasan tugas",
                infoEmpty: "Menampilkan 0 penugasan tugas",
                lengthMenu: "Tampilkan _MENU_ data",
                loadingRecords: "&nbsp;",
                zeroRecords: "Tidak ada penugasan tugas yang sesuai dengan filter"
            },
            drawCallback: function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });

        // Filter event listeners with debounce for search
        let searchTimer;
        $('#filter-search').on('keyup', function() {
            clearTimeout(searchTimer);
            searchTimer = setTimeout(function() {
                refreshActiveTable();
            }, 300);
        });

        $('#filter-status, #filter-company, #filter-department').on('change', function() {
            refreshActiveTable();
        });
    });

    function initEmployeeTable() {
        employeeTable = $('#employee-recap-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('tasks.assignment.get-data') }}",
                data: function(d) {
                    d.view = 'employees';
                    d.search.value = $('#filter-search').val();
                    d.start_date = $('#filter-start-date').val();
                    d.end_date = $('#filter-end-date').val();
                    d.company_id = $('#filter-company').val();
                    d.department_id = $('#filter-department').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'employee_info', name: 'name' },
                { data: 'position_department', name: 'position.name', orderable: false },
                { data: 'tasks_summary', name: 'total_tasks_count', searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ],
            pageLength: 10,
            lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
            language: {
                emptyTable: "Belum ada karyawan yang diberi tugas pada periode ini",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ karyawan bertugas",
                infoEmpty: "Menampilkan 0 karyawan",
                lengthMenu: "Tampilkan _MENU_ data",
                loadingRecords: "&nbsp;",
                zeroRecords: "Tidak ada karyawan yang memiliki tugas pada filter yang dipilih"
            },
            drawCallback: function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });
    }

    function switchAssignmentView(view) {
        currentView = view;
        if (view === 'assignments') {
            $('#tab-assignments').addClass('border-primary text-primary bg-primary/5 font-bold').removeClass('border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium');
            $('#tab-employees').removeClass('border-primary text-primary bg-primary/5 font-bold').addClass('border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium');

            $('#panel-assignments').removeClass('hidden');
            $('#panel-employees').addClass('hidden');
            $('#container-filter-status').removeClass('hidden');

            if (assignmentTable) {
                assignmentTable.columns.adjust().draw(false);
            }
        } else {
            $('#tab-employees').addClass('border-primary text-primary bg-primary/5 font-bold').removeClass('border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium');
            $('#tab-assignments').removeClass('border-primary text-primary bg-primary/5 font-bold').addClass('border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium');

            $('#panel-employees').removeClass('hidden');
            $('#panel-assignments').addClass('hidden');
            $('#container-filter-status').addClass('hidden');

            if (!employeeTable) {
                initEmployeeTable();
            } else {
                employeeTable.columns.adjust().draw(false);
            }
        }

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    function refreshActiveTable() {
        if (currentView === 'assignments' && assignmentTable) {
            assignmentTable.draw();
        } else if (currentView === 'employees' && employeeTable) {
            employeeTable.draw();
        }
    }

    function applyDateFilter() {
        refreshActiveTable();
    }

    function setDateRangePreset(preset) {
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

        // Update active preset button highlight
        $('.preset-btn').removeClass('bg-primary/10 text-primary font-semibold').addClass('bg-gray-100 text-gray-700 font-medium');
        if ($(`#btn-preset-${preset}`).length) {
            $(`#btn-preset-${preset}`).removeClass('bg-gray-100 text-gray-700 font-medium').addClass('bg-primary/10 text-primary font-semibold');
        }

        refreshActiveTable();
    }

    function resetFilters() {
        $('#filter-search').val('');
        $('#filter-status').val('');
        $('#filter-company').val('');
        $('#filter-department').val('');
        setDateRangePreset('today');
    }

    function formatDateISO(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    // Delete single task assignment
    function deleteSingleAssignment(assignmentId, employeeName, taskTitle) {
        Swal.fire({
            title: 'Hapus Penugasan Tugas?',
            html: `Apakah Anda yakin ingin menghapus penugasan tugas <strong>"${taskTitle}"</strong> untuk <strong>${employeeName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Penugasan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/task-assignment/${assignmentId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        type: 'single',
                        assignment_id: assignmentId
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Dihapus',
                            text: response.message || 'Penugasan tugas berhasil dihapus.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        if (assignmentTable) assignmentTable.draw(false);
                        if (employeeTable) employeeTable.draw(false);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Gagal menghapus penugasan tugas.'
                        });
                    }
                });
            }
        });
    }

    // Show modal detail tugas
    function showEmployeeTaskDetail(employeeId, employeeName) {
        $('#modal-emp-name').text(employeeName);
        $('#modal-emp-subtitle').text('Memuat info...');
        $('#modal-detail-content').html(`
            <div class="py-12 text-center text-gray-400 text-xs">
                <i data-lucide="loader-2" class="w-6 h-6 animate-spin mx-auto mb-2 text-primary"></i>
                Memuat rincian tugas...
            </div>
        `);
        $('#modal-employee-detail').removeClass('hidden');

        // Set edit plot button URL
        $('#modal-btn-edit-plot').attr('href', `/task-assignment/${employeeId}/edit`);

        const startDate = $('#filter-start-date').val();
        const endDate = $('#filter-end-date').val();

        $.ajax({
            url: `/task-assignment/${employeeId}/detail`,
            type: 'GET',
            data: { start_date: startDate, end_date: endDate },
            success: function(res) {
                if (res.status === 'success') {
                    const emp = res.employee;
                    $('#modal-emp-name').text(emp.name);
                    $('#modal-emp-subtitle').text(`${emp.nik} • ${emp.position} (${emp.department})`);

                    if (emp.avatar_url) {
                        $('#modal-emp-avatar').html(`<img src="${emp.avatar_url}" alt="${emp.name}" class="w-10 h-10 rounded-full object-cover">`);
                    } else {
                        const initials = (emp.name || 'EM').substring(0, 2).toUpperCase();
                        $('#modal-emp-avatar').html(initials);
                    }

                    $('#modal-emp-task-count').text(`${res.assignments.length} Tugas diberikan`);

                    if (res.assignments.length === 0) {
                        $('#modal-detail-content').html(`
                            <div class="py-10 text-center text-gray-400 text-xs italic">
                                Tidak ada rincian tugas pada rentang tanggal ini.
                            </div>
                        `);
                    } else {
                        let html = '<div class="space-y-3">';
                        res.assignments.forEach(function(item) {
                            html += `
                                <div class="p-3.5 bg-white rounded-xl border border-gray-200 hover:bg-slate-50 transition-colors flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <a href="${item.url}" target="_blank" class="text-xs font-bold text-gray-900 hover:text-primary hover:underline flex items-center gap-1">
                                                ${item.task_title}
                                                <i data-lucide="external-link" class="w-3 h-3 text-gray-400"></i>
                                            </a>
                                        </div>
                                        <div class="flex items-center gap-2 text-[11px] text-gray-500 flex-wrap">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-semibold border ${item.task_type_badge}">
                                                ${item.task_type_label}
                                            </span>
                                            <span class="text-gray-300">•</span>
                                            <span>Mulai: <strong class="font-mono text-gray-700">${item.start_date}</strong></span>
                                            <span class="text-gray-300">•</span>
                                            <span>Deadline: <strong class="font-mono text-gray-700">${item.due_date}</strong></span>
                                            ${item.is_overdue ? '<span class="inline-flex items-center gap-1 text-[10px] text-red-600 font-bold bg-red-50 px-1.5 py-0.5 rounded border border-red-200"><i data-lucide="alert-triangle" class="w-3 h-3"></i> Terlambat</span>' : ''}
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3 self-end sm:self-auto shrink-0">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border ${item.status_badge}">
                                            ${item.status_label}
                                        </span>
                                        <span class="text-xs font-mono font-bold text-gray-600">${item.progress}%</span>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        $('#modal-detail-content').html(html);
                    }

                    if (typeof lucide !== 'undefined') {
                        lucide.createIcons();
                    }
                }
            },
            error: function() {
                $('#modal-detail-content').html(`
                    <div class="py-8 text-center text-red-500 text-xs">
                        Gagal memuat rincian tugas karyawan. Silakan coba kembali.
                    </div>
                `);
            }
        });
    }

    function closeDetailModal() {
        $('#modal-employee-detail').addClass('hidden');
    }

    // Delete assignment for employee (bulk all tasks)
    function deleteAssignment(employeeId, employeeName) {
        const startDate = $('#filter-start-date').val();
        const endDate = $('#filter-end-date').val();

        Swal.fire({
            title: 'Hapus Penugasan Tugas?',
            html: `Apakah Anda yakin ingin menghapus seluruh penugasan tugas untuk <strong>${employeeName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus Penugasan',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/task-assignment/${employeeId}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}',
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Dihapus',
                            text: response.message || 'Penugasan tugas berhasil dihapus.',
                            timer: 2000,
                            showConfirmButton: false
                        });
                        if (assignmentTable) assignmentTable.draw(false);
                        if (employeeTable) employeeTable.draw(false);
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal',
                            text: xhr.responseJSON?.message || 'Gagal menghapus penugasan tugas.'
                        });
                    }
                });
            }
        });
    }
</script>
@endpush

