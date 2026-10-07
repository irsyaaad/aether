@extends('template.root')
@section('page-name', 'izin')
@section('title-page', 'List Izin')
@section('add-page')
    <a href="{{ route('izin.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i>
        Tambah Izin</a>
@endsection
@section('content')
    <!-- DataTable -->
    <div class="table-container">
        <table class="table table-bordered" id="izin-table" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Izin</th>
                    <th>Karyawan</th>
                    <th>Tanggal</th>
                    <th>Jumlah Hari</th>
                    <th>Jenis Izin</th>
                    <th>Status</th>
                    <th width="50px">Action</th>
                </tr>
            </thead>
        </table>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize DataTable
                var table = $('#izin-table').DataTable({
                    pageLength: 50,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('izin.get-data') }}",
                        data: function(d) {
                            d.dr_tgl = $('input[name="dr_tgl"]').val();
                            d.sp_tgl = $('input[name="sp_tgl"]').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'kode_izin',
                            name: 'kode_izin'
                        },
                        {
                            data: 'karyawan',
                            name: 'karyawan.nama_lengkap'
                        },
                        {
                            data: 'tanggal',
                            name: 'tanggal_mulai'
                        },
                        {
                            data: 'jumlah_hari',
                            name: 'jumlah_hari'
                        },
                        {
                            data: 'jenis_izin',
                            name: 'jenis_izin'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [1, 'desc']
                    ],
                    pageLength: 50,
                    responsive: true
                });

                // Delete functionality
                $(document).on('click', '.btn-delete', function() {
                    var id = $(this).data('id');

                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('izin') }}/" + id,
                                type: 'DELETE',
                                data: {
                                    "_token": "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    if (response.status === 'success') {
                                        Swal.fire('Berhasil!', response.message, 'success');
                                        table.ajax.reload();
                                    } else {
                                        Swal.fire('Error!', response.message, 'error');
                                    }
                                },
                                error: function(xhr) {
                                    Swal.fire('Error!',
                                        'Terjadi kesalahan saat menghapus data', 'error'
                                        );
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
