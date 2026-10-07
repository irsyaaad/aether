@extends('template.root')

@section('title', 'Detail KPI')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fa-solid fa-chart-line text-primary me-2"></i>
                                Detail KPI
                            </h4>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                <a href="{{ route('kpi.edit', $kpi->id) }}" class="btn btn-warning">
                                    <i class="fa-solid fa-pen me-1"></i>
                                    Edit
                                </a>
                                <a href="{{ route('kpi.index') }}" class="btn btn-secondary">
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
                                    <td width="150"><strong>Kode KPI</strong></td>
                                    <td>: {{ $kpi->kode_kpi }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jabatan</strong></td>
                                    <td>: {{ $kpi->jabatan->nama_jabatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama KPI</strong></td>
                                    <td>: {{ $kpi->nama_kpi }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Bobot</strong></td>
                                    <td>: {{ $kpi->bobot }}%</td>
                                </tr>
                                <tr>
                                    <td><strong>Satuan</strong></td>
                                    <td>: {{ $kpi->satuan }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Tipe Target</strong></td>
                                    <td>: {{ ucfirst($kpi->tipe_target) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>: 
                                        @php
                                            $color = $kpi->status == 'active' ? 'success' : 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ ucfirst($kpi->status) }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Oleh</strong></td>
                                    <td>: {{ $kpi->creator->nm_user ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Pada</strong></td>
                                    <td>: {{ $kpi->created_at ? $kpi->created_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Diupdate Pada</strong></td>
                                    <td>: {{ $kpi->updated_at ? $kpi->updated_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($kpi->deskripsi)
                    <hr>
                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Deskripsi
                        </h5>
                        <div class="bg-light p-3 rounded">
                            {{ $kpi->deskripsi }}
                        </div>
                    </div>
                    @endif

                    @if($kpi->targets->count() > 0)
                    <hr>
                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="fa-solid fa-bullseye me-2"></i>
                            Target KPI
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Tahun</th>
                                        <th>Periode</th>
                                        <th>Target</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($kpi->targets as $target)
                                    <tr>
                                        <td>{{ $target->tahun }}</td>
                                        <td>{{ $target->nama_periode }}</td>
                                        <td>{{ number_format($target->target_value, 2) }} {{ $kpi->satuan }}</td>
                                        <td>{{ $target->keterangan ?? '-' }}</td>
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