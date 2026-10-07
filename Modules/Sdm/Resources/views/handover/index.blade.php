@extends('template.root')
@section('page-name', 'Serah Terima')
@section('title-page', 'List Serah Terima')
@section('add-page')
    <a href="{{ route('handover.create') }}" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i>
        Tambah Serah Terima</a>
@endsection
@section('content')
    <div class="table-container">
        <table id="handover-table" class="table table-bordered" width="100%">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Dari</th>
                    <th>Kepada</th>
                    <th>Barang</th>
                    <th>Jumlah</th>
                    <th>Form Fisik</th>
                    <th>Verifikator</th>
                    <th width="50px">Action</th>
                </tr>
            </thead>
        </table>
    </div>
    <br>
    <script>
        $(document).ready(function() {
            let table = $('#handover-table').DataTable({
                pageLength: 50,
                processing: true,
                serverSide: true,
                ajax: "{{ route('handover.get-data') }}",
                columns: [{
                        data: 'handover_date',
                        name: 'handover_date'
                    },
                    {
                        data: 'from_employee',
                        name: 'fromEmployee.name'
                    },
                    {
                        data: 'to_employee',
                        name: 'toEmployee.name'
                    },
                    {
                        data: 'item_name',
                        name: 'item_name'
                    },
                    {
                        data: 'quantity',
                        name: 'quantity'
                    },

                    {
                        data: 'has_physical_form',
                        name: 'has_physical_form',
                        render: function(value) {
                            return value ?
                                '<span class="badge bg-success">Ada</span>' :
                                '<span class="badge bg-secondary">Tidak Ada</span>';
                        },
                        className: 'text-center'
                    },

                    {
                        data: 'verifier',
                        name: 'verifier.name'
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
    </script>
@endsection
