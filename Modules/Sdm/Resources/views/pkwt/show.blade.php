@extends('template.root')
@section('page-name', 'Detail PKWT')
@section('title-page', 'Detail PKWT')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">
                <i class="fa fa-file-contract text-primary"></i> Detail PKWT
            </h5>
            <div class="btn-group">
                <a href="{{ route('pkwt.download', $pkwt->id) }}" class="btn btn-success">
                    <i class="fa fa-download"></i> Download File
                </a>
                <a href="{{ route('pkwt.edit', $pkwt->id) }}" class="btn btn-warning">
                    <i class="fa fa-edit"></i> Edit
                </a>
                <a href="{{ route('pkwt.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            {{-- Status Alert --}}
            @if ($pkwt->is_expired)
                <div class="alert alert-danger">
                    <i class="fa fa-exclamation-triangle"></i>
                    <strong>Kontrak Sudah Berakhir!</strong> Kontrak ini telah berakhir pada
                    {{ $pkwt->tanggal_berakhir->format('d F Y') }}.
                </div>
            @elseif($pkwt->is_expiring_soon)
                <div class="alert alert-warning">
                    <i class="fa fa-clock"></i>
                    <strong>Kontrak Akan Berakhir!</strong> Kontrak ini akan berakhir dalam {{ $pkwt->sisa_hari }} hari
                    ({{ $pkwt->tanggal_berakhir->format('d F Y') }}).
                </div>
            @endif

            <div class="row">
                {{-- Informasi Utama --}}
                <div class="col-md-8">
                    <div class="card border-primary">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0"><i class="fa fa-info-circle"></i> Informasi Kontrak</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Nomor PKWT</strong></td>
                                            <td>: {{ $pkwt->nomor_pkwt }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Judul PKWT</strong></td>
                                            <td>: {{ $pkwt->judul_pkwt }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Karyawan</strong></td>
                                            <td>: {{ $pkwt->karyawan->nama_lengkap ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Jenis Kontrak</strong></td>
                                            <td>: <span
                                                    class="badge {{ $pkwt->getJenisPkwtBadgeClass() }}">{{ strtoupper($pkwt->jenis_kontrak) }}</span>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Status</strong></td>
                                            <td>: <span
                                                    class="badge {{ $pkwt->getStatusBadgeClass() }}">{{ ucfirst($pkwt->status_kontrak) }}</span>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                                <div class="col-md-6">
                                    <table class="table table-borderless">
                                        <tr>
                                            <td width="40%"><strong>Tanggal Mulai</strong></td>
                                            <td>: {{ $pkwt->tanggal_mulai->format('d F Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Tanggal Berakhir</strong></td>
                                            <td>: {{ $pkwt->tanggal_berakhir->format('d F Y') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Durasi Kontrak</strong></td>
                                            <td>: {{ $pkwt->durasi_kontrak }} hari</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sisa Hari</strong></td>
                                            <td>:
                                                @if ($pkwt->status_kontrak === 'aktif')
                                                    @if ($pkwt->sisa_hari <= 0)
                                                        <span class="badge bg-danger">Berakhir</span>
                                                    @elseif($pkwt->sisa_hari <= 30)
                                                        <span class="badge bg-warning">{{ $pkwt->sisa_hari }} hari</span>
                                                    @else
                                                        <span class="badge bg-success">{{ $pkwt->sisa_hari }} hari</span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Auto Reminder</strong></td>
                                            <td>:
                                                @if ($pkwt->auto_reminder)
                                                    <span class="badge bg-success">Ya ({{ $pkwt->reminder_days }} hari
                                                        sebelum berakhir)</span>
                                                @else
                                                    <span class="badge bg-secondary">Tidak</span>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    @if ($pkwt->deskripsi)
                        <div class="card border-info mt-3">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="fa fa-file-text"></i> Deskripsi</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $pkwt->deskripsi }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- Catatan --}}
                    @if ($pkwt->catatan)
                        <div class="card border-warning mt-3">
                            <div class="card-header bg-warning text-dark">
                                <h6 class="mb-0"><i class="fa fa-sticky-note"></i> Catatan</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $pkwt->catatan }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="col-md-4">
                    {{-- File Information --}}
                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0"><i class="fa fa-file"></i> Informasi File</h6>
                        </div>
                        <div class="card-body text-center">
                            <i class="fa fa-file-pdf fa-4x text-danger mb-3"></i>
                            <h6>{{ $pkwt->nama_file }}</h6>
                            <p class="text-muted mb-2">
                                <strong>Ukuran:</strong> {{ $pkwt->formatted_size }}<br>
                                <strong>Format:</strong> {{ strtoupper($pkwt->format_file) }}
                            </p>
                            <a href="{{ route('pkwt.download', $pkwt->id) }}" class="btn btn-success btn-sm">
                                <i class="fa fa-download"></i> Download
                            </a>
                        </div>
                    </div>

                    {{-- Financial & Additional Information --}}
                    @if ($pkwt->gaji_pokok || $pkwt->jabatan || $pkwt->ketentuan_khusus)
                        <div class="card border-secondary mt-3">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0"><i class="fa fa-money-bill"></i> Informasi Finansial</h6>
                            </div>
                            <div class="card-body">
                                @if ($pkwt->gaji_pokok)
                                    <div class="mb-2">
                                        <strong>Gaji Pokok:</strong><br>
                                        <span class="text-success">{{ $pkwt->formatted_gaji }}</span>
                                    </div>
                                @endif

                                @if ($pkwt->jabatan)
                                    <div class="mb-2">
                                        <strong>Jabatan:</strong><br>
                                        {{ $pkwt->jabatan }}
                                    </div>
                                @endif

                                @if ($pkwt->ketentuan_khusus)
                                    <div class="mb-2">
                                        <strong>Ketentuan Khusus:</strong><br>
                                        <div class="text-muted">{{ $pkwt->ketentuan_khusus }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Upload Information --}}
                    <div class="card border-dark mt-3">
                        <div class="card-header bg-dark text-white">
                            <h6 class="mb-0"><i class="fa fa-info"></i> Informasi Upload</h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-borderless table-sm">
                                <tr>
                                    <td><strong>Diupload oleh</strong></td>
                                    <td>: {{ $pkwt->uploader->nm_user ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Upload</strong></td>
                                    <td>: {{ $pkwt->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @if ($pkwt->updated_at != $pkwt->created_at)
                                    <tr>
                                        <td><strong>Terakhir Update</strong></td>
                                        <td>: {{ $pkwt->updated_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Diupdate oleh</strong></td>
                                        <td>: {{ $pkwt->updater->nm_user ?? '-' }}</td>
                                    </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
        .table-borderless td {
            padding: 0.25rem 0.5rem;
            border: none;
        }

        .badge {
            font-size: 0.75em;
        }

        .card-header h6 {
            margin: 0;
        }

        .alert {
            border-left: 4px solid;
        }

        .alert-danger {
            border-left-color: #dc3545;
        }

        .alert-warning {
            border-left-color: #ffc107;
        }
    </style>
@endsection
