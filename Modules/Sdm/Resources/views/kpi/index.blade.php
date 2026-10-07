@extends('template.root')

@section('title', 'KPI (Key Performance Indicator)')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h4 class="card-title mb-0">
                                    <i class="fa-solid fa-chart-line text-primary me-2"></i>
                                    KPI (Key Performance Indicator)
                                </h4>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('kpi.create') }}" class="btn btn-primary">
                                    <i class="fa-solid fa-plus me-1"></i>
                                    Tambah KPI
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="kpi-table" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Jabatan</th>
                                        <th>Nama KPI</th>
                                        <th>Bobot</th>
                                        <th>Satuan</th>
                                        <th>Tipe Target</th>
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
            var table = $('#kpi-table').DataTable({
                pageLength: 50,
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('kpi.get-data') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'kode_kpi',
                        name: 'kode_kpi'
                    },
                    {
                        data: 'jabatan_nama',
                        name: 'jabatan.nama_jabatan'
                    },
                    {
                        data: 'nama_kpi',
                        name: 'nama_kpi'
                    },
                    {
                        data: 'bobot_formatted',
                        name: 'bobot'
                    },
                    {
                        data: 'satuan',
                        name: 'satuan'
                    },
                    {
                        data: 'tipe_target',
                        name: 'tipe_target'
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
                    [0, 'desc']
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
                            url: "{{ route('kpi.destroy', ':id') }}".replace(':id', id),
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
