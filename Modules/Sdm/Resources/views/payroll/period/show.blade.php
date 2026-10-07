@extends('template.root')
@section('page-name', 'payroll-period')
@section('title-page', 'Detail Periode Payroll')
@section('content')
    <div class="col-md-12">
        <div class="row">
            <!-- Informasi Periode -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Informasi Periode - {{ $period->kode_periode }}</h6>
                            <div>
                                @if($period->canBeEdited())
                                    <a href="{{ route('payroll-period.edit', $period->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                @endif
                                @if($period->canBeProcessed())
                                    <button class="btn btn-info btn-sm btn-generate" data-id="{{ $period->id }}">
                                        <i class="fa fa-cogs"></i> Generate Slip
                                    </button>
                                @endif
                                <a href="{{ route('payroll-period.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Kode Periode:</strong></div>
                            <div class="col-md-8">{{ $period->kode_periode }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Nama Periode:</strong></div>
                            <div class="col-md-8">{{ $period->nama_periode }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Tanggal Mulai:</strong></div>
                            <div class="col-md-8">{{ $period->tanggal_mulai->format('d F Y') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Tanggal Selesai:</strong></div>
                            <div class="col-md-8">{{ $period->tanggal_selesai->format('d F Y') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Tanggal Gajian:</strong></div>
                            <div class="col-md-8">{{ $period->tanggal_gajian->format('d F Y') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Status:</strong></div>
                            <div class="col-md-8">{!! $period->status_badge !!}</div>
                        </div>
                        @if($period->keterangan)
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Keterangan:</strong></div>
                                <div class="col-md-8">{{ $period->keterangan }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistik -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Statistik Slip Gaji</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $totalSlips = $period->payrollSlips->count();
                            $draftSlips = $period->payrollSlips->where('status', 'draft')->count();
                            $approvedSlips = $period->payrollSlips->where('status', 'approved')->count();
                            $paidSlips = $period->payrollSlips->where('status', 'paid')->count();
                            $totalGaji = $period->payrollSlips->sum('gaji_bersih');
                        @endphp
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Total Slip:</span>
                                <strong>{{ $totalSlips }}</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Draft:</span>
                                <span class="badge bg-secondary">{{ $draftSlips }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Approved:</span>
                                <span class="badge bg-success">{{ $approvedSlips }}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Paid:</span>
                                <span class="badge bg-primary">{{ $paidSlips }}</span>
                            </div>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span><strong>Total Gaji:</strong></span>
                                <strong class="text-success">Rp {{ number_format($totalGaji, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="card mt-3">
                    <div class="card-header">
                        <h6 class="mb-0">Informasi Tambahan</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Dibuat oleh:</small><br>
                            {{ $period->creator->nm_user ?? '-' }}
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Tanggal dibuat:</small><br>
                            {{ $period->created_at->format('d F Y H:i') }}
                        </div>
                        @if($period->updated_at != $period->created_at)
                            <div class="mb-2">
                                <small class="text-muted">Terakhir diupdate:</small><br>
                                {{ $period->updated_at->format('d F Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Slip Gaji -->
        @if($period->payrollSlips->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Daftar Slip Gaji ({{ $period->payrollSlips->count() }} slip)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Slip</th>
                                    <th>Karyawan</th>
                                    <th>Gaji Pokok</th>
                                    <th>Gaji Bersih</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($period->payrollSlips as $index => $slip)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <a href="{{ route('payroll-slip.show', $slip->id) }}" class="text-primary">
                                                {{ $slip->nomor_slip }}
                                            </a>
                                        </td>
                                        <td>{{ $slip->karyawan->nama_lengkap ?? '-' }}</td>
                                        <td>Rp {{ number_format($slip->gaji_pokok, 0, ',', '.') }}</td>
                                        <td>Rp {{ number_format($slip->gaji_bersih, 0, ',', '.') }}</td>
                                        <td>{!! $slip->status_badge !!}</td>
                                        <td>
                                            <a href="{{ route('payroll-slip.show', $slip->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            @if($slip->canBeEdited())
                                                <a href="{{ route('payroll-slip.edit', $slip->id) }}" class="btn btn-sm btn-warning">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        $(document).ready(function() {
            // Generate slips functionality
            $('.btn-generate').on('click', function(e) {
                e.preventDefault();
                var id = $(this).data('id');
                
                Swal.fire({
                    title: 'Generate Slip Gaji?',
                    text: "Ini akan membuat slip gaji untuk semua karyawan aktif",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Generate!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Create a form and submit it with POST method
                        var form = $('<form>', {
                            'method': 'POST',
                            'action': "{{ route('payroll-period.generate-slips', $period->id) }}"
                        });
                        
                        // Add CSRF token
                        form.append($('<input>', {
                            'type': 'hidden',
                            'name': '_token',
                            'value': "{{ csrf_token() }}"
                        }));
                        
                        // Append form to body and submit
                        $('body').append(form);
                        form.submit();
                    }
                });
            });
        });
    </script>
    @endpush
@endsection