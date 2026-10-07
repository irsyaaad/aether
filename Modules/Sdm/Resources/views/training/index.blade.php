@extends('template.root')
@section('page-name', 'Training')
@section('title-page', 'List Training')
@section('add-page')
    <a href="{{ route('training.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i>
        Tambah Training</a>
@endsection
@section('content')
    <div class="table-container">
        <table class="table table-bordered" id="training-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Karyawan</th>
                    <th>Materi</th>
                    <th>Trainer</th>
                    <th>Tanggal Selesai</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <br>
    <script>
        $(document).ready(function() {
            $('#training-table').DataTable({
                pageLength: 50,
                processing: true,
                serverSide: true,
                ajax: "{{ route('training.get-data') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'employee',
                        name: 'employee.nama'
                    },
                    {
                        data: 'material',
                        name: 'material.judul'
                    },
                    {
                        data: 'trainer',
                        name: 'trainer.nama'
                    },
                    {
                        data: 'completed_at'
                    },
                    {
                        data: 'status',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'action',
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
    </script>
@endsection
