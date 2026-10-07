@extends('template.root')
@section('page-name', 'kartu-piutang')
@section('title-page', 'Kartu Piutang Karyawan')
@section('content')
    <div class="table-container">
        <table class="table table-bordered table-striped" id="kartu-piutang-table" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Karyawan</th>
                    <th>Jabatan</th>
                    <th>Total Piutang</th>
                    <th>Total Bayar</th>
                    <th>Sisa Piutang</th>
                    <th width="120px">Action</th>
                </tr>
            </thead>
        </table>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                $('#kartu-piutang-table').DataTable({
                    pageLength: 50,
                    processing: true,
                    serverSide: true,
                    ajax: "{{ route('kartu-piutang.get-data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama_lengkap',
                            name: 'A.nama_lengkap'
                        },
                        {
                            data: 'jabatan',
                            name: 'A.jabatan'
                        },
                        {
                            data: 'total_piutang',
                            name: 'total_piutang',
                            searchable: false
                        },
                        {
                            data: 'total_bayar',
                            name: 'total_bayar',
                            searchable: false
                        },
                        {
                            data: 'sisa_piutang',
                            name: 'sisa_piutang',
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    pageLength: 50,
                    responsive: true
                });
            });
        </script>
    @endpush
@endsection
