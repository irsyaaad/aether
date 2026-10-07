@extends('template.root')
@section('page-name', 'payroll-component')
@section('title-page', 'Detail Komponen Payroll')
@section('content')
    <div class="col-md-12">
        <div class="row">
            <!-- Informasi Komponen -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">Informasi Komponen - {{ $component->kode_komponen }}</h6>
                            <div>
                                <a href="{{ route('payroll-component.edit', $component->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fa fa-edit"></i> Edit
                                </a>
                                <a href="{{ route('payroll-component.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Kode Komponen:</strong></div>
                            <div class="col-md-8">{{ $component->kode_komponen }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Nama Komponen:</strong></div>
                            <div class="col-md-8">{{ $component->nama_komponen }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Jenis:</strong></div>
                            <div class="col-md-8">{!! $component->jenis_badge !!}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Tipe Perhitungan:</strong></div>
                            <div class="col-md-8">{{ $component->tipe_perhitungan_label }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Nilai Default:</strong></div>
                            <div class="col-md-8">
                                @if($component->tipe_perhitungan === 'percentage')
                                    {{ number_format($component->nilai_default, 2) }}%
                                @else
                                    Rp {{ number_format($component->nilai_default, 0, ',', '.') }}
                                @endif
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Kena Pajak:</strong></div>
                            <div class="col-md-8">
                                {!! $component->is_taxable ? '<span class="badge bg-success">Ya</span>' : '<span class="badge bg-secondary">Tidak</span>' !!}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4"><strong>Status:</strong></div>
                            <div class="col-md-8">
                                {!! $component->is_active ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Tidak Aktif</span>' !!}
                            </div>
                        </div>
                        @if($component->keterangan)
                            <div class="row mb-3">
                                <div class="col-md-4"><strong>Keterangan:</strong></div>
                                <div class="col-md-8">{{ $component->keterangan }}</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistik Penggunaan -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Statistik Penggunaan</h6>
                    </div>
                    <div class="card-body">
                        @php
                            $totalUsage = $component->payrollSlipDetails->count();
                            $totalAmount = $component->payrollSlipDetails->sum('nilai');
                            $uniqueEmployees = $component->payrollSlipDetails->pluck('payroll_slip.karyawan_id')->unique()->count();
                        @endphp
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Total Penggunaan:</span>
                                <strong>{{ $totalUsage }} kali</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Karyawan Unik:</span>
                                <strong>{{ $uniqueEmployees }} orang</strong>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>Total Nominal:</span>
                                <strong class="text-{{ $component->jenis === 'allowance' ? 'success' : 'danger' }}">
                                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                                </strong>
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
                            {{ $component->creator->nm_user ?? '-' }}
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Tanggal dibuat:</small><br>
                            {{ $component->created_at->format('d F Y H:i') }}
                        </div>
                        @if($component->updated_at != $component->created_at)
                            <div class="mb-2">
                                <small class="text-muted">Terakhir diupdate:</small><br>
                                {{ $component->updated_at->format('d F Y H:i') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Riwayat Penggunaan -->
        @if($component->payrollSlipDetails->count() > 0)
            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="mb-0">Riwayat Penggunaan ({{ $component->payrollSlipDetails->count() }} penggunaan)</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nomor Slip</th>
                                    <th>Karyawan</th>
                                    <th>Periode</th>
                                    <th>Nilai</th>
                                    <th>Keterangan</th>
                                    <th>Tanggal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($component->payrollSlipDetails->take(20) as $index => $detail)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <a href="{{ route('payroll-slip.show', $detail->payrollSlip->id) }}" class="text-primary">
                                                {{ $detail->payrollSlip->nomor_slip }}
                                            </a>
                                        </td>
                                        <td>{{ $detail->payrollSlip->karyawan->nama_lengkap ?? '-' }}</td>
                                        <td>{{ $detail->payrollSlip->payrollPeriod->nama_periode ?? '-' }}</td>
                                        <td>
                                            <span class="text-{{ $component->jenis === 'allowance' ? 'success' : 'danger' }}">
                                                Rp {{ number_format($detail->nilai, 0, ',', '.') }}
                                            </span>
                                        </td>
                                        <td>{{ $detail->keterangan ?? '-' }}</td>
                                        <td>{{ $detail->created_at->format('d M Y') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($component->payrollSlipDetails->count() > 20)
                            <div class="text-center mt-3">
                                <small class="text-muted">Menampilkan 20 dari {{ $component->payrollSlipDetails->count() }} penggunaan</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection