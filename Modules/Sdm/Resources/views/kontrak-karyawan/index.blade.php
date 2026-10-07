@extends('template.root')

@section('title-page')
    Kontrak Karyawan
@endsection

@section('add-page')
    <a class="btn btn-primary" href="{{ route('kontrak-karyawan.create') }}">Tambah Kontrak</a>
@endsection

@section('content')
    <div class="table-responsive">
        <table class="table table-bordered" id="kontrak-karyawan-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Jenis Kontrak</th>
                    <th>Files</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
@endsection

@section('script')
    <script>
        $(function() {
            var table = $('#kontrak-karyawan-table').DataTable({
                pageLength: 50,
                processing: true,
                serverSide: true,
                ajax: "{{ route('kontrak-karyawan.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nama_karyawan',
                        name: 'nama_karyawan'
                    },
                    {
                        data: 'tanggal_mulai',
                        name: 'tanggal_mulai'
                    },
                    {
                        data: 'tanggal_selesai',
                        name: 'tanggal_selesai'
                    },
                    {
                        data: 'jenis_kontrak',
                        name: 'jenis_kontrak'
                    },
                    {
                        data: 'files',
                        name: 'files',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            // Delete button handler
            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: 'Data yang dihapus tidak dapat dikembalikan!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then(result => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "/kontrak-karyawan/" + id,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {
                                Swal.fire('Berhasil!', res.message, 'success');
                                $('#kontrak-karyawan-table').DataTable().ajax.reload();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
