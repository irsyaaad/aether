@extends('template.root')
@section('page-name', 'payroll-period')
@section('title-page', 'List Periode Payroll')
@section('add-page')
    <a href="{{ route('payroll-period.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i>
        Tambah Periode</a>
@endsection
@section('content')
    <!-- DataTable -->
    <div class="table-container">
        <table class="table table-bordered" id="payroll-period-table" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode Periode</th>
                    <th>Nama Periode</th>
                    <th>Tanggal</th>
                    <th>Tanggal Gajian</th>
                    <th>Total Slip</th>
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
                var table = $('#payroll-period-table').DataTable({
                    pageLength: 50,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('payroll-period.get-data') }}",
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
                            data: 'kode_periode',
                            name: 'kode_periode'
                        },
                        {
                            data: 'nama_periode',
                            name: 'nama_periode'
                        },
                        {
                            data: 'tanggal',
                            name: 'tanggal_mulai'
                        },
                        {
                            data: 'tanggal_gajian',
                            name: 'tanggal_gajian'
                        },
                        {
                            data: 'total_slip',
                            name: 'total_slip',
                            orderable: false,
                            searchable: false
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

                // Generate slips functionality
                $(document).on('click', '.btn-generate', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');

                    Swal.fire({
                        title: 'Generate Slip Gaji?',
                        text: "Ini akan membuat slip gaji untuk semua karyawan aktif",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Generate!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('payroll-period') }}/" + id + "/generate-slips",
                                type: 'POST',
                                data: {
                                    "_token": "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    Swal.fire('Berhasil!', 'Slip gaji berhasil digenerate',
                                        'success');
                                    table.ajax.reload();
                                },
                                error: function(xhr) {
                                    Swal.fire('Error!',
                                        'Terjadi kesalahan saat generate slip', 'error');
                                }
                            });
                        }
                    });
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
                                url: "{{ url('payroll-period') }}/" + id,
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
