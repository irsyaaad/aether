@extends('template.root')
@section('page-name', 'Laporan Harian')
@section('title-page', 'Laporan Harian Karyawan')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fa fa-clipboard-list text-primary"></i> Data Laporan Harian
            </h5>
            <a href="{{ route('daily-report.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> Buat Laporan Hari Ini
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="table-daily-report" width="100%">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Tanggal</th>
                            <th width="20%">Karyawan</th>
                            <th width="15%">Tipe Role</th>
                            <th width="10%">Jumlah Item</th>
                            <th width="25%">Ringkasan</th>
                            <th width="13%">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

@section('script')
    <script>
        $(function() {
            $('#table-daily-report').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 25,
                ajax: '{{ route('daily-report.get-data') }}',
                order: [
                    [1, 'desc'] // Order by date desc
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'report_date',
                        className: 'text-center'
                    },
                    {
                        data: 'karyawan',
                        className: 'fw-bold'
                    },
                    {
                        data: 'role_type',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'items_count',
                        orderable: false,
                        searchable: false,
                        className: 'text-center'
                    },
                    {
                        data: 'summary',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
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

            // Delete function
            $(document).on('click', '.btn-delete', function() {
                const id = $(this).data('id');
                
                if (!confirm('Hapus laporan ini?')) return;

                $.ajax({
                    url: `/daily-report/${id}`,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        $('#table-daily-report').DataTable().ajax.reload();
                        
                        if (res.status === 'success') {
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
            });
        });
    </script>
@endsection
@endsection