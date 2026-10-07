@extends('template.root')
@section('page-name', 'payroll-component')
@section('title-page', 'List Komponen Payroll')
@section('add-page')
    <a href="{{ route('payroll-component.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i>
        Tambah Komponen</a>
@endsection
@section('content')
    <!-- DataTable -->
    <div class="table-container">
        <table class="table table-bordered" id="payroll-component-table" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Kode</th>
                    <th>Nama Komponen</th>
                    <th>Jenis</th>
                    <th>Tipe</th>
                    <th>Nilai Default</th>
                    <th>Kena Pajak</th>
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
                var table = $('#payroll-component-table').DataTable({
                    pageLength: 50,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('payroll-component.get-data') }}"
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'kode_komponen',
                            name: 'kode_komponen'
                        },
                        {
                            data: 'nama_komponen',
                            name: 'nama_komponen'
                        },
                        {
                            data: 'jenis',
                            name: 'jenis'
                        },
                        {
                            data: 'tipe_perhitungan',
                            name: 'tipe_perhitungan'
                        },
                        {
                            data: 'nilai_default',
                            name: 'nilai_default'
                        },
                        {
                            data: 'is_taxable',
                            name: 'is_taxable'
                        },
                        {
                            data: 'is_active',
                            name: 'is_active',
                            orderable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    order: [
                        [1, 'asc']
                    ],
                    pageLength: 50,
                    responsive: true
                });

                // Toggle status functionality
                $(document).on('change', '.toggle-status', function() {
                    var id = $(this).data('id');
                    var isChecked = $(this).is(':checked');

                    $.ajax({
                        url: "{{ url('payroll-component') }}/" + id + "/toggle-status",
                        type: 'PATCH',
                        data: {
                            "_token": "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                toastr.success(response.message);
                            } else {
                                toastr.error(response.message);
                                // Revert checkbox state
                                $(this).prop('checked', !isChecked);
                            }
                        },
                        error: function(xhr) {
                            toastr.error('Terjadi kesalahan saat mengubah status');
                            // Revert checkbox state
                            $(this).prop('checked', !isChecked);
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
                                url: "{{ url('payroll-component') }}/" + id,
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
