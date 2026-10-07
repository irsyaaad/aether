@extends('template.root')

@section('title', 'Detail Target KPI')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fa-solid fa-bullseye text-primary me-2"></i>
                                Detail Target KPI
                            </h4>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                <a href="{{ route('kpi-target.edit', $target->id) }}" class="btn btn-warning">
                                    <i class="fa-solid fa-pen me-1"></i>
                                    Edit
                                </a>
                                <a href="{{ route('kpi-target.index') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left me-1"></i>
                                    Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>KPI</strong></td>
                                    <td>: {{ $target->kpi->nama_kpi ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jabatan</strong></td>
                                    <td>: {{ $target->kpi->jabatan->nama_jabatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tahun</strong></td>
                                    <td>: {{ $target->tahun }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Periode</strong></td>
                                    <td>: {{ $target->nama_periode }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Target Value</strong></td>
                                    <td>: {{ number_format($target->target_value, 2) }} {{ $target->kpi->satuan ?? '' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Bobot KPI</strong></td>
                                    <td>: {{ $target->kpi->bobot ?? 0 }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Tipe Target</strong></td>
                                    <td>: {{ ucfirst($target->kpi->tipe_target ?? '') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Oleh</strong></td>
                                    <td>: {{ $target->creator->nm_user ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Pada</strong></td>
                                    <td>: {{ $target->created_at ? $target->created_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Diupdate Pada</strong></td>
                                    <td>: {{ $target->updated_at ? $target->updated_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($target->keterangan)
                    <hr>
                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Keterangan
                        </h5>
                        <div class="bg-light p-3 rounded">
                            {{ $target->keterangan }}
                        </div>
                    </div>
                    @endif

                    @if($target->realisasi->count() > 0)
                    <hr>
                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="fa-solid fa-chart-bar me-2"></i>
                            Realisasi KPI
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Karyawan</th>
                                        <th>Realisasi</th>
                                        <th>Pencapaian</th>
                                        <th>Skor</th>
                                        <th>Status</th>
                                        <th>Tanggal Input</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($target->realisasi as $realisasi)
                                    <tr>
                                        <td>{{ $realisasi->karyawan->nama_lengkap ?? '-' }}</td>
                                        <td>{{ number_format($realisasi->realisasi_value, 2) }} {{ $target->kpi->satuan ?? '' }}</td>
                                        <td>{{ number_format($realisasi->pencapaian_persen ?? 0, 2) }}%</td>
                                        <td>{{ number_format($realisasi->skor ?? 0, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $realisasi->status_badge }}">
                                                {{ ucfirst($realisasi->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $realisasi->tanggal_input ? $realisasi->tanggal_input->format('d M Y') : '-' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
        