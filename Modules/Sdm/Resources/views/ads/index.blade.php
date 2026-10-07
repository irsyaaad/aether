@extends('template.root')
@section('page-name', 'Iklan')
@section('title-page', 'List Iklan')
@section('add-page')
    <a href="{{ route('ads.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i>
        Tambah Iklan</a>
@endsection
@section('content')
    <div class="table-container">
        <table class="table table-bordered" id="ads-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Foto</th>
                    <th>Judul</th>
                    <th>Status</th>
                    <th>Mulai</th>
                    <th>Selesai</th>
                    <th>History</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <br>
    <script>
        $(document).ready(function() {
            $('#ads-table').DataTable({
                processing: true,
                serverSide: true,
                pageLength: 50,
                ajax: "{{ route('ads.get-data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'image',
                        name: 'image',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'status',
                        name: 'status',
                        orderable: false
                    },
                    {
                        data: 'start_date',
                        name: 'start_date'
                    },
                    {
                        data: 'end_date',
                        name: 'end_date'
                    },
                    {
                        data: 'history',
                        name: 'history',
                        searchable: false
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });

            function dateIndo(dateStr) {
                if (!dateStr) return '-';
                const date = new Date(dateStr);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: 'long',
                    year: 'numeric'
                });
            }

            function ucwords(str) {
                if (!str) return '-';
                return str.toLowerCase().replace(/\b\w/g, c => c.toUpperCase());
            }

        });

        $(document).on('click', '.btn-delete', function() {
            let id = $(this).data('id');
            let url = "{{ route('ads.destroy', ':id') }}".replace(':id', id);

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

                                $('#ads-table').DataTable().ajax.reload(null, false);
                            }
                        }
                    });
                }
            });
        });
    </script>
@endsection
