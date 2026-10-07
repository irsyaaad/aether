@extends('template.root')
@section('page-name', 'laporan-absensi')
@section('title-page', 'Laporan Absensi Karyawan')
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal & Waktu</th>
                            <th>Karyawan</th>
                            <th>Tipe</th>
                            <th>Foto</th>
                            <th>Lokasi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                var table = $('#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('laporan-absensi.get-data') }}",
                        data: function(d) {
                            d.dr_tgl = $('#dr_tgl').val();
                            d.sp_tgl = $('#sp_tgl').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'tanggal'
                        },
                        {
                            data: 'karyawan'
                        },
                        {
                            data: 'tipe',
                            className: 'text-center'
                        },
                        {
                            data: 'foto',
                            className: 'text-center',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'lokasi',
                            className: 'text-center',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                $('#btn-filter').click(function() {
                    table.ajax.reload();
                });

                $('#btn-pdf').click(function() {
                    Swal.fire('Info', 'Fitur cetak PDF akan segera hadir', 'info');
                });

                $('#btn-excel').click(function() {
                    Swal.fire('Info', 'Fitur cetak Excel akan segera hadir', 'info');
                });
            });
        </script>
    @endpush
@endsection
