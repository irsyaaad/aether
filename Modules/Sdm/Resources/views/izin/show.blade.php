@extends('template.root2')
@section('page-name', 'Detail Izin')
@section('content')
    <div class="container-fluid">
        <div class="page-title">
            <div class="row">
                <div class="col-6">
                    <h3>Detail Izin</h3>
                </div>
                <div class="col-6">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}"><i data-feather="home"></i></a></li>
                        <li class="breadcrumb-item">SDM</li>
                        <li class="breadcrumb-item"><a href="{{ route('izin.index') }}">Izin</a></li>
                        <li class="breadcrumb-item active">Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>Detail Izin - {{ $izin->kode_izin }}</h5>
                            </div>
                            <div class="col-md-6 text-end">
                                @if($izin->status === 'pending')
                                    <a href="{{ route('izin.edit', $izin->id) }}" class="btn btn-warning btn-sm">
                                        <i class="fa fa-edit"></i> Edit
                                    </a>
                                @endif
                                <a href="{{ route('izin.index') }}" class="btn btn-secondary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Kembali
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <!-- Informasi Izin -->
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Informasi Izin</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Kode Izin:</strong></div>
                                            <div class="col-md-8">{{ $izin->kode_izin }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Karyawan:</strong></div>
                                            <div class="col-md-8">{{ $izin->karyawan->nama_lengkap ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Jabatan:</strong></div>
                                            <div class="col-md-8">{{ $izin->karyawan->jabatan ?? '-' }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Jenis Izin:</strong></div>
                                            <div class="col-md-8">
                                                <span class="badge bg-info">{{ $izin->jenis_izin_label }}</span>
                                            </div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Tanggal Mulai:</strong></div>
                                            <div class="col-md-8">{{ dateindo($izin->tanggal_mulai) }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Tanggal Selesai:</strong></div>
                                            <div class="col-md-8">{{ dateindo($izin->tanggal_selesai) }}</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Jumlah Hari:</strong></div>
                                            <div class="col-md-8">{{ $izin->jumlah_hari }} hari</div>
                                        </div>
                                        <div class="row mb-3">
                                            <div class="col-md-4"><strong>Alasan:</strong></div>
                                            <div class="col-md-8">{{ $izin->alasan }}</div>
                                        </div>
                                        @if($izin->dokumen_pendukung)
                                            <div class="row mb-3">
                                                <div class="col-md-4"><strong>Dokumen:</strong></div>
                                                <div class="col-md-8">
                                                    <a href="{{ Storage::url($izin->dokumen_pendukung) }}" target="_blank" class="btn btn-sm btn-info">
                                                        <i class="fa fa-eye"></i> Lihat Dokumen
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Status dan Approval -->
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Status Persetujuan</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            {!! $izin->status_badge !!}
                                        </div>

                                        @if($izin->status === 'pending')
                                            <form action="{{ route('izin.update-status', $izin->id) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Status</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="">Pilih Status</option>
                                                        <option value="disetujui">Setujui</option>
                                                        <option value="ditolak">Tolak</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Catatan Atasan</label>
                                                    <textarea name="catatan_atasan" class="form-control" rows="3" placeholder="Catatan (opsional)"></textarea>
                                                </div>
                                                
                                                <button type="submit" class="btn btn-primary btn-sm w-100">
                                                    <i class="fa fa-check"></i> Update Status
                                                </button>
                                            </form>
                                        @else
                                            @if($izin->approver)
                                                <div class="mb-2">
                                                    <small class="text-muted">Disetujui oleh:</small><br>
                                                    <strong>{{ $izin->approver->nm_user }}</strong>
                                                </div>
                                            @endif
                                            
                                            @if($izin->tanggal_disetujui)
                                                <div class="mb-2">
                                                    <small class="text-muted">Tanggal:</small><br>
                                                    {{ dateindo($izin->tanggal_disetujui) }}
                                                </div>
                                            @endif
                                            
                                            @if($izin->catatan_atasan)
                                                <div class="mb-2">
                                                    <small class="text-muted">Catatan:</small><br>
                                                    {{ $izin->catatan_atasan }}
                                                </div>
                                            @endif
                                        @endif
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
                                            {{ $izin->creator->nm_user ?? '-' }}
                                        </div>
                                        <div class="mb-2">
                                            <small class="text-muted">Tanggal dibuat:</small><br>
                                            {{ $izin->created_at->format('d F Y H:i') }}
                                        </div>
                                        @if($izin->updated_at != $izin->created_at)
                                            <div class="mb-2">
                                                <small class="text-muted">Terakhir diupdate:</small><br>
                                                {{ $izin->updated_at->format('d F Y H:i') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection