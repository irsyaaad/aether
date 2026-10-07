@extends('template.root')
@section('page-name', 'sop')
@section('title-page', 'List SOP')
@section('add-page')
    <a href="{{ route('sop.create') }}" class="btn btn-sm btn-primary">
        <i class="fa fa-plus"></i> Tambah SOP
    </a>
@endsection

@section('content')
    <div class="table-responsive">
        <table id="sop-table" class="table responsive table-bordered">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode SOP</th>
                    <th>Judul SOP</th>
                    <th>Jabatan</th>
                    <th>Versi</th>
                    <th>Status</th>
                    <th>File</th>
                    <th>Tanggal Berlaku</th>
                    <th>User</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>

    <script>
        let table;
        $(document).ready(function() {
            table = $('#sop-table').DataTable({
                pageLength: 50,
                searching: true,
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: {
                    url: "{{ route('sop.get-data') }}",
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
                        data: 'kode_sop',
                        name: 'kode_sop'
                    },
                    {
                        data: 'judul_sop',
                        name: 'judul_sop'
                    },
                    {
                        data: 'jabatan_nama',
                        name: 'jabatan.nama_jabatan'
                    },
                    {
                        data: 'versi',
                        name: 'versi'
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'file_badge',
                        name: 'dokumen_file',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal_berlaku',
                        name: 'tanggal_berlaku',
                        render: function(data, type, row) {
                            if (data) {
                                return new Date(data).toLocaleDateString('id-ID', {
                                    day: '2-digit',
                                    month: 'short',
                                    year: 'numeric'
                                });
                            }
                            return '-';
                        }
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
            let url = "/sop/" + id;

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