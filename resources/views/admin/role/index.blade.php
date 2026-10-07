@extends('layouts.app')

@section('title', 'Manajemen Role - ' . config('app.name'))
@section('page_title', 'Role')

@push('styles')
<style>
    #roles-table_wrapper { width: 100% !important; }
    #roles-table_wrapper .dt-layout-row { width: 100% !important; }
    #roles-table_wrapper .dt-layout-cell { width: 100% !important; padding: 0 !important; }
    #roles-table_wrapper .dt-layout-table { width: 100% !important; }
    #roles-table_wrapper table { width: 100% !important; border-collapse: collapse !important; }
    .dataTables_empty { padding: 2.5rem !important; text-align: center; color: #6b7280; font-weight: 400; font-size: 0.875rem; }
    #roles-table_wrapper .dt-layout-row:last-child {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1rem 1.5rem;
        border-top: 1px solid #e5e7eb;
        font-size: 0.875rem;
        color: #6b7280;
        gap: 1rem;
        flex-wrap: wrap;
        background-color: #ffffff;
    }
    #roles-table_wrapper .dt-layout-cell.dt-start { order: 1; display: flex; align-items: center; gap: 1rem; }
    #roles-table_wrapper .dt-layout-cell.dt-end { order: 2; margin-left: auto; display: flex; justify-content: flex-end; }
    #roles-table_wrapper .dt-paging nav { display: flex; gap: 0.25rem; align-items: center; }
    #roles-table_wrapper .dt-paging button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        padding: 0 0.5rem;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        background: #ffffff;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        color: #374151;
        transition: all 0.15s ease-in-out;
    }
    #roles-table_wrapper .dt-paging button:hover:not(.current):not(:disabled) {
        background: #f9fafb;
        border-color: #d1d5db;
    }
    #roles-table_wrapper .dt-paging button.current {
        background: #e11d48;
        color: #ffffff;
        border-color: #e11d48;
    }
    #roles-table_wrapper .dt-paging button:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }
    #roles-table_wrapper .dt-length select {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.25rem 1.75rem 0.25rem 0.5rem;
        font-size: 0.875rem;
        color: #374151;
        background-color: #ffffff;
        outline: none;
    }
    #roles-table th {
        padding: 1rem 1.5rem !important;
        font-weight: 500 !important;
        font-size: 0.875rem !important;
        text-align: left;
        color: #4b5563 !important;
        background-color: #f9fafb !important;
        border-bottom: 1px solid #e5e7eb !important;
    }
    #roles-table td {
        padding: 1rem 1.5rem !important;
        vertical-align: middle;
        color: #111827;
        border-bottom: 1px solid #f3f4f6 !important;
    }
    #roles-table tbody tr:hover {
        background-color: #f9fafb;
        transition: background-color 0.15s ease-in-out;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Toolbar / Filter & Action -->
    <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-4">
        <div class="flex flex-wrap sm:flex-nowrap items-center gap-3 w-full sm:w-auto">
            <div class="relative w-full sm:w-80">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400"></i>
                <input 
                    type="text" 
                    id="custom-search-input" 
                    placeholder="Cari Role..." 
                    class="w-full rounded-md border border-gray-300 bg-white py-2 pl-9 pr-4 text-sm shadow-sm focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                />
            </div>
        </div>

        <a 
            href="{{ route('roles.create') }}"
            class="inline-flex items-center justify-center rounded-md bg-primary px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-primary/90 transition-colors shrink-0"
        >
            <i data-lucide="plus" class="mr-2 h-4 w-4"></i> Tambah Role
        </a>
    </div>

    <!-- Table Card -->
    <div class="rounded-md border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left" id="roles-table" style="width: 100%">
                <thead class="bg-gray-50 text-gray-600 font-medium border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap w-16 text-center">No</th>
                        <th class="px-6 py-4 whitespace-nowrap">Nama Role</th>
                        <th class="px-6 py-4 whitespace-nowrap">Aplikasi (App ID)</th>
                        <th class="px-6 py-4 text-right whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200"></tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var table;
    $(document).ready(function() {
        table = $('#roles-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('roles.data') }}",
                data: function(d) {
                    d.search = $('#custom-search-input').val();
                }
            },
            pageLength: 10,
            order: [[1, 'asc']],
            language: {
                search: '',
                searchPlaceholder: 'Cari Role...',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ Role',
                infoEmpty: 'Menampilkan 0 Role',
                infoFiltered: '(difilter dari _MAX_ total)',
                lengthMenu: 'Tampilkan _MENU_ data',
                loadingRecords: '&nbsp;',
                paginate: { first: '&laquo;', previous: '&lsaquo;', next: '&rsaquo;', last: '&raquo;' }
            },
            layout: {
                topStart: null,
                topEnd: null,
                bottomStart: ['pageLength', 'info'],
                bottomEnd: 'paging'
            },
            columnDefs: [
                { orderable: false, targets: [0, 2, 3] },
                { className: "align-middle font-medium text-sm text-gray-900", targets: "_all" }
            ],
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    searchable: false,
                    orderable: false,
                    className: 'text-center text-gray-500 font-medium'
                },
                {
                    data: 'nm_role',
                    name: 'nm_role',
                    className: 'font-semibold text-gray-900'
                },
                {
                    data: 'app_id',
                    name: 'app_id',
                    className: 'text-gray-600',
                    render: function(data) {
                        return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200 font-mono">' +
                                (data || '-').toUpperCase() + '</span>';
                    }
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false,
                    className: 'text-right'
                },
            ],
            drawCallback: function(settings) {
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
        });

        $('#custom-search-input').on('keyup', function() {
            table.search(this.value).draw();
        });

        // Delete confirmation matching companies customClass
        $(document).on('click', '.btn-delete', function(e) {
            e.preventDefault();
            const url = $(this).data('url');
            const name = $(this).data('name') || 'Role';

            Swal.fire({
                title: "Hapus Role?",
                text: "Role \"" + name + "\" akan dihapus permanen.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#e11d48",
                cancelButtonColor: "#6b7280",
                confirmButtonText: "Ya, Hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    popup: 'rounded-xl',
                    confirmButton: 'rounded-md px-4 py-2 font-medium text-sm',
                    cancelButton: 'rounded-md px-4 py-2 font-medium text-sm'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: "success",
                                    title: "Berhasil!",
                                    text: res.message || "Data role berhasil dihapus.",
                                    timer: 1500,
                                    showConfirmButton: false,
                                    customClass: {
                                        popup: 'rounded-xl',
                                        confirmButton: 'rounded-md px-4 py-2 font-medium text-sm'
                                    }
                                });
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: "error",
                                    title: "Gagal!",
                                    text: res.message || "Tidak dapat menghapus data role.",
                                    customClass: {
                                        popup: 'rounded-xl',
                                        confirmButton: 'rounded-md px-4 py-2 font-medium text-sm'
                                    }
                                });
                            }
                        },
                        error: function(xhr) {
                            Swal.fire({
                                icon: "error",
                                title: "Error!",
                                text: xhr.responseJSON?.message || "Terjadi kesalahan saat menghapus data.",
                                customClass: {
                                    popup: 'rounded-xl',
                                    confirmButton: 'rounded-md px-4 py-2 font-medium text-sm'
                                }
                            });
                        }
                    });
                }
            });
        });
    });
</script>
@endpush
