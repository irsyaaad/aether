@extends('template.root')
@section('page-name', 'jabatan')
@section('title-page', 'Detail Jabatan')
@section('add-page')
    <a href="{{ route('jabatan.index') }}" class="btn btn-sm btn-secondary">
        <i class="fa fa-arrow-left"></i> Kembali
    </a>
    <a href="{{ route('jabatan.edit', $jabatan->id) }}" class="btn btn-sm btn-warning">
        <i class="fa fa-edit"></i> Edit
    </a>
@endsection

@section('content')
    <div class="row">
        <!-- Detail Jabatan -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Informasi Jabatan</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Kode Jabatan</strong></td>
                                    <td>: {{ $jabatan->kode_jabatan }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nama Jabatan</strong></td>
                                    <td>: {{ $jabatan->nama_jabatan }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Level</strong></td>
                                    <td>: {{ $jabatan->level ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Departemen</strong></td>
                                    <td>: {{ $jabatan->departemen ?: '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Status</strong></td>
                                    <td>: 
                                        <span class="badge bg-{{ $jabatan->is_active ? 'success' : 'secondary' }}">
                                            {{ $jabatan->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Oleh</strong></td>
                                    <td>: {{ $jabatan->creator->nm_user ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Dibuat</strong></td>
                                    <td>: {{ $jabatan->created_at ? $jabatan->created_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Terakhir Update</strong></td>
                                    <td>: {{ $jabatan->updated_at ? $jabatan->updated_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($jabatan->deskripsi)
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <strong>Deskripsi:</strong>
                                <p class="mt-2">{{ $jabatan->deskripsi }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistik -->
        <div class="col-md-12 mt-3">
            <div class="row">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $jabatan->karyawan->count() }}</h4>
                                    <p class="mb-0">Karyawan</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa fa-users fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $jabatan->sop->count() }}</h4>
                                    <p class="mb-0">SOP</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa fa-file-text fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $jabatan->jobDescription->count() }}</h4>
                                    <p class="mb-0">Job Description</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa fa-clipboard fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4>{{ $jabatan->kpi->count() }}</h4>
                                    <p class="mb-0">KPI</p>
                                </div>
                                <div class="align-self-center">
                                    <i class="fa fa-chart-line fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs untuk detail -->
        <div class="col-md-12 mt-3">
            <div class="card">
                <div class="card-body">
                    <ul class="nav nav-tabs" id="jabatanTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="karyawan-tab" data-bs-toggle="tab" data-bs-target="#karyawan" type="button" role="tab">
                                <i class="fa fa-users"></i> Karyawan ({{ $jabatan->karyawan->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sop-tab" data-bs-toggle="tab" data-bs-target="#sop" type="button" role="tab">
                                <i class="fa fa-file-text"></i> SOP ({{ $jabatan->sop->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="jobdesc-tab" data-bs-toggle="tab" data-bs-target="#jobdesc" type="button" role="tab">
                                <i class="fa fa-clipboard"></i> Job Description ({{ $jabatan->jobDescription->count() }})
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="kpi-tab" data-bs-toggle="tab" data-bs-target="#kpi" type="button" role="tab">
                                <i class="fa fa-chart-line"></i> KPI ({{ $jabatan->kpi->count() }})
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content mt-3" id="jabatanTabsContent">
                        <!-- Tab Karyawan -->
                        <div class="tab-pane fade show active" id="karyawan" role="tabpanel">
                            @if($jabatan->karyawan->count() > 0)
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
                                            @foreach($jabatan->karyawan as $key => $karyawan)
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
                                    <p class="mt-2 text-muted">Belum ada karyawan dengan jabatan ini</p>
                                </div>
                            @endif
                        </div>

                        <!-- Tab SOP -->
                        <div class="tab-pane fade" id="sop" role="tabpanel">
                            <div class="d-flex justify-content-end mb-3">
                                <a href="{{ route('sop.create') }}?jabatan_id={{ $jabatan->id }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i> Tambah SOP
                                </a>
                            </div>
                            @if($jabatan->sop->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode SOP</th>
                                                <th>Judul</th>
                                                <th>Versi</th>
                                                <th>Status</th>
                                                <th>Tanggal Berlaku</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($jabatan->sop as $key => $sop)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $sop->kode_sop }}</td>
                                                    <td>{{ $sop->judul_sop }}</td>
                                                    <td>{{ $sop->versi }}</td>
                                                    <td>
                                                        @php
                                                            $colors = ['draft' => 'secondary', 'active' => 'success', 'inactive' => 'warning', 'archived' => 'dark'];
                                                        @endphp
                                                        <span class="badge bg-{{ $colors[$sop->status] ?? 'secondary' }}">
                                                            {{ ucfirst($sop->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $sop->tanggal_berlaku ? $sop->tanggal_berlaku->format('d M Y') : '-' }}</td>
                                                    <td>
                                                        <a href="{{ route('sop.show', $sop->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fa fa-file-text fa-3x text-muted"></i>
                                    <p class="mt-2 text-muted">Belum ada SOP untuk jabatan ini</p>
                                </div>
                            @endif
                        </div>

                        <!-- Tab Job Description -->
                        <div class="tab-pane fade" id="jobdesc" role="tabpanel">
                            <div class="d-flex justify-content-end mb-3">
                                <a href="{{ route('job-description.create') }}?jabatan_id={{ $jabatan->id }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i> Tambah Job Description
                                </a>
                            </div>
                            @if($jabatan->jobDescription->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode</th>
                                                <th>Judul</th>
                                                <th>Versi</th>
                                                <th>Status</th>
                                                <th>Tanggal Berlaku</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($jabatan->jobDescription as $key => $jobdesc)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $jobdesc->kode_jobdesc }}</td>
                                                    <td>{{ $jobdesc->judul_jobdesc }}</td>
                                                    <td>{{ $jobdesc->versi }}</td>
                                                    <td>
                                                        @php
                                                            $colors = ['draft' => 'secondary', 'active' => 'success', 'inactive' => 'warning', 'archived' => 'dark'];
                                                        @endphp
                                                        <span class="badge bg-{{ $colors[$jobdesc->status] ?? 'secondary' }}">
                                                            {{ ucfirst($jobdesc->status) }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $jobdesc->tanggal_berlaku ? $jobdesc->tanggal_berlaku->format('d M Y') : '-' }}</td>
                                                    <td>
                                                        <a href="{{ route('job-description.show', $jobdesc->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fa fa-clipboard fa-3x text-muted"></i>
                                    <p class="mt-2 text-muted">Belum ada Job Description untuk jabatan ini</p>
                                </div>
                            @endif
                        </div>

                        <!-- Tab KPI -->
                        <div class="tab-pane fade" id="kpi" role="tabpanel">
                            <div class="d-flex justify-content-end mb-3">
                                <a href="{{ route('kpi.create') }}?jabatan_id={{ $jabatan->id }}" class="btn btn-sm btn-primary">
                                    <i class="fa fa-plus"></i> Tambah KPI
                                </a>
                            </div>
                            @if($jabatan->kpi->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>No</th>
                                                <th>Kode KPI</th>
                                                <th>Nama KPI</th>
                                                <th>Bobot (%)</th>
                                                <th>Satuan</th>
                                                <th>Tipe Target</th>
                                                <th>Status</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($jabatan->kpi as $key => $kpi)
                                                <tr>
                                                    <td>{{ $key + 1 }}</td>
                                                    <td>{{ $kpi->kode_kpi }}</td>
                                                    <td>{{ $kpi->nama_kpi }}</td>
                                                    <td>{{ $kpi->bobot }}%</td>
                                                    <td>{{ $kpi->satuan }}</td>
                                                    <td>{{ ucfirst($kpi->tipe_target) }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $kpi->status == 'active' ? 'success' : 'secondary' }}">
                                                            {{ ucfirst($kpi->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <a href="{{ route('kpi.show', $kpi->id) }}" class="btn btn-sm btn-info">
                                                            <i class="fa fa-eye"></i>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fa fa-chart-line fa-3x text-muted"></i>
                                    <p class="mt-2 text-muted">Belum ada KPI untuk jabatan ini</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection