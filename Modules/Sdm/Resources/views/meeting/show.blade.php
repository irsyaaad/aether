@extends('template.root')
@section('page-name', 'meeting')
@section('title-page', 'Detail Meeting')

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="fa fa-info-circle"></i> Informasi Meeting</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td width="40%"><strong>Tanggal:</strong></td>
                            <td>{{ $meeting->tanggal_meeting->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Jenis:</strong></td>
                            <td>
                                <span class="badge {{ $meeting->getTypeBadgeClass() }}">
                                    {{ $meeting->jenis_meeting }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat Oleh:</strong></td>
                            <td>{{ $meeting->uploader->nm_user ?? '-' }}</td>
                        </tr>
                        <tr>
                            <td><strong>Dibuat Pada:</strong></td>
                            <td>{{ $meeting->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>

                    <hr>
                    <h6><i class="fa fa-users"></i> Peserta</h6>
                    <ul class="list-group list-group-flush">
                        @foreach ((array) $meeting->peserta as $p)
                            <li class="list-group-item p-1"><i class="fa fa-check-circle text-success small me-1"></i>
                                {{ $p }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="{{ route('meeting.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Kembali
                    </a>
                    <a href="{{ route('meeting.edit', $meeting->id) }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fa fa-align-left"></i> Hasil Meeting</h6>
                </div>
                <div class="card-body">
                    <div>{!! $meeting->hasil_meeting !!}</div>
                </div>
            </div>

            <div class="card border-success">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fa fa-camera"></i> Dokumentasi Foto</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        @forelse ($meeting->photo_urls as $url)
                            <div class="col-md-4 col-6">
                                <a href="{{ $url }}" target="_blank">
                                    <img src="{{ $url }}" class="img-fluid rounded shadow-sm border"
                                        style="height: 200px; width: 100%; object-fit: cover;">
                                </a>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5 text-muted">
                                <i class="fa fa-image fa-3x mb-2"></i>
                                <p>Tidak ada dokumentasi foto.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('style')
    <style>
        .table-borderless td {
            border: none;
            padding: 0.5rem 0;
        }

        .list-group-item {
            background: transparent;
            border: none;
        }
    </style>
@endsection
