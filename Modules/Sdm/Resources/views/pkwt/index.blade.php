@extends('template.root')
@section('page-name', 'PKWT')
@section('title-page', 'Perjanjian Kerja Waktu Tertentu')

@section('content')
    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['total'] }}</h4>
                            <p class="mb-0">Total PKWT</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa fa-file-contract fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['aktif'] }}</h4>
                            <p class="mb-0">Kontrak Aktif</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['expiring'] }}</h4>
                            <p class="mb-0">Akan Berakhir</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa fa-exclamation-triangle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $stats['expired'] }}</h4>
                            <p class="mb-0">Sudah Berakhir</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fa fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fa fa-file-contract text-primary"></i> Daftar PKWT
            </h5>
            <a href="{{ route('pkwt.create') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Upload PKWT Baru
            </a>
        </div>
        <div class="card-body">
            {{-- Filter Section --}}
            <div class="row mb-3">
                <div class="col-md-2">
                    <label class="form-label">Jenis Kontrak</label>
                    <select id="filter_jenis" class="form-select form-select-sm">
                        <option value="">Semua Jenis</option>
                        <option value="pkwt">PKWT</option>
                        <option value="pkwtt">PKWTT</option>
                        <option value="magang">Magang</option>
                        <option value="freelance">Freelance</option>
                        <option value="konsultan">Konsultan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select id="filter_status" class="form-select form-select-sm">
                        <option value="">Semua Status</option>
                        <option value="aktif" selected>Aktif</option>
                        <option value="berakhir">Berakhir</option>
                        <option value="diperpanjang">Diperpanjang</option>
                        <option value="dibatalkan">Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Karyawan</label>
                    <input type="text" id="filter_karyawan" class="form-control form-control-sm"
                        placeholder="Nama karyawan">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" id="dr_tgl" class="form-control form-control-sm"
                        value="{{ $filter['dr_tgl'] }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" id="sp_tgl" class="form-control form-control-sm"
                        value="{{ $filter['sp_tgl'] }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="button" id="btn-filter" class="btn btn-primary btn-sm">
                            <i class="fa fa-search"></i> Filter
                        </button>
                    </div>
                </div>
            </div>

            {{-- Data Table --}}
            <div class="table-responsive">
                <table id="pkwt-table" class="table table-bordered table-hover" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Nomor PKWT</th>
                            <th width="15%">Karyawan</th>
                            <th width="20%">Judul PKWT</th>
                            <th width="8%">Jenis</th>
                            <th width="8%">Status</th>
                            <th width="12%">Periode</th>
                            <th width="8%">Sisa Hari</th>
                            <th width="8%">File</th>
                            <th width="4%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#pkwt-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('pkwt.get-data') }}",
                    data: function(d) {
                        d.jenis_kontrak = $('#filter_jenis').val();
                        d.status = $('#filter_status').val();
                        d.dr_tgl = $('#dr_tgl').val();
                        d.sp_tgl = $('#sp_tgl').val();
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'nomor_pkwt',
                        name: 'nomor_pkwt'
                    },
                    {
                        data: 'karyawan_nama',
                        name: 'karyawan_nama'
                    },
                    {
                        data: 'judul_pkwt',
                        name: 'judul_pkwt'
                    },
                    {
                        data: 'jenis_badge',
                        name: 'jenis_kontrak'
                    },
                    {
                        data: 'status_badge',
                        name: 'status_kontrak'
                    },
                    {
                        data: 'periode',
                        name: 'periode',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sisa_hari',
                        name: 'sisa_hari',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'file_info',
                        name: 'file_info',
                        orderable: false,
                        searchable: false
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
                pageLength: 25,
                language: {
                    processing: "Memuat...",
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    },
                    emptyTable: "Tidak ada data PKWT"
                }
            });

            // Filter functionality
            $('#btn-filter').on('click', function() {
                table.ajax.reload();
            });

            // Auto filter on Enter
            $('#filter_karyawan').on('keypress', function(e) {
                if (e.which === 13) {
                    table.ajax.reload();
                }
            });

            // Delete functionality
            window.deletePkwt = function(id) {
                Swal.fire({
                    title: 'Konfirmasi Hapus',
                    text: 'Apakah Anda yakin ingin menghapus PKWT ini? File akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/pkwt/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
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
                                    'Terjadi kesalahan saat menghapus data.', 'error');
                            }
                        });
                    }
                });
            };
        });
    </script>
@endsection

@section('style')
    <style>
        .table th {
            vertical-align: middle;
        }

        .table td {
            vertical-align: middle;
        }

        .badge {
            font-size: 0.75em;
        }

        .card.bg-primary,
        .card.bg-success,
        .card.bg-warning,
        .card.bg-danger {
            border: none;
        }

        .card.bg-primary .card-body,
        .card.bg-success .card-body,
        .card.bg-warning .card-body,
        .card.bg-danger .card-body {
            padding: 1.5rem;
        }
    </style>
@endsection
