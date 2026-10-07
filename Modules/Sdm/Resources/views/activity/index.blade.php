@extends('template.root')
@section('page-name', 'Aktifitas Karyawan')
@section('title-page', 'List Aktifitas Karyawan')
@section('add-page')
    <a href="{{ route('employee-activities.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i>
        Tambah Aktifitas</a>
@endsection
@section('content')
    <div class="table-responsive">
        <table class="table table-bordered" id="table-aktivitas" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Tanggal</th>
                    <th>Karyawan</th>
                    <th>Jam</th>
                    <th>Kegiatan</th>
                    <th>Summary</th>
                    <th>Foto</th>
                    <th>Aksi</th>
                </tr>
            </thead>
        </table>
    </div>
@section('script')
    <script>
        $(function() {
            $('#table-aktivitas').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: '{{ route('employee-activities.get-data') }}',
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'activity_date'
                    },
                    {
                        data: 'karyawan'
                    },
                    {
                        data: 'jam'
                    },
                    {
                        data: 'activity'
                    },
                    {
                        data: 'summary',
                        orderable: false
                    },
                    {
                        data: 'foto',
                        orderable: false
                    },
                    {
                        data: 'action',
                        orderable: false
                    },
                ]
            });
        });

        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            let url = "{{ route('employee-activities.destroy', ':id') }}".replace(':id', id);

            Swal.fire({
                title: "Yakin hapus?",
                text: "Data akan hilang permanen!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
                customClass: {
                    confirmButton: 'btn btn-danger me-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: true
            }).then((result) => {
                if (result.isConfirmed) {

                    $.ajax({
                        url: url,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {
                            if (res.status === "success") {
                                Swal.fire({
                                    title: "Terhapus!",
                                    text: res.message,
                                    icon: "success",
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                $('#table-aktivitas').DataTable().ajax.reload(null, false);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
@endsection
