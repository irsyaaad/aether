@extends('template.root')
@section('page-name', 'Perusahaan')
@section('title-page', 'List Perusahaan')
@section('add-page')
    <a href="{{ route('perusahaan.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i>
        Tambah Perusahaan</a>
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table" id="table-perusahaan">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Kota</th>
                    <th>Telp</th>
                    <th>Status</th>
                    <th width="180px">Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <br>
    <script>
        $(document).ready(function() {
            const table = $('#table-perusahaan').DataTable({
                pageLength: 50,
                processing: true,
                serverSide: true,
                ajax: "{{ route('perusahaan.get-data') }}",
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'nama_perusahaan'
                    },
                    {
                        data: 'kota'
                    },
                    {
                        data: 'telp'
                    },
                    {
                        data: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

        });

        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            let url = "{{ route('perusahaan.destroy', ':id') }}".replace(':id', id);

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

                                $('#table-perusahaan').DataTable().ajax.reload(null, false);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
