@extends('template.root')
@section('page-name', 'meeting')
@section('title-page', 'Kegiatan Meeting')
@section('add-page')
    <a href="{{ route('meeting.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i>
        Tambah Meeting</a>
@endsection
@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="meeting-table" class="table table-bordered table-striped" width="100%">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Peserta</th>
                            <th>User</th>
                            <th width="50px">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let table = $('#meeting-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('meeting.get-data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'tanggal_meeting',
                        name: 'tanggal_meeting'
                    },
                    {
                        data: 'jenis_meeting',
                        name: 'jenis_meeting'
                    },
                    {
                        data: 'peserta',
                        name: 'peserta'
                    },
                    {
                        data: 'uploader',
                        name: 'uploader.nm_user'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            $(document).on('click', '.btn-delete', function() {
                let id = $(this).data('id');
                let url = "{{ route('meeting.destroy', ':id') }}".replace(':id', id);

                Swal.fire({
                    title: "Yakin hapus?",
                    text: "Data akan dihapus permanen!",
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
                                    table.ajax.reload(null, false);
                                }
                            }
                        });
                    }
                });
            });
        });
    </script>
@endsection
