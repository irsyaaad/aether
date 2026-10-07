@extends('layouts.app')

@section('title', 'Data Layanan - ' . config('app.name'))
@section('page_title', 'Data Layanan')

@section('breadcrumb')
    <a class="text-secondary-foreground hover:text-primary" href="{{ url('/') }}">Home</a>
    <span class="text-muted-foreground text-sm">/</span>
    <span class="text-secondary-foreground">Master</span>
    <span class="text-muted-foreground text-sm">/</span>
    <span class="text-mono">Layanan</span>
@endsection

@section('toolbar_actions')
    @if(check_access('layanan.create'))
        <button class="kt-btn kt-btn-sm kt-btn-primary" data-kt-drawer-toggle="#layanan_drawer">
            <i class="ki-filled ki-plus"></i> Tambah Layanan
        </button>
    @endif
@endsection

@push('styles')
<style>
    #layanan-table_wrapper { width: 100% !important; }
    #layanan-table_wrapper .dt-layout-row { width: 100% !important; }
    #layanan-table_wrapper .dt-layout-cell { width: 100% !important; padding: 0 !important; }
    #layanan-table_wrapper .dt-layout-table { width: 100% !important; }
    #layanan-table_wrapper table { width: 100% !important; table-layout: auto !important; }
    .dataTables_empty { padding: 3rem !important; text-align: center; color: var(--secondary-foreground); font-weight: 500; }
    #layanan-table_wrapper .dt-layout-row:last-child { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-top: 1px solid var(--border); font-size: 0.875rem; color: var(--secondary-foreground); gap: 1.25rem; flex-wrap: wrap; }
    #layanan-table_wrapper .dt-layout-cell.dt-start { order: 1; display: flex; align-items: center; gap: 1rem; }
    #layanan-table_wrapper .dt-layout-cell.dt-end { order: 2; margin-left: auto; display: flex; justify-content: flex-end; }
    #layanan-table_wrapper .dt-paging nav { display: flex; gap: 0.25rem; align-items: center; }
    #layanan-table_wrapper .dt-paging button { display: inline-flex; align-items: center; justify-content: center; min-width: 2rem; height: 2rem; padding: 0 0.5rem; border: 1px solid var(--border); border-radius: 0.375rem; background: transparent; font-size: 0.875rem; font-weight: 500; cursor: pointer; color: var(--foreground); transition: all 0.15s ease-in-out; }
    #layanan-table_wrapper .dt-paging button.current { background: var(--primary); color: #fff; border-color: var(--primary); }
    #layanan-table_wrapper .dt-paging button:hover:not(.current) { background: var(--accent); border-color: var(--border-active); }
    #layanan-table_wrapper .dt-paging button:disabled { opacity: 0.5; cursor: not-allowed; }
    #layanan-table th { padding: 0.75rem 1rem !important; font-weight: 600 !important; text-align: left; color: var(--secondary-foreground); }
    #layanan-table td { padding: 0.875rem 1rem !important; vertical-align: middle; color: var(--foreground); }
    #layanan-table tbody tr:hover { background-color: var(--muted-light, #f9fafb); }
    .kt-badge { font-size: 0.7rem; padding: 2px 8px; border-radius: 10px; font-weight: 500; }
    .kt-badge-success { background: color-mix(in oklab, #10b981 15%, transparent); color: #10b981; }
    .kt-badge-danger { background: color-mix(in oklab, #ef4444 15%, transparent); color: #ef4444; }
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
            <h3 class="kt-card-title text-sm" id="table-title">Menampilkan data Layanan...</h3>
            <div class="flex flex-wrap gap-2 lg:gap-5">
                <div class="flex">
                    <label class="kt-input">
                        <i class="ki-filled ki-magnifier"></i>
                        <input id="custom-search-input" placeholder="Cari Layanan..." type="text" value=""/>
                    </label>
                </div>
            </div>
        </div>
        <div class="kt-card-content">
            <div class="kt-scrollable-x-auto min-h-[300px]">
                <table class="kt-table table-auto kt-table-border w-full" id="layanan-table" style="width:100%">
                    <thead>
                        <tr>
                            <th class="w-[60px] text-center">No</th>
                            <th class="min-w-[120px]">Kode Layanan</th>
                            <th class="min-w-[250px]">Nama Layanan</th>
                            <th class="min-w-[100px]">Status</th>
                            <th class="w-[100px] text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
    </div>
</div>

@if(check_access('layanan.create'))
<div class="hidden kt-drawer kt-drawer-end card flex-col max-w-[90%] w-[700px] top-5 bottom-5 end-5 rounded-xl border border-border" data-kt-drawer="true" data-kt-drawer-container="body" id="layanan_drawer">
    <div class="flex items-center justify-between gap-2.5 text-sm text-mono font-semibold px-5 py-3 border-b border-b-border">
        <span id="drawer_title">Tambah Layanan</span>
        <button class="kt-btn kt-btn-sm kt-btn-icon kt-btn-dim shrink-0" data-kt-drawer-dismiss="true">
            <i class="ki-filled ki-cross"></i>
        </button>
    </div>
    <div class="flex-1 overflow-y-auto p-5">
        <form id="form_layanan" class="flex flex-col gap-4">
            @csrf
            <input type="hidden" id="drawer_method" value="POST">
            <input type="hidden" id="drawer_id" value="">
            <div id="drawer_errors" class="hidden kt-alert kt-alert-destructive">
                <div class="kt-alert-content"><div class="kt-alert-description"><ul class="list-disc ps-4 text-sm" id="drawer_errors_list"></ul></div></div>
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-foreground">Kode Layanan <span class="text-destructive">*</span></label>
                <input type="text" id="drawer_kode" name="kode_layanan" class="kt-input w-full" placeholder="Contoh: REG, EKS, ONS" required maxlength="20">
            </div>
            <div class="flex flex-col gap-1">
                <label class="text-sm font-medium text-foreground">Nama Layanan <span class="text-destructive">*</span></label>
                <input type="text" id="drawer_nama" name="nama_layanan" class="kt-input w-full" placeholder="Masukkan nama layanan" required>
            </div>
            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" class="kt-btn kt-btn-outline" data-kt-drawer-dismiss="true">Batal</button>
                <button type="submit" class="kt-btn kt-btn-primary"><i class="ki-filled ki-check text-sm"></i> Simpan</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    $(document).ready(function() {
        var table = $('#layanan-table').DataTable({
            autoWidth: false, processing: true, serverSide: true,
            ajax: {
                url: '{{ route("layanan.get-data") }}',
                data: function(d) {
                    d.search = $('#custom-search-input').val();
                }
            },
            pageLength: 25, order: [[1, 'asc']],
            language: {
                search: '', searchPlaceholder: 'Cari Layanan...',
                info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ Layanan',
                infoEmpty: 'Menampilkan 0 Layanan',
                infoFiltered: '(difilter dari _MAX_ total Layanan)',
                loadingRecords: '&nbsp;',
                paginate: { first: '&laquo;', previous: '&lsaquo;', next: '&rsaquo;', last: '&raquo;' }
            },
            layout: { topStart: null, topEnd: null, bottomStart: 'info', bottomEnd: 'paging' },
            columnDefs: [{ orderable: false, targets: [0, 4] }, { className: "align-middle", targets: "_all" }],
            columns: [
                { data: 'DT_RowIndex', searchable: false },
                { data: 'kode_layanan' },
                { data: 'nama_layanan' },
                { data: 'status', orderable: false },
                { data: 'aksi', searchable: false }
            ],
            drawCallback: function(settings) {
                if (typeof KTMenu !== 'undefined') { KTMenu.init(); }
                let info = table.page.info();
                let titleText = 'Menampilkan 0 Layanan';
                if (info.recordsTotal > 0) {
                    titleText = 'Menampilkan ' + (info.start + 1) + ' sampai ' + info.end + ' dari ' + info.recordsTotal + ' Layanan';
                }
                $('#table-title').text(titleText);
            }
        });
        $('#custom-search-input').on('keyup', function() { table.search(this.value).draw(); });

        $('#form_layanan').on('submit', function(e) {
            e.preventDefault();
            var $form = $(this);
            var $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).html('<i class="ki-filled ki-spinner-record text-sm animate-spin"></i> Menyimpan...');
            $('#drawer_errors').addClass('hidden');

            var method = $('#drawer_method').val();
            var kode = $('#drawer_kode').val().trim();
            var nama = $('#drawer_nama').val().trim();
            var id = $('#drawer_id').val();
            var token = $('input[name="_token"]').val();

            if (!kode) { Swal.fire('Error', 'Kode Layanan wajib diisi', 'error'); $btn.prop('disabled', false).html('<i class="ki-filled ki-check text-sm"></i> Simpan'); return; }
            if (!nama) { Swal.fire('Error', 'Nama Layanan wajib diisi', 'error'); $btn.prop('disabled', false).html('<i class="ki-filled ki-check text-sm"></i> Simpan'); return; }

            var url = method === 'PUT' ? '{{ route("layanan.update", ":id") }}'.replace(':id', id) : '{{ route("layanan.store") }}';
            var data = { kode_layanan: kode, nama_layanan: nama, _token: token };
            if (method === 'PUT') { data._method = 'PUT'; }

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                headers: { 'Accept': 'application/json' },
                success: function(res) {
                    if (res.status === 'success') {
                        Swal.fire({ title: 'Berhasil!', text: res.message, icon: 'success', timer: 1500, showConfirmButton: false });
                        table.ajax.reload(null, false);
                        var drawerEl = document.getElementById('layanan_drawer');
                        if (drawerEl && typeof KTDrawer !== 'undefined') {
                            var inst = KTDrawer.getInstance(drawerEl);
                            if (inst) inst.hide();
                        }
                    } else {
                        Swal.fire({ title: 'Gagal!', text: res.message, icon: 'error' });
                    }
                },
                error: function(xhr) {
                    var $errBox = $('#drawer_errors');
                    $errBox.addClass('hidden').find('ul').empty();
                    var msg = 'Terjadi kesalahan pada server.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errs = [];
                        $.each(xhr.responseJSON.errors, function(k, v) { errs.push('<li>' + v[0] + '</li>'); });
                        $errBox.find('ul').html(errs.join(''));
                        $errBox.removeClass('hidden');
                        return;
                    }
                    if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
                    Swal.fire({ title: 'Error!', html: msg, icon: 'error' });
                },
                complete: function() {
                    $btn.prop('disabled', false).html('<i class="ki-filled ki-check text-sm"></i> Simpan');
                }
            });
        });

        var drawerEl = document.getElementById('layanan_drawer');
        if (drawerEl) {
            drawerEl.addEventListener('kt.drawer.after.hide', function() {
                $('#form_layanan')[0].reset();
                $('#drawer_errors').addClass('hidden').find('ul').empty();
                $('#drawer_method').val('POST');
                $('#drawer_id').val('');
                $('#drawer_kode').val('').removeAttr('readonly');
                $('#drawer_title').text('Tambah Layanan');
                var $btn = $('#form_layanan').find('button[type="submit"]');
                $btn.prop('disabled', false).html('<i class="ki-filled ki-check text-sm"></i> Simpan');
            });
        }
    });

    function openDrawer(mode, kode, nama) {
        $('#drawer_errors').addClass('hidden').find('ul').empty();

        if (mode === 'edit') {
            $('#drawer_title').text('Edit Layanan');
            $('#drawer_method').val('PUT');
            $('#drawer_id').val(kode);
            $('#drawer_kode').val(kode).attr('readonly', true);
            $('#drawer_nama').val(nama);
        } else {
            $('#drawer_title').text('Tambah Layanan');
            $('#drawer_method').val('POST');
            $('#drawer_id').val('');
            $('#drawer_kode').val('').removeAttr('readonly');
            $('#drawer_nama').val('');
        }

        var drawerEl = document.getElementById('layanan_drawer');
        if (typeof KTDrawer !== 'undefined') {
            var inst = KTDrawer.getInstance(drawerEl);
            if (inst) inst.show();
            else { drawerEl.classList.remove('hidden'); drawerEl.classList.add('show'); }
        } else {
            drawerEl.classList.remove('hidden');
            drawerEl.classList.add('show');
        }
    }

    function deleteLayanan(id) {
        let url = '{{ route("layanan.destroy", ":id") }}'.replace(':id', id);
        Swal.fire({
            title: "Yakin hapus Layanan ini?",
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
                    url: url, type: "DELETE",
                    headers: { 'Accept': 'application/json' },
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(res) {
                        if (res.status === "success") {
                            Swal.fire({ title: "Terhapus!", text: res.message, icon: "success", timer: 1500, showConfirmButton: false });
                            $('#layanan-table').DataTable().ajax.reload(null, false);
                        } else { Swal.fire({ title: "Gagal!", text: res.message, icon: "error" }); }
                    },
                    error: function(err) { Swal.fire({ title: "Error!", text: "Terjadi kesalahan pada server.", icon: "error" }); }
                });
            }
        });
    }
</script>
@endpush
