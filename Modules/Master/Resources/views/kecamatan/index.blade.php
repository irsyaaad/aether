@extends('layouts.app')

@section('title', 'Data Kecamatan - ' . config('app.name'))
@section('page_title', 'Data Kecamatan')

@section('breadcrumb')
    <a class="text-secondary-foreground hover:text-primary" href="{{ url('/') }}">Home</a>
    <span class="text-muted-foreground text-sm">/</span>
    <span class="text-secondary-foreground">Master</span>
    <span class="text-muted-foreground text-sm">/</span>
    <span class="text-mono">Kecamatan</span>
@endsection

@section('toolbar_actions')
    <a class="kt-btn kt-btn-sm kt-btn-primary" href="{{ route('wilayah-kecamatan.create') }}">
        <i class="ki-filled ki-plus"></i> Tambah Kecamatan
    </a>
@endsection

@push('styles')
<style>
    #kecamatan-table_wrapper { width: 100% !important; }
    #kecamatan-table_wrapper .dt-layout-row { width: 100% !important; }
    #kecamatan-table_wrapper .dt-layout-cell { width: 100% !important; padding: 0 !important; }
    #kecamatan-table_wrapper .dt-layout-table { width: 100% !important; }
    #kecamatan-table_wrapper table { width: 100% !important; table-layout: auto !important; }
    .dataTables_empty { padding: 3rem !important; text-align: center; color: var(--secondary-foreground); font-weight: 500; }
    #kecamatan-table_wrapper .dt-layout-row:last-child { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-top: 1px solid var(--border); font-size: 0.875rem; color: var(--secondary-foreground); gap: 1.25rem; flex-wrap: wrap; }
    #kecamatan-table_wrapper .dt-layout-cell.dt-start { order: 1; display: flex; align-items: center; gap: 1rem; }
    #kecamatan-table_wrapper .dt-layout-cell.dt-end { order: 2; margin-left: auto; display: flex; justify-content: flex-end; }
    #kecamatan-table_wrapper .dt-paging nav { display: flex; gap: 0.25rem; align-items: center; }
    #kecamatan-table_wrapper .dt-paging button { display: inline-flex; align-items: center; justify-content: center; min-width: 2rem; height: 2rem; padding: 0 0.5rem; border: 1px solid var(--border); border-radius: 0.375rem; background: transparent; font-size: 0.875rem; font-weight: 500; cursor: pointer; color: var(--foreground); transition: all 0.15s ease-in-out; }
    #kecamatan-table_wrapper .dt-paging button.current { background: var(--primary); color: #fff; border-color: var(--primary); }
    #kecamatan-table_wrapper .dt-paging button:hover:not(.current) { background: var(--accent); border-color: var(--border-active); }
    #kecamatan-table_wrapper .dt-paging button:disabled { opacity: 0.5; cursor: not-allowed; }
    #kecamatan-table th { padding: 0.75rem 1rem !important; font-weight: 600 !important; text-align: left; color: var(--secondary-foreground); }
    #kecamatan-table td { padding: 0.875rem 1rem !important; vertical-align: middle; color: var(--foreground); }
    #kecamatan-table tbody tr:hover { background-color: var(--muted-light, #f9fafb); }
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
            <h3 class="kt-card-title text-sm" id="table-title">Menampilkan data Kecamatan...</h3>
            <div class="flex flex-wrap gap-2 lg:gap-5">
                <div class="flex">
                    <label class="kt-input">
                        <i class="ki-filled ki-magnifier"></i>
                        <input id="custom-search-input" placeholder="Cari Kecamatan..." type="text" value=""/>
                    </label>
                </div>
                <div class="flex flex-wrap gap-2.5">
                    <select class="kt-select w-44" id="filter-province">
                        <option value="">Semua Provinsi</option>
                        @foreach($provinces as $province)
                            <option value="{{ $province->id }}">{{ $province->name }}</option>
                        @endforeach
                    </select>
                    <select class="kt-select w-44" id="filter-regency" disabled>
                        <option value="">Semua Kota/Kab</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="kt-card-content">
            <div class="kt-scrollable-x-auto min-h-[300px]">
                <table class="kt-table table-auto kt-table-border w-full" id="kecamatan-table" style="width:100%">
                    <thead>
                        <tr>
                            <th class="w-[60px] text-center">No</th>
                            <th class="min-w-[100px]">Kode</th>
                            <th class="min-w-[200px]">Nama Kecamatan</th>
                            <th class="min-w-[160px]">Kota/Kabupaten</th>
                            <th class="min-w-[140px]">Provinsi</th>
                            <th class="min-w-[100px]">Jumlah Desa</th>
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
        var table = $('#kecamatan-table').DataTable({
            autoWidth: false,
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("wilayah-kecamatan.get-data") }}',
                data: function(d) {
                    d.search = $('#custom-search-input').val();
                    d.province_id = $('#filter-province').val();
                    d.regency_id = $('#filter-regency').val();
                }
            },
            pageLength: 25,
            order: [[1, 'asc']],
            language: {
                search: '',
                searchPlaceholder: 'Cari Kecamatan...',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ Kecamatan',
                infoEmpty: 'Menampilkan 0 Kecamatan',
                infoFiltered: '(difilter dari _MAX_ total Kecamatan)',
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
                { orderable: false, targets: [0, 6] },
                { className: "align-middle font-medium text-sm text-foreground", targets: "_all" }
            ],
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false, orderable: false, className: 'text-center' },
                { data: 'id' },
                { data: 'name' },
                { data: 'kota_kab', orderable: false },
                { data: 'provinsi', orderable: false },
                { data: 'jumlah_desa', orderable: false },
                { data: 'action', searchable: false }
            ],
            drawCallback: function(settings) {
                var api = this.api();
                let info = api.page.info();
                let titleText = 'Menampilkan 0 Kecamatan';
                if (info.recordsTotal > 0) {
                    titleText = 'Menampilkan ' + (info.start + 1) + ' sampai ' + info.end + ' dari ' + info.recordsTotal + ' Kecamatan';
                }
                $('#table-title').text(titleText);
            }
        });

        $('#custom-search-input').on('keyup', function() { table.search(this.value).draw(); });

        $('#filter-province').on('change', function() {
            let provinceId = $(this).val();
            let regencySelect = $('#filter-regency');
            regencySelect.html('<option value="">Semua Kota/Kab</option>').prop('disabled', true);
            if (provinceId) {
                $.get('{{ route("wilayah-kecamatan.get-regencies", ":id") }}'.replace(':id', provinceId), function(data) {
                    $.each(data, function(i, item) {
                        regencySelect.append('<option value="' + item.id + '">' + item.name + '</option>');
                    });
                    regencySelect.prop('disabled', false);
                });
            }
            table.ajax.reload(null, false);
        });

        $('#filter-regency').on('change', function() { table.ajax.reload(null, false); });
    });

    function deleteKecamatan(id) {
        let url = "{{ route('wilayah-kecamatan.destroy', ':id') }}".replace(':id', id);
        Swal.fire({
            title: "Yakin hapus Kecamatan ini?",
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
