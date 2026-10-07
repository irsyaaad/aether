@extends('template.root')

@section('title', 'Detail Job Description')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="card-title mb-0">
                                <i class="fa-solid fa-file-text text-primary me-2"></i>
                                Detail Job Description
                            </h4>
                        </div>
                        <div class="col-auto">
                            <div class="btn-group">
                                <a href="{{ route('job-description.edit', $jobdesc->id) }}" class="btn btn-warning">
                                    <i class="fa-solid fa-pen me-1"></i>
                                    Edit
                                </a>
                                <a href="{{ route('job-description.index') }}" class="btn btn-secondary">
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
                                    <td width="150"><strong>Kode Job Description</strong></td>
                                    <td>: {{ $jobdesc->kode_jobdesc }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Jabatan</strong></td>
                                    <td>: {{ $jobdesc->jabatan->nama_jabatan ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Judul</strong></td>
                                    <td>: {{ $jobdesc->judul_jobdesc }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Versi</strong></td>
                                    <td>: {{ $jobdesc->versi }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>: 
                                        @php
                                            $colors = [
                                                'draft' => 'secondary',
                                                'active' => 'success',
                                                'inactive' => 'warning',
                                                'archived' => 'dark'
                                            ];
                                            $color = $colors[$jobdesc->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ ucfirst($jobdesc->status) }}</span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Tanggal Berlaku</strong></td>
                                    <td>: {{ $jobdesc->tanggal_berlaku ? $jobdesc->tanggal_berlaku->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tanggal Review</strong></td>
                                    <td>: {{ $jobdesc->tanggal_review ? $jobdesc->tanggal_review->format('d M Y') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Oleh</strong></td>
                                    <td>: {{ $jobdesc->creator->nm_user ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dibuat Pada</strong></td>
                                    <td>: {{ $jobdesc->created_at ? $jobdesc->created_at->format('d M Y H:i') : '-' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Dokumen File</strong></td>
                                    <td>: 
                                        @if($jobdesc->dokumen_file)
                                            <a href="{{ asset('storage/' . $jobdesc->dokumen_file) }}" target="_blank" class="badge bg-info">
                                                <i class="fa fa-file"></i> Lihat File
                                            </a>
                                        @else
                                            <span class="badge bg-secondary">No File</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <hr>

                    @if($jobdesc->deskripsi)
                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            Deskripsi
                        </h5>
                        <div class="bg-light p-3 rounded">
                            {{ $jobdesc->deskripsi }}
                        </div>
                    </div>
                    @endif

                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="fa-solid fa-tasks me-2"></i>
                            Tugas Utama
                        </h5>
                        <div class="bg-light p-3 rounded">
                            {!! $jobdesc->tugas_utama !!}
                        </div>
                    </div>

                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="fa-solid fa-shield-alt me-2"></i>
                            Tanggung Jawab
                        </h5>
                        <div class="bg-light p-3 rounded">
                            {!! $jobdesc->tanggung_jawab !!}
                        </div>
                    </div>

                    @if($jobdesc->kualifikasi)
                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="fa-solid fa-graduation-cap me-2"></i>
                            Kualifikasi
                        </h5>
                        <div class="bg-light p-3 rounded">
                            {!! $jobdesc->kualifikasi !!}
                        </div>
                    </div>
                    @endif

                    @if($jobdesc->kompetensi)
                    <div class="mb-4">
                        <h5 class="text-primary">
                            <i class="fa-solid fa-star me-2"></i>
                            Kompetensi
                        </h5>
                        <div class="bg-light p-3 rounded">
                            {!! $jobdesc->kompetensi !!}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection