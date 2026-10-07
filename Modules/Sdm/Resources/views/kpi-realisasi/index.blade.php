@extends('template.root')

@section('title', 'Realisasi KPI')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title mb-0">
                                    <i class="fa-solid fa-chart-bar text-primary me-2"></i>
                                    Realisasi KPI
                                </h4>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('kpi-realisasi.create') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-plus me-1"></i>
                                    Tambah Realisasi KPI
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="kpi-realisasi-table" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>KPI</th>
                                        <th>Karyawan</th>
                                        <th>Periode</th>
                                        <th>Pencapaian</th>
                                        <th>Status</th>
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
            var table = $('#kpi-realisasi-table').DataTable({
                pageLength: 50,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kpi-realisasi.get-data') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'kpi_info',
                        name: 'kpiTarget.kpi.nama_kpi',
                        orderable: false
                    },
                    {
                        data: 'karyawan_nama',
                        name: 'karyawan.nama_lengkap'
                    },
                    {
                        data: 'periode_info',
                        name: 'kpiTarget.tahun'
                    },
                    {
                        data: 'pencapaian_info',
                        name: 'realisasi_value',
                        orderable: false
                    },
                    {
                        data: 'status_badge',
                        name: 'status',
                        orderable: false
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
                    [2, 'desc']
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
                            url: "{{ route('kpi-realisasi.destroy', ':id') }}".replace(
                                ':id', id),
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

            // Approve handler
            $(document).on('click', '.btn-approve', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Konfirmasi Approve',
                    text: 'Apakah Anda yakin ingin menyetujui realisasi ini?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Approve!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kpi-realisasi.approve', ':id') }}".replace(
                                ':id', id),
                            type: 'POST',
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

            // Reject handler
            $(document).on('click', '.btn-reject', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Konfirmasi Reject',
                    text: 'Apakah Anda yakin ingin menolak realisasi ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ffc107',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Reject!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: "{{ route('kpi-realisasi.reject', ':id') }}".replace(':id',
                                id),
                            type: 'POST',
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
