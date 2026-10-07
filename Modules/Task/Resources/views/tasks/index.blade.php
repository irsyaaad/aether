@extends('layouts.app')

@section('title', 'Manajemen Tugas - ' . config('app.name'))
@section('page_title', 'Manajemen Tugas')

@section('toolbar_actions')
<div class="flex items-center gap-2">
    <a href="{{ route('task-categories.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
        <i data-lucide="tags" class="w-4 h-4 text-gray-500"></i>
        Kategori
    </a>
    <a href="{{ route('task-priorities.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
        <i data-lucide="alert-circle" class="w-4 h-4 text-gray-500"></i>
        Prioritas
    </a>
    <a href="{{ route('tasks.create') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Tambah Tugas
    </a>
</div>
@endsection

@push('styles')
<style>
    #tasks-table_wrapper { width: 100% !important; }
    #tasks-table_wrapper .dt-layout-row { width: 100% !important; }
    #tasks-table_wrapper .dt-layout-cell { padding: 0 !important; }
    #tasks-table_wrapper .dt-layout-table {
        width: 100% !important;
        overflow-x: auto !important;
        display: block !important;
    }
    #tasks-table_wrapper table { width: 100% !important; border-collapse: collapse !important; }

    /* Top Controls: Length Menu & Search */
    #tasks-table_wrapper .dt-layout-row:first-child,
    #tasks-table_wrapper .dt-layout-row:has(.dt-length),
    #tasks-table_wrapper .dt-layout-row:has(.dt-search) {
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

    #tasks-table_wrapper .dt-length {
        display: flex !important;
        align-items: center !important;
    }
    #tasks-table_wrapper .dt-length label {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        font-size: 0.8125rem !important;
        color: #4b5563 !important;
        font-weight: 500 !important;
        margin-bottom: 0 !important;
    }
    #tasks-table_wrapper .dt-length select {
        padding: 0.375rem 2rem 0.375rem 0.75rem !important;
        font-size: 0.8125rem !important;
        border-radius: 0.5rem !important;
        border: 1px solid #d1d5db !important;
        background-color: #ffffff !important;
        color: #374151 !important;
        outline: none !important;
        cursor: pointer !important;
    }
    #tasks-table_wrapper .dt-length select:focus {
        border-color: var(--color-primary, #e11d48) !important;
        box-shadow: 0 0 0 1px var(--color-primary, #e11d48) !important;
    }

    #tasks-table_wrapper .dt-search {
        display: flex !important;
        align-items: center !important;
    }
    #tasks-table_wrapper .dt-search label {
        display: inline-flex !important;
        align-items: center !important;
        gap: 0.5rem !important;
        font-size: 0.8125rem !important;
        color: #4b5563 !important;
        font-weight: 500 !important;
        margin-bottom: 0 !important;
    }
    #tasks-table_wrapper .dt-search input {
        padding: 0.375rem 0.75rem !important;
        font-size: 0.8125rem !important;
        border-radius: 0.5rem !important;
        border: 1px solid #d1d5db !important;
        background-color: #ffffff !important;
        color: #374151 !important;
        outline: none !important;
        min-width: 220px !important;
        transition: all 0.15s ease-in-out !important;
    }
    #tasks-table_wrapper .dt-search input:focus {
        border-color: var(--color-primary, #e11d48) !important;
        box-shadow: 0 0 0 1px var(--color-primary, #e11d48) !important;
    }

    /* Bottom Controls: Info & Pagination */
    #tasks-table_wrapper .dt-layout-table + .dt-layout-row,
    #tasks-table_wrapper .dt-layout-row:has(.dt-info),
    #tasks-table_wrapper .dt-layout-row:has(.dt-paging),
    #tasks-table_wrapper .dt-layout-row:last-child {
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

    #tasks-table_wrapper .dt-info {
        font-size: 0.875rem !important;
        color: #4b5563 !important;
        font-weight: 500 !important;
        margin: 0 !important;
        padding: 0 !important;
        line-height: 1.5 !important;
    }
    #tasks-table_wrapper .dt-layout-start,
    #tasks-table_wrapper .dt-start {
        display: flex !important;
        align-items: center !important;
        gap: 1rem !important;
    }
    #tasks-table_wrapper .dt-layout-end,
    #tasks-table_wrapper .dt-end {
        margin-left: auto !important;
        display: flex !important;
        align-items: center !important;
        justify-content: flex-end !important;
        gap: 0.5rem !important;
    }
    #tasks-table_wrapper .dt-paging {
        display: flex !important;
        align-items: center !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    #tasks-table_wrapper .dt-paging nav {
        display: flex !important;
        gap: 0.375rem !important;
        align-items: center !important;
    }
    #tasks-table_wrapper .dt-paging button,
    #tasks-table_wrapper .dt-paging .dt-paging-button {
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
    #tasks-table_wrapper .dt-paging button:hover:not(.current):not(:disabled),
    #tasks-table_wrapper .dt-paging .dt-paging-button:hover:not(.current):not(.disabled) {
        background-color: #f9fafb !important;
        border-color: #d1d5db !important;
        color: #111827 !important;
    }
    #tasks-table_wrapper .dt-paging button.current,
    #tasks-table_wrapper .dt-paging .dt-paging-button.current {
        background-color: var(--color-primary, #e11d48) !important;
        border-color: var(--color-primary, #e11d48) !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }
    #tasks-table_wrapper .dt-paging button:disabled,
    #tasks-table_wrapper .dt-paging .dt-paging-button.disabled {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
    }

    #tasks-table th {
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
    #tasks-table td {
        padding: 0.875rem 1.25rem !important;
        font-size: 0.875rem !important;
        border-bottom: 1px solid #f3f4f6 !important;
        color: #1f2937 !important;
        vertical-align: middle !important;
    }
    #tasks-table tbody tr:hover {
        background-color: #f9fafb !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">

    <!-- KPI / Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Total Tugas</p>
                <h3 class="text-2xl font-bold text-gray-900 mt-1" id="stat-total">{{ $stats['total'] }}</h3>
            </div>
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                <i data-lucide="clipboard-list" class="w-6 h-6"></i>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Sedang Berjalan</p>
                <h3 class="text-2xl font-bold text-amber-600 mt-1" id="stat-in-progress">{{ $stats['in_progress'] }}</h3>
            </div>
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <i data-lucide="clock" class="w-6 h-6"></i>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Terlambat (Overdue)</p>
                <h3 class="text-2xl font-bold text-red-600 mt-1" id="stat-overdue">{{ $stats['overdue'] }}</h3>
            </div>
            <div class="p-3 bg-red-50 text-red-600 rounded-xl">
                <i data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500">Selesai</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1" id="stat-completed">{{ $stats['completed'] }}</h3>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <i data-lucide="check-circle-2" class="w-6 h-6"></i>
            </div>
        </div>
    </div>

    <!-- Filter & Toolbar Panel -->
    <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <h4 class="text-sm font-semibold text-gray-800 flex items-center gap-2">
                <i data-lucide="filter" class="w-4 h-4 text-primary"></i>
                Filter & Pencarian Tugas
            </h4>
            <button type="button" id="btn-reset-filters" class="text-xs text-gray-500 hover:text-primary transition-colors flex items-center gap-1">
                <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                Reset Filter
            </button>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- Search Text -->
            <div class="col-span-1 sm:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">Cari Tugas</label>
                <div class="relative">
                    <input type="text" id="filter-search" placeholder="No. Tugas atau Judul..." class="w-full pl-8 pr-3 py-2 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5"></i>
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
                <select id="filter-status" class="w-full py-2 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Overdue Filter -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Keterlambatan</label>
                <select id="filter-overdue" class="w-full py-2 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua</option>
                    <option value="1">Hanya Terlambat</option>
                    <option value="0">Tepat Waktu / Selesai</option>
                </select>
            </div>

            <!-- Priority Filter -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Prioritas</label>
                <select id="filter-priority" class="w-full py-2 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Prioritas</option>
                    @foreach($priorities as $priority)
                        <option value="{{ $priority->id }}">{{ $priority->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Category Filter -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                <select id="filter-category" class="w-full py-2 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter (Harian, Bulanan, Insidentil) -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Tipe Tugas</label>
                <select id="filter-type" class="w-full py-2 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Tipe</option>
                    @foreach($taskTypes as $typeKey => $typeLabel)
                        <option value="{{ $typeKey }}">{{ $typeLabel }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Scope Filter (Master Template vs Generated Instances) -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Lingkup Tugas</label>
                <select id="filter-scope" class="w-full py-2 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="master" selected>Master Tugas (Definisi)</option>
                    <option value="instances">Riwayat Tergenerate</option>
                    <option value="all">Semua Data</option>
                </select>
            </div>

            <!-- Output / Execution Type Filter -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Bentuk Hasil</label>
                <select id="filter-output-type" class="w-full py-2 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Bentuk</option>
                    <option value="checklist">Checklist (Selesai/Belum)</option>
                    <option value="target">Target Angka / Nilai</option>
                    <option value="data_list">List Jawaban / Data</option>
                    <option value="stock_update">Update Stock Kosong</option>
                    <option value="full_custom">Full Custom</option>
                </select>
            </div>

            <!-- Assignee Filter -->
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Penerima Tugas</label>
                <select id="filter-employee" class="w-full py-2 px-2.5 text-xs border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    <option value="">Semua Karyawan</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}">{{ $employee->display_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <table id="tasks-table" class="w-full text-left">
            <thead>
                <tr>
                    <th class="text-center w-12">#</th>
                    <th>No. Tugas</th>
                    <th>Judul Tugas</th>
                    <th>Prioritas</th>
                    <th class="text-right">Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let tasksTable;

    $(document).ready(function() {
        tasksTable = $('#tasks-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('tasks.get-data') }}",
                data: function(d) {
                    let customSearch = $('#filter-search').val();
                    if (customSearch) {
                        d.search.value = customSearch;
                    }
                    d.task_type = $('#filter-type').val();
                    d.output_type = $('#filter-output-type').val();
                    d.status = $('#filter-status').val();
                    d.overdue = $('#filter-overdue').val();
                    d.priority_id = $('#filter-priority').val();
                    d.category_id = $('#filter-category').val();
                    d.employee_id = $('#filter-employee').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
                { data: 'task_number', name: 'task_number' },
                { data: 'title', name: 'title' },
                { data: 'priority_badge', name: 'priority.name', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-right' }
            ],
            order: [[1, 'desc']], // Order by task_number desc by default
            language: {
                emptyTable: "Belum ada data tugas",
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ tugas",
                infoEmpty: "Menampilkan 0 tugas",
                lengthMenu: "Tampilkan _MENU_ data",
                loadingRecords: "&nbsp;",
                search: "Search:",
                searchPlaceholder: "Cari tugas...",
                zeroRecords: "Tidak ada tugas yang sesuai filter"
            },
            drawCallback: function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });

        // Filter event bindings
        $('#filter-search').on('keyup input', function() {
            tasksTable.search(this.value).draw();
        });

        $(document).on('keyup input', '#tasks-table_wrapper .dt-search input', function() {
            $('#filter-search').val($(this).val());
        });

        $('#filter-type, #filter-output-type, #filter-status, #filter-overdue, #filter-priority, #filter-category, #filter-employee').on('change', function() {
            tasksTable.draw();
        });

        $('#btn-reset-filters').on('click', function() {
            $('#filter-search').val('');
            $('#tasks-table_wrapper .dt-search input').val('');
            $('#filter-type').val('');
            $('#filter-output-type').val('');
            $('#filter-status').val('');
            $('#filter-overdue').val('');
            $('#filter-priority').val('');
            $('#filter-category').val('');
            $('#filter-employee').val('');
            tasksTable.search('').draw();
        });
    });

    function deleteTask(id, taskNumber) {
        Swal.fire({
            title: 'Hapus Tugas?',
            text: `Apakah Anda yakin ingin menghapus tugas "${taskNumber}"? Tindakan ini dapat dipulihkan melalui database.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `{{ url('tasks') }}/${id}`,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            tasksTable.ajax.reload(null, false);
                        } else {
                            Swal.fire('Gagal!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        const message = xhr.responseJSON ? xhr.responseJSON.message : 'Terjadi kesalahan sistem.';
                        Swal.fire('Gagal!', message, 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
