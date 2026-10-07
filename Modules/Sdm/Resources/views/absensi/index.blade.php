@extends('template.root')
@section('page-name', 'Absensi')
@section('title-page', 'List Absensi')
@section('style')
    <style>
        .fab-absensi {
            position: fixed;
            bottom: 24px;
            right: 24px;
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: #fff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .25);
            z-index: 9999;
            text-decoration: none;
            transition: all .3s ease;
        }

        .fab-absensi:hover {
            transform: scale(1.08) rotate(5deg);
            box-shadow: 0 14px 30px rgba(0, 0, 0, .35);
            color: #fff;
        }

        .table-absensi th {
            text-align: center;
            vertical-align: middle;
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .table-absensi td {
            vertical-align: middle;
        }

        .img-thumbnail {
            border-radius: 8px;
            transition: transform 0.2s;
        }

        .img-thumbnail:hover {
            transform: scale(1.1);
        }
    </style>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-absensi" id="table-absensi" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="20%">Nama Karyawan</th>
                            <th width="12%">Tanggal</th>
                            <th width="12%">Masuk</th>
                            <th width="12%">Pulang</th>
                            <th width="15%">Lokasi</th>
                            <th width="15%">Foto</th>
                            <th width="9%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <a href="{{ route('absensi.create') }}" class="fab-absensi" title="Ambil Absensi">
        <i class="fa fa-camera"></i>
    </a>

@section('script')
    <script>
        $(function() {
            $('#table-absensi').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 25,
                ajax: {
                    url: "{{ route('absensi.get-data') }}",
                    data: function(d) {
                        d.url = "{{ request()->segment(1) }}";
                        d.dr_tgl = $('#dr_tgl').val() || "{{ $filter['dr_tgl'] ?? '' }}";
                        d.sp_tgl = $('#sp_tgl').val() || "{{ $filter['sp_tgl'] ?? '' }}";
                    }
                },
                order: [
                    [2, 'desc'] // Order by tanggal desc
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'nama',
                        className: 'fw-bold'
                    },
                    {
                        data: 'tanggal',
                        className: 'text-center'
                    },
                    {
                        data: 'masuk',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'pulang',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'lokasi',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'foto',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'aksi',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                ],
                language: {
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Data tidak ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(difilter dari _MAX_ total data)",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                }
            });

            // Trigger reload on filter change
            $('#dr_tgl, #sp_tgl').on('change', function() {
                $('#table-absensi').DataTable().ajax.reload();
            });
        });

        // HAPUS
        function hapus(id) {
            if (!confirm('Hapus data absensi ini?')) return;

            $.ajax({
                url: '/absensi/' + id,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    $('#table-absensi').DataTable().ajax.reload();

                    // Show success message
                    if (res.message) {
                        // You can use your preferred notification system here
                        alert(res.message);
                    }
                },
                error: function(xhr) {
                    let message = 'Gagal menghapus data';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    alert(message);
                }
            });
        }
    </script>
@endsection
@endsection
