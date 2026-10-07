@extends('template.root')
@section('page-name', 'kandidat')
@section('title-page', 'List Kandidat')
@section('add-page')
    <a href="{{ route('kandidat.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i>
        Tambah Kandidat</a>
@endsection
@section('content')
    <div class="table-container">
        <table id="karyawan-table" class="table table-bordered" width="100%">
            <thead>
                <tr>
                    <th>Nama Lengkap</th>
                    <th>NIK KTP</th>
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
                ajax: "{{ route('kandidat.get-data') }}",
                columns: [{
                        data: 'nama_lengkap',
                        name: 'nama_lengkap'
                    },
                    {
                        data: 'nik_ktp',
                        name: 'nik_ktp'
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
            let url = "{{ route('kandidat.destroy', ':id') }}".replace(':id', id);

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
