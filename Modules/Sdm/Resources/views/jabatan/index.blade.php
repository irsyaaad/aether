@extends('template.root')
@section('page-name', 'jabatan')
@section('title-page', 'List Jabatan')
@section('add-page')
    <a href="{{ route('jabatan.create') }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus"></i> Tambah Jabatan
    </a>
@endsection

@section('content')
    <div class="table-responsive">
        <table id="jabatan-table" class="table responsive table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Jabatan</th>
                    <th>Nama Jabatan</th>
                    <th>Level</th>
                    <th>Departemen</th>
                    <th>Status</th>
                    <th>User</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>

    <script>
        let table;
        $(document).ready(function() {
            table = $('#jabatan-table').DataTable({
                pageLength: 50,
                searching: true,
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('jabatan.get-data') }}",
                    data: function(d) {
                        d.url = "{{ request()->segment(1) }}";
                    }
                },
                columns: [
                    {
                        data: null,
                        name: 'index',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row, meta) {
                            return meta.row + meta.settings._iDisplayStart + 1;
                        }
                    },
                    {
                        data: 'kode_jabatan',
                        name: 'kode_jabatan'
                    },
                    {
                        data: 'nama_jabatan',
                        name: 'nama_jabatan'
                    },
                    {
                        data: 'level',
                        name: 'level'
                    },
                    {
                        data: 'departemen',
                        name: 'departemen'
                    },
                    {
                        data: 'status_badge',
                        name: 'is_active',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'dibuat_oleh',
                        name: 'creator.nm_user',
                        orderable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
        });

        // Handle delete button click
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            let url = "/jabatan/" + id;

            Swal.fire({
                title: "Yakin hapus?",
                text: "Data akan hilang permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: url,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            if (res.status === "success") {
                                Swal.fire({
                                    title: "Terhapus!",
                                    text: res.message,
                                    icon: "success",
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null, false);
                            }
                        },
                        error: function(xhr) {
                            let message = xhr.responseJSON?.message || 'Terjadi kesalahan';
                            Swal.fire({
                                title: "Error!",
                                text: message,
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection