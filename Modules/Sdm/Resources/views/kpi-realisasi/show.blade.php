@extends('template.root')

@section('title', 'Detail Realisasi KPI')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fa-solid fa-chart-bar text-primary me-2"></i>
                                Detail Realisasi KPI
                            </h4>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                @if($realisasi->status === 'draft')
                                    <a href="{{ route('kpi-realisasi.edit', $realisasi->id) }}" class="btn btn-warning">
                                        <i class="fa-solid fa-pen me-1"></i>
                                        Edit
                                    </a>
                                @endif
                                <a href="{{ route('kpi-realisasi.index') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left me-1"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- KPI Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary">
                                <i class="fa-solid fa-chart-line me-2"></i>
                                Informasi KPI
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Nama KPI</strong></td>
                                            <td>: {{ $realisasi->kpiTarget->kpi->nama_kpi ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jabatan</strong></td>
                                            <td>: {{ $realisasi->kpiTarget->kpi->jabatan->nama_jabatan ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Bobot KPI</strong></td>
                                            <td>: {{ $realisasi->kpiTarget->kpi->bobot ?? 0 }}%</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Periode</strong></td>
                                            <td>: {{ $realisasi->kpiTarget->tahun ?? '' }} - {{ $realisasi->kpiTarget->nama_periode ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Target</strong></td>
                                            <td>: {{ number_format($realisasi->kpiTarget->target_value ?? 0, 2) }} {{ $realisasi->kpiTarget->kpi->satuan ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Satuan</strong></td>
                                            <td>: {{ $realisasi->kpiTarget->kpi->satuan ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <!-- Realisasi Information -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary">
                                <i class="fa-solid fa-user me-2"></i>
                                Informasi Realisasi
                            </h5>
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Karyawan</strong></td>
                                            <td>: {{ $realisasi->karyawan->nama_lengkap ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Realisasi Value</strong></td>
                                            <td>: {{ number_format($realisasi->realisasi_value, 2) }} {{ $realisasi->kpiTarget->kpi->satuan ?? '' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Pencapaian</strong></td>
                                            <td>: {{ number_format($realisasi->pencapaian_persen ?? 0, 2) }}%</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Skor</strong></td>
                                            <td>: {{ number_format($realisasi->skor ?? 0, 2) }}</td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="150"><strong>Status</strong></td>
                                            <td>: <span class="badge bg-{{ $realisasi->status_badge }}">{{ ucfirst($realisasi->status) }}</span></td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Input</strong></td>
                                            <td>: {{ $realisasi->tanggal_input ? $realisasi->tanggal_input->format('d M Y') : '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dibuat Oleh</strong></td>
                                            <td>: {{ $realisasi->creator->nm_user ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Dibuat Pada</strong></td>
                                            <td>: {{ $realisasi->created_at ? $realisasi->created_at->format('d M Y H:i') : '-' }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Achievement Visualization -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5 class="text-primary">
                                <i class="fa-solid fa-chart-pie me-2"></i>
                                Visualisasi Pencapaian
                            </h5>
                            <div class="row">
                                <div class="col-md-8">
                                    @php
                                        $pencapaian = $realisasi->pencapaian_persen ?? 0;
                                        $progressColor = 'bg-danger';
                                        if ($pencapaian >= 100) {
                                            $progressColor = 'bg-success';
                                        } elseif ($pencapaian >= 80) {
                                            $progressColor = 'bg-warning';
                                        }
                                    @endphp
                                    <div class="progress mb-3" style="height: 30px;">
                                        <div class="progress-bar {{ $progressColor }}" role="progressbar" 
                                             style="width: {{ min($pencapaian, 100) }}%" 
                                             aria-valuenow="{{ $pencapaian }}" aria-valuemin="0" aria-valuemax="100">
                                            {{ number_format($pencapaian, 1) }}%
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="text-center">
                                        @if($pencapaian >= 100)
                                            <i class="fa-solid fa-check-circle text-success fa-3x"></i>
                                            <p class="text-success mt-2"><strong>Target Tercapai</strong></p>
                                        @elseif($pencapaian >= 80)
                                            <i class="fa-solid fa-exclamation-triangle text-warning fa-3x"></i>
                                            <p class="text-warning mt-2"><strong>Hampir Tercapai</strong></p>
                                        @else
                                            <i class="fa-solid fa-times-circle text-danger fa-3x"></i>
                                            <p class="text-danger mt-2"><strong>Belum Tercapai</strong></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($realisasi->catatan)
                    <hr>
                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="fa-solid fa-sticky-note me-2"></i>
                            Catatan
                        </h5>
                        <div class="bg-light p-3 rounded">
                            {{ $realisasi->catatan }}
                        </div>
                    </div>
                    @endif

                    @if($realisasi->approved_by && $realisasi->approved_at)
                    <hr>
                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="fa-solid fa-user-check me-2"></i>
                            Informasi Approval
                        </h5>
                        <div class="row">
                            <div class="col-md-6">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="150"><strong>Disetujui Oleh</strong></td>
                                        <td>: {{ $realisasi->approver->nm_user ?? '-' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Tanggal Approval</strong></td>
                                        <td>: {{ $realisasi->approved_at ? $realisasi->approved_at->format('d M Y H:i') : '-' }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection