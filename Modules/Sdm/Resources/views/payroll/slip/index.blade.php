@extends('template.root')
@section('page-name', 'payroll-slip')
@section('title-page', 'List Slip Gaji')
@section('content')
    <!-- Filter Form -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Periode Payroll</label>
                    <select name="period_id" class="form-select">
                        <option value="">Semua Periode</option>
                        @foreach ($periods as $period)
                            <option value="{{ $period->id }}" {{ $filter['period_id'] == $period->id ? 'selected' : '' }}>
                                {{ $period->nama_periode }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ $filter['status'] == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="approved" {{ $filter['status'] == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="paid" {{ $filter['status'] == 'paid' ? 'selected' : '' }}>Paid</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-info me-2">
                        <i class="fa fa-search"></i> Filter
                    </button>
                    <a href="{{ route('payroll-slip.index') }}" class="btn btn-secondary">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- DataTable -->
    <div class="table-container">
        <table class="table table-bordered" id="payroll-slip-table" width="100%">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Slip</th>
                    <th>Karyawan</th>
                    <th>Periode</th>
                    <th>Gaji Bersih</th>
                    <th>Status</th>
                    <th width="50px">Action</th>
                </tr>
            </thead>
        </table>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize DataTable
                var table = $('#payroll-slip-table').DataTable({
                    pageLength: 50,
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: "{{ route('payroll-slip.get-data') }}",
                        data: function(d) {
                            d.period_id = $('select[name="period_id"]').val();
                            d.status = $('select[name="status"]').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nomor_slip',
                            name: 'nomor_slip'
                        },
                        {
                            data: 'karyawan',
                            name: 'karyawan.nama_lengkap'
                        },
                        {
                            data: 'periode',
                            name: 'payrollPeriod.nama_periode'
                        },
                        {
                            data: 'gaji_bersih',
                            name: 'gaji_bersih'
                        },
                        {
                            data: 'status',
                            name: 'status'
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
                    pageLength: 50,
                    responsive: true
                });

                // Approve functionality
                $(document).on('click', '.btn-approve', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');

                    Swal.fire({
                        title: 'Approve Slip Gaji?',
                        text: "Slip gaji akan disetujui dan tidak dapat diedit lagi",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Approve!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('payroll-slip') }}/" + id + "/approve",
                                type: 'PATCH',
                                data: {
                                    "_token": "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    Swal.fire('Berhasil!', 'Slip gaji berhasil disetujui',
                                        'success');
                                    table.ajax.reload();
                                },
                                error: function(xhr) {
                                    Swal.fire('Error!',
                                        'Terjadi kesalahan saat approve slip', 'error');
                                }
                            });
                        }
                    });
                });

                // Mark as paid functionality
                $(document).on('click', '.btn-pay', function(e) {
                    e.preventDefault();
                    var id = $(this).data('id');

                    Swal.fire({
                        title: 'Tandai Sebagai Dibayar?',
                        text: "Slip gaji akan ditandai sebagai sudah dibayar",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, Tandai!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: "{{ url('payroll-slip') }}/" + id + "/mark-as-paid",
                                type: 'PATCH',
                                data: {
                                    "_token": "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    Swal.fire('Berhasil!',
                                        'Slip gaji berhasil ditandai sebagai dibayar',
                                        'success');
                                    table.ajax.reload();
                                },
                                error: function(xhr) {
                                    Swal.fire('Error!',
                                        'Terjadi kesalahan saat menandai sebagai dibayar',
                                        'error');
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
