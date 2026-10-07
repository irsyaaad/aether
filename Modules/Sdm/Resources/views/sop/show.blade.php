@extends('template.root')
@section('page-name', 'sop')
@section('title-page', 'Detail SOP')
@section('add-page')
    <a href="{{ route('sop.index') }}" class="btn btn-sm btn-secondary">
        <i class="fa fa-arrow-left"></i> Kembali
    </a>
    <a href="{{ route('sop.edit', $sop->id) }}" class="btn btn-sm btn-warning">
        <i class="fa fa-edit"></i> Edit
    </a>
    @if($sop->dokumen_file)
        <a href="{{ asset('storage/' . $sop->dokumen_file) }}" target="_blank" class="btn btn-sm btn-info">
            <i class="fa fa-download"></i> Download File
        </a>
    @endif
@endsection

@section('content')
    <div class="row">
        <!-- Header Info -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $sop->judul_sop }}</h5>
                        @php
                            $colors = ['draft' => 'secondary', 'active' => 'success', 'inactive' => 'warning', 'archived' => 'dark'];
                        @endphp
                        <span class="badge bg-{{ $colors[$sop->status] ?? 'secondary' }} fs-6">
                            {{ ucfirst($sop->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Kode SOP</strong></td>
                                    <td>: {{ $sop->kode_sop }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jabatan</strong></td>
                                    <td>: 
                                        <a href="{{ route('jabatan.show', $sop->jabatan->id) }}" class="text-decoration-none">
                                            {{ $sop->jabatan->nama_jabatan }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Versi</strong></td>
                                    <td>: {{ $sop->versi }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Berlaku</strong></td>
                                    <td>: {{ $sop->tanggal_berlaku ? $sop->tanggal_berlaku->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Review</strong></td>
                                    <td>: {{ $sop->tanggal_review ? $sop->tanggal_review->format('d M Y') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Dibuat Oleh</strong></td>
                                    <td>: {{ $sop->creator->nm_user ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Dibuat</strong></td>
                                    <td>: {{ $sop->created_at ? $sop->created_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Update</strong></td>
                                    <td>: {{ $sop->updated_at ? $sop->updated_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dokumen File</strong></td>
                                    <td>: 
                                        @if($sop->dokumen_file)
                                            <a href="{{ asset('storage/' . $sop->dokumen_file) }}" target="_blank" class="badge bg-info">
                                                <i class="fa fa-file"></i> Download
                                            </a>
                                        @else
                                            <span class="badge bg-secondary">No File</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deskripsi -->
        @if($sop->deskripsi)
            <div class="col-md-12 mt-3">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Deskripsi</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $sop->deskripsi }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Prosedur -->
        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Prosedur</h6>
                </div>
                <div class="card-body">
                    <div class="sop-content">
                        {!! nl2br(e($sop->prosedur)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Karyawan yang Terkait -->
        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">Karyawan yang Terkait dengan Jabatan Ini</h6>
                </div>
                <div class="card-body">
                    @if($sop->jabatan->karyawan->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Email</th>
                                        <th>Status Kerja</th>
                                        <th>Tanggal Mulai</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sop->jabatan->karyawan as $key => $karyawan)
                                        <tr>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $karyawan->nama_lengkap }}</td>
                                            <td>{{ $karyawan->email ?: '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $karyawan->status_kerja == 'aktif' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($karyawan->status_kerja) }}
                                                </span>
                                            </td>
                                            <td>{{ $karyawan->tanggal_mulai_kerja ? \Carbon\Carbon::parse($karyawan->tanggal_mulai_kerja)->format('d M Y') : '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fa fa-users fa-3x text-muted"></i>
                            <p class="mt-2 text-muted">Belum ada karyawan dengan jabatan {{ $sop->jabatan->nama_jabatan }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <style>
        .sop-content {
            line-height: 1.8;
            font-size: 14px;
        }
        
        .sop-content p {
            margin-bottom: 1rem;
        }
        
        .table-borderless td {
            border: none;
            padding: 0.5rem 0;
        }
    </style>
@endsection