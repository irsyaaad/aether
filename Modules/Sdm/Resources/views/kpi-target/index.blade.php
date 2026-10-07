@extends('template.root')

@section('title', 'Target KPI')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title mb-0">
                                    <i class="fa-solid fa-bullseye text-primary me-2"></i>
                                    Target KPI
                                </h4>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('kpi-target.create') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-plus me-1"></i>
                                    Tambah Target KPI
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="kpi-target-table" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>KPI</th>
                                        <th>Periode</th>
                                        <th>Target</th>
                                        <th>Keterangan</th>
                                        <th>User</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#kpi-target-table').DataTable({
                pageLength: 50,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kpi-target.get-data') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'kpi_info',
                        name: 'kpi.nama_kpi',
                        orderable: false
                    },
                    {
                        data: 'periode_info',
                        name: 'tahun'
                    },
                    {
                        data: 'target_formatted',
                        name: 'target_value'
                    },
                    {
                        data: 'keterangan',
                        name: 'keterangan'
                    },
                    {
                        data: 'dibuat_oleh',
                        name: 'created_at',
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
                    [1, 'desc']
                ],
                pageLength: 50,
                responsive: true,
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                }
            });

            // Delete handler
            $(document).on('click', '.btn-delete', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus data ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kpi-target.destroy', ':id') }}".replace(':id',
                                id),
                            type: 'DELETE',
                            data: {
                                _token: "{{ csrf_token() }}"
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
                                var response = xhr.responseJSON;
                                Swal.fire('Error!', response.message ||
                                    'Terjadi kesalahan', 'error');
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
