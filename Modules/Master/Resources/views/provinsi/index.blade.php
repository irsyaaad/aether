@extends('layouts.app')

@section('title', 'Data Provinsi - ' . config('app.name'))
@section('page_title', 'Data Provinsi')

@section('breadcrumb')
    <a class="text-secondary-foreground hover:text-primary" href="{{ url('/') }}">Home</a>
    <span class="text-muted-foreground text-sm">/</span>
    <span class="text-secondary-foreground">Master</span>
    <span class="text-muted-foreground text-sm">/</span>
    <span class="text-mono">Provinsi</span>
@endsection

@section('toolbar_actions')
    <a class="kt-btn kt-btn-sm kt-btn-primary" href="{{ route('wilayah-provinsi.create') }}">
        <i class="ki-filled ki-plus"></i> Tambah Provinsi
    </a>
@endsection

@push('styles')
<style>
    #provinsi-table_wrapper { width: 100% !important; }
    #provinsi-table_wrapper .dt-layout-row { width: 100% !important; }
    #provinsi-table_wrapper .dt-layout-cell { width: 100% !important; padding: 0 !important; }
    #provinsi-table_wrapper .dt-layout-table { width: 100% !important; }
    #provinsi-table_wrapper table { width: 100% !important; table-layout: auto !important; }
    .dataTables_empty { padding: 3rem !important; text-align: center; color: var(--secondary-foreground); font-weight: 500; }
    #provinsi-table_wrapper .dt-layout-row:last-child { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-top: 1px solid var(--border); font-size: 0.875rem; color: var(--secondary-foreground); gap: 1.25rem; flex-wrap: wrap; }
    #provinsi-table_wrapper .dt-layout-cell.dt-start { order: 1; display: flex; align-items: center; gap: 1rem; }
    #provinsi-table_wrapper .dt-layout-cell.dt-end { order: 2; margin-left: auto; display: flex; justify-content: flex-end; }
    #provinsi-table_wrapper .dt-paging nav { display: flex; gap: 0.25rem; align-items: center; }
    #provinsi-table_wrapper .dt-paging button { display: inline-flex; align-items: center; justify-content: center; min-width: 2rem; height: 2rem; padding: 0 0.5rem; border: 1px solid var(--border); border-radius: 0.375rem; background: transparent; font-size: 0.875rem; font-weight: 500; cursor: pointer; color: var(--foreground); transition: all 0.15s ease-in-out; }
    #provinsi-table_wrapper .dt-paging button.current { background: var(--primary); color: #fff; border-color: var(--primary); }
    #provinsi-table_wrapper .dt-paging button:hover:not(.current) { background: var(--accent); border-color: var(--border-active); }
    #provinsi-table_wrapper .dt-paging button:disabled { opacity: 0.5; cursor: not-allowed; }
    #provinsi-table th { padding: 0.75rem 1rem !important; font-weight: 600 !important; text-align: left; color: var(--secondary-foreground); }
    #provinsi-table td { padding: 0.875rem 1rem !important; vertical-align: middle; color: var(--foreground); }
    #provinsi-table tbody tr:hover { background-color: var(--muted-light, #f9fafb); }
</style>
@endpush

@section('content')
<div class="kt-container-fixed">
    @if(Session::has('success'))
        <div class="kt-alert kt-alert-success mb-5">
            <div class="kt-alert-content">
                <p class="kt-alert-description">{{ Session::get('success') }}</p>
            </div>
        </div>
    @endif

    <div class="grid gap-5 lg:gap-7.5">
    <div class="kt-card kt-card-grid min-w-full">
        <div class="kt-card-header flex-wrap gap-2">
            <h3 class="kt-card-title text-sm" id="table-title">Menampilkan data Provinsi...</h3>
            <div class="flex flex-wrap gap-2 lg:gap-5">
                <div class="flex">
                    <label class="kt-input">
                        <i class="ki-filled ki-magnifier"></i>
                        <input id="custom-search-input" placeholder="Cari Provinsi..." type="text" value=""/>
                    </label>
                </div>
            </div>
        </div>
        <div class="kt-card-content">
            <div class="kt-scrollable-x-auto min-h-[300px]">
                <table class="kt-table table-auto kt-table-border w-full" id="provinsi-table" style="width:100%">
                    <thead>
                        <tr>
                            <th class="w-[60px] text-center">No</th>
                            <th class="min-w-[100px]">Kode</th>
                            <th class="min-w-[200px]">Nama Provinsi</th>
                            <th class="min-w-[120px]">Jumlah Kota/Kab</th>
                            <th class="w-[80px] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        var table = $('#provinsi-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("wilayah-provinsi.get-data") }}',
                data: function(d) {
                    d.search = $('#custom-search-input').val();
                }
            },
            pageLength: 25,
            order: [[1, 'asc']],
            language: {
                search: '',
                searchPlaceholder: 'Cari Provinsi...',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ Provinsi',
                infoEmpty: 'Menampilkan 0 Provinsi',
                infoFiltered: '(difilter dari _MAX_ total Provinsi)',
                lengthMenu: 'Tampilkan _MENU_ per halaman',
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
                { orderable: false, targets: [0, 4] },
                { className: "align-middle font-medium text-sm text-foreground", targets: "_all" }
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center' },
                { data: 'id' },
                { data: 'name' },
                { data: 'jumlah_kota_kab', orderable: false },
                { data: 'action', searchable: false }
            ],
            drawCallback: function(settings) {
                var api = this.api();
                let info = api.page.info();
                let titleText = 'Menampilkan 0 Provinsi';
                if (info.recordsTotal > 0) {
                    titleText = 'Menampilkan ' + (info.start + 1) + ' sampai ' + info.end + ' dari ' + info.recordsTotal + ' Provinsi';
                }
                $('#table-title').text(titleText);
            }
        });

        $('#custom-search-input').on('keyup', function() { table.search(this.value).draw(); });
    });

    function deleteProvinsi(id) {
        let url = "{{ route('wilayah-provinsi.destroy', ':id') }}".replace(':id', id);
        Swal.fire({
            title: "Yakin hapus Provinsi ini?",
            text: "Data akan dihapus permanen!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal",
            customClass: {
                confirmButton: 'kt-btn kt-btn-destructive me-2',
                cancelButton: 'kt-btn kt-btn-outline',
                popup: 'kt-modal-content',
                header: 'kt-modal-header',
                title: 'kt-modal-title'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: url,
                    type: "DELETE",
                    data: { _token: "{{ csrf_token() }}" },
                    success: function(res) {
                        if (res.status === "success") {
                            Swal.fire({ title: "Terhapus!", text: res.message, icon: "success", timer: 1500, showConfirmButton: false });
                            table.ajax.reload(null, false);
                        } else {
                            Swal.fire({ title: "Gagal!", text: res.message, icon: "error" });
                        }
                    },
                    error: function(err) {
                        Swal.fire({ title: "Error!", text: "Terjadi kesalahan pada server.", icon: "error" });
                    }
                });
            }
        });
    }
</script>
@endpush
