@extends('template.root')
@section('page-name', 'karyawan')
@section('title-page', 'List Karyawan')
@section('add-page')
    <a href="{{ route('karyawan.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i>
        Tambah Karyawan</a>
@endsection
@section('content')
    <div class="table-responsive" style="display: block; overflow-x: auto; white-space: nowrap;">
        <table id="karyawan-table" class="table table-bordered" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>Foto</th>
                    <th>Jenis SIM</th>
                    <th>Jabatan</th>
                    <th>No HP</th>
                    <th>Usia</th>
                    <th>Status</th>
                    <th>User</th>
                    <th width="50px">Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <br>
    <script>
        $(document).ready(function() {
            let table = $('#karyawan-table').DataTable({
                pageLength: 50,
                processing: true,
                serverSide: true,
                ajax: "{{ route('karyawan.get-data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_lengkap',
                        name: 'nama_lengkap'
                    },
                    {
                        data: 'foto_diri',
                        name: 'foto_diri',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'jenis_sim',
                        name: 'jenis_sim'
                    },
                    {
                        data: 'jabatan',
                        name: 'jabatan'
                    },
                    {
                        data: 'nomor_hp',
                        name: 'nomor_hp'
                    },
                    {
                        data: 'usia',
                        name: 'usia',
                        searchable: false,
                        orderable: false
                    },
                    {
                        data: 'status_label',
                        name: 'status',
                        searchable: false,
                        orderable: false
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
        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            let url = "{{ route('karyawan.destroy', ':id') }}".replace(':id', id);

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

                                $('#karyawan-table').DataTable().ajax.reload(null, false);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
