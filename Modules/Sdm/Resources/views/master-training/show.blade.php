@extends('template.root')
@section('page-name', 'master-training')
@section('title-page', 'Detail Master Training')
@section('add-page')
    <a href="{{ route('master-training.index') }}" class="btn btn-sm btn-secondary">
        <i class="fa fa-arrow-left"></i> Kembali
    </a>
    <a href="{{ route('master-training.edit', $masterTraining->id) }}" class="btn btn-sm btn-warning">
        <i class="fa fa-edit"></i> Edit
    </a>
@endsection

@section('content')
    <style>
        .training-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 24px;
            color: white;
            margin-bottom: 24px;
        }

        .training-header h2 {
            margin: 0 0 8px 0;
            font-weight: 600;
        }

        .training-header .badge {
            font-size: 0.85rem;
            padding: 6px 12px;
        }

        .info-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            padding: 20px;
            height: 100%;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
        }

        .info-card .icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 12px;
        }

        .info-card .icon-wrapper.blue {
            background: rgba(59, 130, 246, 0.1);
            color: #3b82f6;
        }

        .info-card .icon-wrapper.purple {
            background: rgba(139, 92, 246, 0.1);
            color: #8b5cf6;
        }

        .info-card .icon-wrapper.green {
            background: rgba(34, 197, 94, 0.1);
            color: #22c55e;
        }

        .info-card .icon-wrapper.orange {
            background: rgba(249, 115, 22, 0.1);
            color: #f97316;
        }

        .info-card .label {
            font-size: 0.8rem;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-card .value {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
        }

        .materi-section {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-top: 24px;
        }

        .materi-section .section-header {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            padding: 16px 24px;
            border-bottom: 1px solid #e2e8f0;
        }

        .materi-section .section-header h5 {
            margin: 0;
            font-weight: 600;
            color: #334155;
        }

        .materi-section .section-body {
            padding: 24px;
        }

        .video-container {
            background: #0f172a;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .file-preview-container {
            background: #f8fafc;
            border: 2px dashed #e2e8f0;
            border-radius: 12px;
            padding: 20px;
        }

        .meta-info {
            background: #f8fafc;
            border-radius: 8px;
            padding: 16px;
            margin-top: 24px;
        }

        .meta-info .item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }

        .download-btn:hover {
            transform: translateY(-2px);
        }
    </style>

    {{-- Header Section --}}
    <div class="training-header">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-dark">{{ $masterTraining->kode_training }}</span>
                    @if ($masterTraining->is_active)
                        <span class="badge bg-success"><i class="fa fa-check-circle me-1"></i>Aktif</span>
                    @else
                        <span class="badge bg-secondary"><i class="fa fa-times-circle me-1"></i>Tidak Aktif</span>
                    @endif
                </div>
                <h2>{{ $masterTraining->nama_training }}</h2>
                @if ($masterTraining->deskripsi)
                    <p class="mb-0 opacity-75">{{ $masterTraining->deskripsi }}</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Info Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="info-card">
                <div class="icon-wrapper blue">
                    <i class="fa fa-folder"></i>
                </div>
                <div class="label">Kategori</div>
                <div class="value">{{ $masterTraining->kategori ?? '-' }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-card">
                <div class="icon-wrapper purple">
                    <i class="fa fa-clock"></i>
                </div>
                <div class="label">Durasi</div>
                <div class="value">{{ $masterTraining->durasi_jam ? $masterTraining->durasi_jam . ' Jam' : '-' }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-card">
                <div class="icon-wrapper green">
                    <i class="fa fa-signal"></i>
                </div>
                <div class="label">Level</div>
                <div class="value">{{ $masterTraining->level ?? '-' }}</div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="info-card">
                <div class="icon-wrapper orange">
                    <i class="fa fa-user"></i>
                </div>
                <div class="label">Dibuat Oleh</div>
                <div class="value">{{ $masterTraining->creator->nm_user ?? '-' }}</div>
            </div>
        </div>
    </div>

    {{-- Video YouTube Section --}}
    @if ($masterTraining->link_youtube)
        @php
            $videoId = null;
            $url = $masterTraining->link_youtube;
            if (
                preg_match(
                    '/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/embed\/|youtube\.com\/v\/|youtube\.com\/shorts\/)([a-zA-Z0-9_-]{11})/',
                    $url,
                    $matches,
                )
            ) {
                $videoId = $matches[1];
            } elseif (preg_match('/[?&]v=([a-zA-Z0-9_-]{11})/', $url, $matches)) {
                $videoId = $matches[1];
            }
        @endphp
        <div class="materi-section">
            <div class="section-header d-flex justify-content-between align-items-center">
                <h5><i class="fa-brands fa-youtube text-danger me-2"></i>Video Materi</h5>
                <a href="{{ $masterTraining->link_youtube }}" target="_blank" class="btn btn-sm btn-outline-danger">
                    <i class="fa fa-external-link-alt me-1"></i> Buka di YouTube
                </a>
            </div>
            <div class="section-body">
                @if ($videoId)
                    <div class="video-container">
                        <div class="ratio ratio-16x9">
                            <iframe src="https://www.youtube.com/embed/{{ $videoId }}" title="Video Materi Training"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                        </div>
                    </div>
                @else
                    <div class="alert alert-warning mb-0">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        Format link YouTube tidak dikenali. Silakan klik tombol "Buka di YouTube" untuk melihat video.
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- File Materi Section --}}
    @if ($masterTraining->file_materi)
        @php
            $fileUrl = asset('storage/' . $masterTraining->file_materi);
            $extension = strtolower(pathinfo($masterTraining->file_materi, PATHINFO_EXTENSION));
            $fileName = basename($masterTraining->file_materi);

            $iconMap = [
                'pdf' => 'fa-file-pdf text-danger',
                'doc' => 'fa-file-word text-primary',
                'docx' => 'fa-file-word text-primary',
                'ppt' => 'fa-file-powerpoint text-warning',
                'pptx' => 'fa-file-powerpoint text-warning',
                'xls' => 'fa-file-excel text-success',
                'xlsx' => 'fa-file-excel text-success',
                'mp4' => 'fa-file-video text-info',
                'webm' => 'fa-file-video text-info',
                'zip' => 'fa-file-archive text-secondary',
            ];
            $fileIcon = $iconMap[$extension] ?? 'fa-file text-secondary';
        @endphp
        <div class="materi-section">
            <div class="section-header d-flex justify-content-between align-items-center">
                <h5><i class="fa fa-file-alt text-primary me-2"></i>File Materi</h5>
                <a href="{{ $fileUrl }}" target="_blank" class="btn btn-sm btn-primary download-btn">
                    <i class="fa fa-download"></i> Download
                    <span class="badge bg-white text-primary ms-1">{{ strtoupper($extension) }}</span>
                </a>
            </div>
            <div class="section-body">
                @if (in_array($extension, ['pdf']))
                    {{-- PDF Preview --}}
                    <div class="file-preview-container p-0" style="background: #1a1a1a;">
                        <embed src="{{ $fileUrl }}" type="application/pdf" width="100%" height="600px">
                    </div>
                @elseif(in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                    {{-- Image Preview --}}
                    <div class="file-preview-container text-center">
                        <img src="{{ $fileUrl }}" alt="Preview Materi" class="img-fluid rounded shadow-sm"
                            style="max-height: 500px;">
                    </div>
                @elseif(in_array($extension, ['mp4', 'webm']))
                    {{-- Video Preview --}}
                    <div class="video-container">
                        <video controls class="w-100">
                            <source src="{{ $fileUrl }}" type="video/{{ $extension }}">
                            Browser tidak mendukung video playback.
                        </video>
                    </div>
                @else
                    {{-- Other File Types --}}
                    <div class="file-preview-container text-center">
                        <i class="fa {{ $fileIcon }}" style="font-size: 64px; opacity: 0.5;"></i>
                        <p class="mt-3 mb-0 text-muted">
                            Preview tidak tersedia untuk file <strong>{{ strtoupper($extension) }}</strong>
                            <br><small>Silakan download file untuk melihat isinya</small>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Empty State if no media --}}
    @if (!$masterTraining->file_materi && !$masterTraining->link_youtube)
        <div class="materi-section">
            <div class="section-body">
                <div class="empty-state">
                    <i class="fa fa-folder-open"></i>
                    <h5 class="text-muted">Belum Ada Materi</h5>
                    <p class="mb-0">Training ini belum memiliki file materi atau link video YouTube.</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Meta Information --}}
    <div class="meta-info">
        <div class="row">
            <div class="col-md-6">
                <div class="item">
                    <i class="fa fa-calendar-plus"></i>
                    <span>Dibuat:
                        {{ $masterTraining->created_at ? $masterTraining->created_at->format('d M Y, H:i') : '-' }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="item">
                    <i class="fa fa-calendar-check"></i>
                    <span>Terakhir diupdate:
                        {{ $masterTraining->updated_at ? $masterTraining->updated_at->format('d M Y, H:i') : '-' }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
