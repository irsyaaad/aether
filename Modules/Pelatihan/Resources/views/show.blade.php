@extends('layouts.app')

@section('title', ($kelas->judul ?? 'Detail Kelas') . ' - ' . config('app.name'))
@section('page_title', 'Kelas & Workshop Riset')

@section('content')
<div class="space-y-6">

    <!-- Top Breadcrumb & Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:px-6 sm:py-4 rounded-2xl border border-gray-200 shadow-xs">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('pelatihan.kelas-saya') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Kelas Saya</span>
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-xs font-medium text-gray-500 truncate max-w-xs sm:max-w-md">{{ $kelas->judul }}</span>
        </div>

        <div class="flex items-center gap-2">
            @if(!empty($kelas->link_meeting))
                <a href="{{ $kelas->link_meeting }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] shadow-xs transition">
                    <i data-lucide="video" class="w-3.5 h-3.5"></i>
                    <span>Masuk Sesi Live</span>
                </a>
            @endif
            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $kelas->status === 'Dibuka' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-700 border-gray-200' }}">
                {{ $kelas->status ?? 'Aktif' }}
            </span>
        </div>
    </div>

    <!-- Layout 70% (Materi Viewer) & 30% (List Silabus / Modul) -->
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6 items-start">

        <!-- ========================================== -->
        <!-- SISI KIRI (70%): MATERI VIEWER & DESKRIPSI -->
        <!-- ========================================== -->
        <div class="lg:col-span-7 space-y-5">

            <!-- Player & Media Container -->
            <div class="bg-slate-950 rounded-2xl border border-slate-800 shadow-md overflow-hidden relative" id="viewer-container">
                @if(!$activeMateri)
                    <div class="aspect-video w-full flex flex-col items-center justify-center p-8 text-center text-slate-400 bg-slate-900">
                        <i data-lucide="folder-open" class="w-12 h-12 text-slate-600 mb-3"></i>
                        <h3 class="text-base font-bold text-slate-200">Belum Ada Materi Tersedia</h3>
                        <p class="text-xs text-slate-400 max-w-md mt-1">Silabus dan modul untuk kelas ini sedang disiapkan oleh instruktur ahli.</p>
                    </div>
                @else
                    <!-- Video Player Container (Hidden if non-video) -->
                    <div id="video-player-wrapper" class="{{ $activeMateri && $activeMateri->tipe_materi === 'Video' && $activeMateri->link_video ? '' : 'hidden' }}">
                        <div class="aspect-video w-full bg-slate-950 relative flex items-center justify-center overflow-hidden rounded-t-2xl group select-none shadow-md border-x border-t border-slate-900" id="player-box">
                            @php
                                $videoSrc = $activeMateri->link_video ?? '';
                                $isYoutube = false;
                                $ytId = '';
                                if (preg_match('/(youtube\.com\/(watch\?v=|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})/', $videoSrc, $matches)) {
                                    $isYoutube = true;
                                    $ytId = $matches[3];
                                }
                            @endphp

                            <!-- Iframe YouTube Player (controls=0 untuk menghilangkan seluruh kontrol & logo bawaan YouTube) -->
                            <iframe id="video-iframe" 
                                    src="" 
                                    class="w-full h-full border-0 hidden" 
                                    sandbox="allow-scripts allow-same-origin allow-presentation allow-forms"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen>
                            </iframe>

                            <!-- Transparent Screen Click Layer (Klik di layar memicu Toggle Play/Pause kustom) -->
                            <div id="player-click-layer" onclick="togglePlayPause()" class="absolute inset-0 z-10 cursor-pointer hidden" title="Klik untuk Putar / Jeda Video"></div>

                            <!-- Top click-guard overlay untuk mencegah klik judul / channel keluar ke YouTube -->
                            <div id="video-top-guard" class="absolute top-0 inset-x-0 h-12 bg-transparent pointer-events-auto z-15 hidden" title="Aether Video Player"></div>

                            <!-- Bottom-right click-guard overlay untuk menutupi watermark YouTube -->
                            <div id="video-bottom-guard" class="absolute bottom-0 right-0 w-28 h-12 bg-transparent pointer-events-auto z-15 hidden" title="Aether Video Player"></div>

                            <!-- Pause Overlay Kustom (100% Menutupi Video saat di-jeda tanpa logo YouTube) -->
                            <div id="video-pause-overlay" 
                                 onclick="togglePlayPause()" 
                                 class="absolute inset-0 z-20 bg-slate-950/70 backdrop-blur-[2px] flex flex-col items-center justify-center cursor-pointer transition-all duration-300 hidden">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-tr from-[#1768B5] via-[#1b7fd6] to-sky-400 text-white flex items-center justify-center shadow-2xl shadow-sky-500/30 border-2 border-white/40 transform hover:scale-110 transition duration-300 mb-3">
                                    <svg class="w-7 h-7 sm:w-8 sm:h-8 fill-current text-white translate-x-0.5" viewBox="0 0 24 24">
                                        <polygon points="6 3 20 12 6 21 6 3"></polygon>
                                    </svg>
                                </div>
                                <span class="px-3.5 py-1.5 rounded-full bg-black/60 backdrop-blur-md text-white text-xs font-bold border border-white/10 shadow-sm flex items-center gap-1.5">
                                    <i data-lucide="play" class="w-3.5 h-3.5 fill-current text-sky-400"></i>
                                    <span>Video Dijeda • Klik untuk Melanjutkan</span>
                                </span>
                            </div>

                            <!-- Custom Video Cover & Custom Play Button (Pengganti Lambang & Play Button YouTube di Awal Play) -->
                            <div id="video-custom-cover" 
                                 onclick="startCustomPlay()"
                                 class="absolute inset-0 z-30 flex flex-col items-center justify-center cursor-pointer overflow-hidden transition-all duration-300 {{ $isYoutube ? '' : 'hidden' }}">
                                
                                <!-- Background Thumbnail Image -->
                                <img id="custom-cover-img" 
                                     src="{{ $ytId ? 'https://img.youtube.com/vi/' . $ytId . '/hqdefault.jpg' : ($kelas->thumbnail_url ?: asset('images/default-course.jpg')) }}" 
                                     alt="Video Thumbnail" 
                                     class="absolute inset-0 w-full h-full object-cover transform scale-100 group-hover:scale-105 transition duration-700 ease-out filter brightness-[0.6] group-hover:brightness-[0.45]">
                                
                                <!-- Dark Vignette Overlay -->
                                <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/30 to-slate-950/60 pointer-events-none"></div>

                                <!-- Custom Play Button Aether (Bukan Icon YouTube) -->
                                <div class="relative z-10 flex flex-col items-center text-center px-4">
                                    <div class="relative flex items-center justify-center mb-3">
                                        <!-- Ambient Ring Pulse Animation -->
                                        <div class="absolute w-20 h-20 rounded-full bg-[#1768B5]/40 animate-ping opacity-60"></div>
                                        <div class="absolute w-24 h-24 rounded-full bg-sky-400/20 blur-md"></div>
                                        
                                        <!-- Main Custom Play Button Circle (Gradient Blue Aether) -->
                                        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-gradient-to-tr from-[#1768B5] via-[#1b7fd6] to-sky-400 text-white flex items-center justify-center shadow-xl shadow-[#1768B5]/40 border-2 border-white/40 transform group-hover:scale-110 transition duration-300 ease-out">
                                            <!-- Custom Geometry Play Icon (Bukan Icon YouTube) -->
                                            <svg class="w-7 h-7 sm:w-8 sm:h-8 fill-current text-white translate-x-0.5 drop-shadow" viewBox="0 0 24 24">
                                                <polygon points="6 3 20 12 6 21 6 3"></polygon>
                                            </svg>
                                        </div>
                                    </div>

                                    <!-- Action Text Badge -->
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-black/60 backdrop-blur-md text-white text-xs font-bold border border-white/10 shadow-sm group-hover:bg-[#1768B5] transition">
                                        <i data-lucide="play" class="w-3 h-3 fill-current text-sky-300"></i>
                                        <span id="custom-cover-label">Mulai Putar Video</span>
                                    </span>
                                    
                                    <h4 id="custom-cover-title" class="text-xs sm:text-sm font-semibold text-slate-200 mt-2 max-w-md line-clamp-1 drop-shadow">
                                        {{ $activeMateri?->judul_materi }}
                                    </h4>
                                </div>

                                <!-- Top Badges Inside Custom Cover -->
                                <div class="absolute top-4 left-4 right-4 flex items-center justify-between pointer-events-none z-10">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-black/50 backdrop-blur-md text-[11px] font-bold text-sky-300 border border-white/10">
                                        <i data-lucide="video" class="w-3.5 h-3.5"></i>
                                        <span>Modul Video Interaktif</span>
                                    </span>
                                    <span id="custom-cover-duration" class="px-2.5 py-1 rounded-lg bg-black/50 backdrop-blur-md text-[11px] font-bold text-slate-300 border border-white/10">
                                        {{ $activeMateri && $activeMateri->durasi_menit > 0 ? $activeMateri->durasi_menit . ' Menit' : 'Sesi Video' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Fallback Video Card (Tanpa redirect eksternal ke YouTube) -->
                            <div id="video-fallback" class="w-full h-full flex flex-col items-center justify-center p-6 text-center text-white bg-gradient-to-br from-slate-900 to-slate-950 {{ $isYoutube ? 'hidden' : '' }}">
                                <div class="w-16 h-16 rounded-2xl bg-[#1768B5]/30 text-[#1768B5] flex items-center justify-center mb-3 border border-[#1768B5]/40 shadow-inner">
                                    <i data-lucide="play-circle" class="w-8 h-8 text-sky-400"></i>
                                </div>
                                <h4 class="text-base font-bold text-white mb-1" id="video-fallback-title">{{ $activeMateri?->judul_materi }}</h4>
                                <p class="text-xs text-slate-400 max-w-md">Video pembelajaran interaktif. Silakan tonton materi melalui pemutar video yang tersedia.</p>
                            </div>
                        </div>

                        <!-- Custom Player Control Bar (100% Kontrol Mandiri Aether, Bebas Logo YouTube) -->
                        <div id="custom-player-controls" class="bg-slate-900 px-4 py-2.5 rounded-b-2xl border-x border-b border-slate-800 flex items-center gap-3 text-white select-none shadow-md {{ $isYoutube ? '' : 'hidden' }}">
                            <!-- Play / Pause Button -->
                            <button type="button" id="ctrl-play-btn" onclick="togglePlayPause()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-[#1768B5] text-white flex items-center justify-center transition shadow-xs" title="Putar / Jeda">
                                <i data-lucide="play" class="w-4 h-4 fill-current text-white" id="ctrl-play-icon"></i>
                            </button>

                            <!-- Rewind 10s -->
                            <button type="button" onclick="seekRelative(-10)" class="w-8 h-8 rounded-lg hover:bg-white/10 text-slate-300 hover:text-white flex items-center justify-center transition" title="Mundur 10 detik">
                                <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            </button>

                            <!-- Forward 10s -->
                            <button type="button" onclick="seekRelative(10)" class="w-8 h-8 rounded-lg hover:bg-white/10 text-slate-300 hover:text-white flex items-center justify-center transition" title="Maju 10 detik">
                                <i data-lucide="rotate-cw" class="w-4 h-4"></i>
                            </button>

                            <!-- Scrubber / Seekbar -->
                            <div class="flex-1 flex items-center mx-1">
                                <input type="range" 
                                       id="ctrl-seekbar" 
                                       min="0" 
                                       max="100" 
                                       value="0" 
                                       step="0.1" 
                                       oninput="onSeekInput(this.value)" 
                                       onchange="onSeekChange(this.value)" 
                                       class="w-full h-1.5 bg-slate-700 hover:bg-slate-600 rounded-lg appearance-none cursor-pointer accent-[#1768B5] transition">
                            </div>

                            <!-- Time Display -->
                            <div class="text-[11px] font-mono text-slate-300 whitespace-nowrap min-w-[85px] text-center" id="ctrl-time-display">
                                00:00 / 00:00
                            </div>

                            <!-- Volume Button & Slider -->
                            <div class="hidden sm:flex items-center gap-1.5">
                                <button type="button" id="ctrl-mute-btn" onclick="toggleMute()" class="w-8 h-8 rounded-lg hover:bg-white/10 text-slate-300 hover:text-white flex items-center justify-center transition" title="Volume">
                                    <i data-lucide="volume-2" class="w-4 h-4" id="ctrl-volume-icon"></i>
                                </button>
                                <input type="range" 
                                       id="ctrl-volume-slider" 
                                       min="0" 
                                       max="100" 
                                       value="100" 
                                       oninput="setPlayerVolume(this.value)" 
                                       class="w-14 h-1 bg-slate-700 rounded-lg appearance-none cursor-pointer accent-sky-400">
                            </div>

                            <!-- Fullscreen Button -->
                            <button type="button" onclick="togglePlayerFullscreen()" class="w-8 h-8 rounded-lg hover:bg-white/10 text-slate-300 hover:text-white flex items-center justify-center transition" title="Layar Penuh">
                                <i data-lucide="maximize" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>

                    @php
                        $fileExt = strtolower(pathinfo($activeMateri->file_materi ?? '', PATHINFO_EXTENSION));
                        $isVideoActive = $activeMateri && $activeMateri->tipe_materi === 'Video' && $activeMateri->link_video;
                        $isPdfActive = $activeMateri && !$isVideoActive && ($fileExt === 'pdf');
                        $isPresentationActive = $activeMateri && !$isVideoActive && in_array($fileExt, ['ppt', 'pptx']);
                        $isOtherFileActive = $activeMateri && !$isVideoActive && !$isPdfActive && !$isPresentationActive;
                        $activePdfUrl = ($activeMateri && $activeMateri->file_materi) ? asset('storage/' . $activeMateri->file_materi) : '#';
                    @endphp

                    <!-- Interactive PDF Viewer Container (Muncul jika materi berupa Dokumen PDF Asli) -->
                    <div id="pdf-viewer-wrapper" class="{{ $isPdfActive ? '' : 'hidden' }}">
                        <div class="w-full bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 shadow-md">
                            <!-- Header Toolbar PDF -->
                            <div class="bg-slate-900 px-4 py-3 sm:px-6 sm:py-3.5 border-b border-slate-800 flex items-center justify-between gap-3 flex-wrap">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0 border border-rose-500/30 shadow-xs">
                                        <i data-lucide="file-text" class="w-5 h-5"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded border border-rose-500/20">Dokumen PDF</span>
                                            <span id="pdf-materi-urutan" class="text-[10px] font-bold text-slate-400">Modul #{{ $activeMateri?->urutan ?? 1 }}</span>
                                        </div>
                                        <h4 id="pdf-header-title" class="text-xs sm:text-sm font-bold text-white truncate max-w-sm sm:max-w-md mt-0.5">
                                            {{ $activeMateri?->judul_materi }}
                                        </h4>
                                    </div>
                                </div>

                                <!-- Toolbar Proteksi / Status Baca Saja -->
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 border border-slate-700/80 text-xs font-semibold shadow-2xs">
                                        <i data-lucide="lock" class="w-3.5 h-3.5 text-rose-400"></i>
                                        <span>Mode Baca Saja (Unduh Dinonaktifkan)</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Embedded PDF Frame (toolbar=0 & navpanes=0 mematikan tombol download/print bawaan viewer browser) -->
                            <div class="relative w-full h-[620px] sm:h-[760px] bg-slate-950">
                                <iframe id="pdf-embed-frame" 
                                        src="{{ ($isPdfActive && $activeMateri->file_materi) ? asset('storage/' . $activeMateri->file_materi) . '#toolbar=0&navpanes=0' : '' }}" 
                                        class="w-full h-full border-0" 
                                        title="Viewer Dokumen PDF">
                                </iframe>
                            </div>
                        </div>
                    </div>

                    <!-- Presentation / Slide Container (Muncul jika materi berupa file PowerPoint .ppt / .pptx) -->
                    <div id="presentation-wrapper" class="{{ $isPresentationActive ? '' : 'hidden' }}">
                        <div class="w-full bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 shadow-md">
                            <!-- Header Toolbar Slide -->
                            <div class="bg-slate-900 px-4 py-3 sm:px-6 sm:py-3.5 border-b border-slate-800 flex items-center justify-between gap-3 flex-wrap">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center shrink-0 border border-amber-500/30 shadow-xs">
                                        <i data-lucide="presentation" class="w-5 h-5"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">Slide Presentasi (PPT)</span>
                                            <span id="ppt-materi-urutan" class="text-[10px] font-bold text-slate-400">Modul #{{ $activeMateri?->urutan ?? 1 }}</span>
                                        </div>
                                        <h4 id="ppt-header-title" class="text-xs sm:text-sm font-bold text-white truncate max-w-sm sm:max-w-md mt-0.5">
                                            {{ $activeMateri?->judul_materi }}
                                        </h4>
                                    </div>
                                </div>

                                <!-- Toolbar Status Proteksi -->
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 text-slate-300 border border-slate-700/80 text-xs font-semibold shadow-2xs">
                                        <i data-lucide="lock" class="w-3.5 h-3.5 text-amber-400"></i>
                                        <span>Mode Tayangan (Auto-Download Dinonaktifkan)</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Presentation Body Preview -->
                            <div class="p-8 sm:p-14 flex flex-col items-center justify-center text-center bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 min-h-[380px]">
                                <div class="w-20 h-20 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mb-4 shadow-inner">
                                    <i data-lucide="presentation" class="w-10 h-10"></i>
                                </div>
                                <span class="text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-full bg-slate-800 text-amber-300 border border-slate-700 mb-2">
                                    Dokumen Slide PowerPoint
                                </span>
                                <h3 id="ppt-body-title" class="text-lg sm:text-xl font-bold text-white max-w-lg mb-2">
                                    {{ $activeMateri?->judul_materi }}
                                </h3>
                                <p id="ppt-display-desc" class="text-xs text-slate-400 max-w-md mb-6 leading-relaxed">
                                    {{ $activeMateri?->deskripsi ?: 'Modul tayangan slide presentasi materi pelatihan.' }}
                                </p>
                                <div class="bg-amber-950/40 border border-amber-500/30 rounded-xl p-3.5 max-w-md text-left text-xs text-amber-200/90 flex items-start gap-2.5">
                                    <i data-lucide="info" class="w-4 h-4 text-amber-400 shrink-0 mt-0.5"></i>
                                    <p class="leading-relaxed text-[11px]">
                                        File berformat PowerPoint (<code class="bg-amber-900/40 px-1 py-0.5 rounded">.ppt/.pptx</code>) tidak memiliki penampil bawaan di browser. Sistem melindungi modul ini agar <em>tidak ter-download otomatis</em> saat diklik. Untuk penayangan interaktif langsung di layar, disarankan mengunggah materi dalam format <strong>PDF</strong>.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File & Dataset Reader Container (Shown when non-video and non-pdf) -->
                    <div id="file-reader-wrapper" class="{{ $isOtherFileActive ? '' : 'hidden' }}">
                        <div class="p-8 sm:p-12 flex flex-col items-center justify-center text-center bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 min-h-[380px]">
                            <div id="file-type-icon-wrapper" class="w-20 h-20 rounded-2xl bg-sky-500/10 border border-sky-500/20 text-sky-400 flex items-center justify-center mb-4 shadow-inner">
                                <i id="file-type-icon" data-lucide="{{ $activeMateri && $activeMateri->tipe_materi === 'Dataset Praktik' ? 'database' : 'folder' }}" class="w-10 h-10"></i>
                            </div>
                            <span id="file-badge-type" class="text-[11px] font-black uppercase tracking-wider px-3 py-1 rounded-full bg-slate-800 text-sky-300 border border-slate-700 mb-2">
                                {{ $activeMateri?->tipe_materi }}
                            </span>
                            <h3 id="file-display-title" class="text-lg sm:text-xl font-bold text-white max-w-lg mb-2">
                                {{ $activeMateri?->judul_materi }}
                            </h3>
                            <p id="file-display-desc" class="text-xs text-slate-400 max-w-md mb-6 leading-relaxed">
                                {{ $activeMateri?->deskripsi ?: 'Modul file dan materi praktik terlampir siap dipelajari dan diunduh untuk bahan analisis skripsi.' }}
                            </p>

                            <div class="flex items-center gap-3 flex-wrap justify-center">
                                <a id="btn-download-main-file" 
                                   href="{{ $activeMateri && $activeMateri->file_materi ? asset('storage/' . $activeMateri->file_materi) : '#' }}" 
                                   download
                                   class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#1768B5] hover:bg-[#135392] text-white text-xs font-bold transition shadow-sm {{ $activeMateri && $activeMateri->file_materi ? '' : 'opacity-50 pointer-events-none' }}">
                                    <i data-lucide="download" class="w-4 h-4"></i>
                                    <span>Unduh File Materi</span>
                                </a>

                                <a id="btn-preview-main-file" 
                                   href="{{ $activeMateri && $activeMateri->file_materi ? asset('storage/' . $activeMateri->file_materi) : '#' }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition {{ $activeMateri && $activeMateri->file_materi ? '' : 'hidden' }}">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    <span>Buka di Tab Baru</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Attached File Bar (If active materi has both video AND file attachment) -->
            <div id="attached-file-box" class="bg-sky-50/70 border border-sky-200/80 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3 {{ $activeMateri && $activeMateri->file_materi && $activeMateri->tipe_materi === 'Video' ? '' : 'hidden' }}">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#1768B5] text-white flex items-center justify-center shrink-0 shadow-xs">
                        <i data-lucide="paperclip" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-800">File Praktik Terlampir</span>
                        <h4 id="attached-filename" class="text-xs font-bold text-gray-900 truncate max-w-sm sm:max-w-md">
                            {{ $activeMateri->file_materi ?? 'Dokumen Materi Latihan' }}
                        </h4>
                    </div>
                </div>
                <a id="attached-download-btn" href="{{ $activeMateri && $activeMateri->file_materi ? asset('storage/' . $activeMateri->file_materi) : '#' }}" download class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-sky-300 text-xs font-bold text-[#1768B5] hover:bg-sky-100 transition shrink-0 shadow-2xs">
                    <i data-lucide="download" class="w-3.5 h-3.5"></i>
                    <span>Unduh Lampiran</span>
                </a>
            </div>

            <!-- Active Materi Information & Navigation Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 pb-4 border-b border-gray-100">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span id="active-urutan" class="text-xs font-extrabold px-2.5 py-0.5 rounded-lg bg-sky-100 text-[#1768B5]">
                                Modul #{{ $activeMateri?->urutan ?? 1 }}
                            </span>
                            <span id="active-badge-tipe" class="text-xs font-bold px-2.5 py-0.5 rounded-lg bg-gray-100 text-gray-700">
                                {{ $activeMateri?->tipe_materi ?? 'Modul' }}
                            </span>
                            <span id="active-durasi" class="text-xs font-medium text-gray-500 flex items-center gap-1">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                <span>{{ $activeMateri && $activeMateri->durasi_menit > 0 ? $activeMateri->durasi_menit . ' Menit' : 'Sesi Praktik' }}</span>
                            </span>
                            @if($activeMateri?->is_free_preview)
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Preview Gratis
                                </span>
                            @endif
                        </div>
                        <h2 id="active-judul" class="text-lg sm:text-xl font-bold text-gray-900 tracking-tight">
                            {{ $activeMateri?->judul_materi ?? 'Pilih Modul dari Silabus' }}
                        </h2>
                    </div>

                    <!-- Next & Previous Navigation Buttons + Action Selesai Ditonton -->
                    <div class="flex items-center gap-2.5 shrink-0 self-end sm:self-center flex-wrap">
                        @php
                            $isActiveCompleted = $activeMateri && in_array($activeMateri->id, $completedMateriIds ?? []);
                        @endphp
                        <button type="button" 
                                id="btn-mark-selesai" 
                                onclick="toggleMateriSelesai()" 
                                data-id="{{ $activeMateri?->id }}"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold transition shadow-2xs {{ $isActiveCompleted ? 'bg-emerald-600 text-white hover:bg-emerald-700 border border-emerald-600' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }}">
                            <i data-lucide="{{ $isActiveCompleted ? 'check-check' : 'check-circle' }}" class="w-4 h-4" id="icon-mark-selesai"></i>
                            <span id="text-mark-selesai">{{ $isActiveCompleted ? '✓ Selesai Ditonton' : 'Tandai Selesai Ditonton' }}</span>
                        </button>

                        <button type="button" id="btn-prev" onclick="navigasiMateri(-1)" class="px-3 py-2 rounded-xl border border-gray-200 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition flex items-center gap-1.5 disabled:opacity-40 disabled:cursor-not-allowed">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                            <span class="hidden sm:inline">Sebelumnya</span>
                        </button>
                        <button type="button" id="btn-next" onclick="navigasiMateri(1)" class="px-3.5 py-2 rounded-xl bg-[#1768B5] hover:bg-[#135392] text-xs font-bold text-white transition flex items-center gap-1.5 shadow-2xs disabled:opacity-40 disabled:cursor-not-allowed">
                            <span>Selanjutnya</span>
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Tabbed Details -->
                <div class="space-y-4">
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-gray-400 mb-2 flex items-center gap-1.5">
                            <i data-lucide="file-text" class="w-3.5 h-3.5 text-[#1768B5]"></i>
                            <span>Catatan &amp; Deskripsi Materi</span>
                        </h4>
                        <div id="active-deskripsi" class="text-xs sm:text-sm text-gray-600 leading-relaxed bg-gray-50/70 p-4 rounded-xl border border-gray-100">
                            {{ $activeMateri?->deskripsi ?: 'Tidak ada catatan deskripsi khusus untuk modul materi ini.' }}
                        </div>
                    </div>

                    <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs text-gray-500">
                        <div class="flex items-center gap-2">
                            <i data-lucide="user-check" class="w-4 h-4 text-emerald-600"></i>
                            <span>Mentor: <strong>{{ $kelas?->mentor ? ($kelas->mentor->nama_lengkap . ($kelas->mentor->gelar ? ', ' . $kelas->mentor->gelar : '')) : 'Instruktur Ahli Aether' }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-sky-600"></i>
                            <span>Periode: {{ $kelas->tanggal_mulai ? date('d M', strtotime($kelas->tanggal_mulai)) : 'Jadwal Fleksibel' }} {{ $kelas->tanggal_selesai ? ' - ' . date('d M Y', strtotime($kelas->tanggal_selesai)) : '' }}</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ============================================== -->
        <!-- SISI KANAN (30%): LIST MATERI / SILABUS KURIKULUM -->
        <!-- ============================================== -->
        <div class="lg:col-span-3 space-y-4 lg:sticky lg:top-6">

            <!-- Silabus Header Card -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
                <div class="p-4 border-b border-gray-100 bg-gray-50/80">
                    <div class="flex items-center justify-between mb-1">
                        <h3 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                            <i data-lucide="list-video" class="w-4 h-4 text-[#1768B5]"></i>
                            <span>Silabus &amp; Daftar Materi</span>
                        </h3>
                        <span class="text-[11px] font-black px-2 py-0.5 rounded-full bg-sky-100 text-[#1768B5]">
                            {{ $materis->count() }} Modul
                        </span>
                    </div>
                    <p class="text-[11px] text-gray-500">Klik modul di bawah untuk memutar video atau mengunduh bahan materi.</p>

                    <!-- Progress Bar -->
                    <div class="mt-3">
                        <div class="flex items-center justify-between text-[10px] font-bold text-gray-500 mb-1">
                            <span id="progress-text">Selesai {{ $selesaiCount ?? 0 }} dari {{ $materis->count() }} Modul</span>
                            <span id="progress-percent">{{ $progressPercent ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                            <div id="progress-bar" class="bg-emerald-500 h-1.5 rounded-full transition-all duration-300" style="width: {{ $progressPercent ?? 0 }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Daftar Item Silabus -->
                <div class="divide-y divide-gray-100 max-h-[520px] overflow-y-auto overscroll-contain" id="silabus-list">
                    @forelse($materis as $idx => $m)
                        @php
                            $isActive = $activeMateri && $activeMateri->id === $m->id;
                            $isMateriCompleted = in_array($m->id, $completedMateriIds ?? []);
                        @endphp
                        <button type="button" 
                                onclick="pilihMateri({{ $m->id }})" 
                                data-id="{{ $m->id }}"
                                data-index="{{ $idx }}"
                                class="w-full text-left p-3.5 transition flex items-start gap-3 materi-item {{ $isActive ? 'bg-sky-50/80 border-l-4 border-l-[#1768B5]' : 'hover:bg-gray-50 border-l-4 border-l-transparent' }}">
                            
                            <!-- Index Badge -->
                            <span class="w-6 h-6 rounded-lg text-xs font-black flex items-center justify-center shrink-0 mt-0.5 {{ $isActive ? 'bg-[#1768B5] text-white shadow-2xs' : 'bg-gray-100 text-gray-600' }}">
                                {{ $m->urutan ?? ($idx + 1) }}
                            </span>

                            <div class="flex-1 min-w-0 space-y-1">
                                <h4 class="text-xs font-bold leading-snug line-clamp-2 {{ $isActive ? 'text-[#1768B5]' : 'text-gray-900' }}">
                                    {{ $m->judul_materi }}
                                </h4>
                                
                                <div class="flex items-center gap-2 text-[11px] text-gray-400 flex-wrap">
                                    <span class="inline-flex items-center gap-1 font-medium text-gray-500">
                                        @if($m->tipe_materi === 'Video')
                                            <i data-lucide="play-circle" class="w-3.5 h-3.5 text-rose-500"></i>
                                            <span>{{ $m->durasi_menit > 0 ? $m->durasi_menit . ' mnt' : 'Video' }}</span>
                                        @elseif($m->tipe_materi === 'Dokumen PDF')
                                            <i data-lucide="file-text" class="w-3.5 h-3.5 text-blue-500"></i>
                                            <span>Dokumen</span>
                                        @elseif($m->tipe_materi === 'Dataset Praktik')
                                            <i data-lucide="database" class="w-3.5 h-3.5 text-amber-500"></i>
                                            <span>Dataset</span>
                                        @else
                                            <i data-lucide="folder" class="w-3.5 h-3.5 text-purple-500"></i>
                                            <span>Modul</span>
                                        @endif
                                    </span>

                                    <!-- Status Selesai Badge -->
                                    <span id="badge-selesai-{{ $m->id }}" class="badge-selesai-item text-[10px] font-bold px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800 {{ $isMateriCompleted ? 'inline-flex items-center gap-0.5' : 'hidden' }}">
                                        <i data-lucide="check" class="w-3 h-3"></i>
                                        <span>Selesai</span>
                                    </span>

                                    @if($m->is_free_preview)
                                        <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-100 text-emerald-800">
                                            Free
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($isActive)
                                <div class="shrink-0 text-[#1768B5] self-center">
                                    <i data-lucide="volume-2" class="w-4 h-4 animate-pulse"></i>
                                </div>
                            @endif
                        </button>
                    @empty
                        <div class="p-6 text-center text-gray-400 text-xs">
                            <i data-lucide="inbox" class="w-8 h-8 text-gray-300 mx-auto mb-2"></i>
                            <p>Belum ada silabus yang terdaftar.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Live Session Quick Info -->
            @if(!empty($kelas->link_meeting))
                <div class="bg-gradient-to-br from-indigo-900 to-[#102654] text-white p-4 rounded-2xl shadow-xs border border-indigo-950 space-y-2.5">
                    <div class="flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-white/10 text-white">
                            <i data-lucide="video" class="w-4 h-4"></i>
                        </span>
                        <div>
                            <h4 class="text-xs font-bold">Ruang Sesi Live &amp; Konsultasi</h4>
                            <p class="text-[10px] text-indigo-200">Google Meet / Zoom Interaktif</p>
                        </div>
                    </div>
                    <a href="{{ $kelas->link_meeting }}" target="_blank" class="w-full py-2 px-3 rounded-xl bg-white text-[#102654] text-xs font-bold hover:bg-gray-100 transition flex items-center justify-center gap-1.5 shadow-xs">
                        <span>Buka Ruang Pertemuan</span>
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            @endif

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    const materis = @json($materis);
    let currentId = {{ $activeMateri ? $activeMateri->id : 'null' }};
    let completedMateriIds = @json($completedMateriIds ?? []);
    let currentYtId = '{{ $ytId ?? "" }}';
    const routeSelesaiTemplate = "{{ route('pelatihan.materi.selesai', ':id') }}";
    const csrfToken = "{{ csrf_token() }}";

    // Player State Variables
    let isPlaying = false;
    let isMuted = false;
    let videoDuration = {{ ($activeMateri && $activeMateri->durasi_menit > 0) ? ($activeMateri->durasi_menit * 60) : 0 }};
    let videoCurrentTime = 0;
    let isSeeking = false;
    let progressTimer = null;

    $(document).ready(function() {
        if (window.lucide) {
            lucide.createIcons();
        }
        updateNavButtons();
        updateMarkButtonUI();
        updateTimeUI(0, videoDuration);

        // Listen to YouTube postMessage events for state & time sync
        window.addEventListener('message', function(event) {
            if (!event || !event.data) return;
            try {
                let data = event.data;
                if (typeof data === 'string') {
                    data = JSON.parse(data);
                }
                if (data.event === 'infoDelivery' && data.info) {
                    if (typeof data.info.duration === 'number' && data.info.duration > 0) {
                        videoDuration = data.info.duration;
                    }
                    if (typeof data.info.currentTime === 'number' && !isSeeking) {
                        videoCurrentTime = data.info.currentTime;
                        updateTimeUI(videoCurrentTime, videoDuration);
                    }
                    if (typeof data.info.playerState === 'number') {
                        handleYtState(data.info.playerState);
                    }
                }
            } catch(e) {}
        });
    });

    function getYoutubeId(url) {
        if (!url) return null;
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    }

    function buildYoutubeEmbedUrl(ytId, autoplay = false) {
        if (!ytId) return '';
        const origin = encodeURIComponent(window.location.origin);
        return 'https://www.youtube-nocookie.com/embed/' + ytId + 
               '?enablejsapi=1&controls=0&modestbranding=1&rel=0&iv_load_policy=3&playsinline=1&disablekb=1&fs=0' + 
               (autoplay ? '&autoplay=1' : '') + 
               '&origin=' + origin;
    }

    function postToYt(func, args = []) {
        const iframe = document.getElementById('video-iframe');
        if (iframe && iframe.contentWindow) {
            iframe.contentWindow.postMessage(JSON.stringify({
                event: 'command',
                func: func,
                args: args
            }), '*');
        }
    }

    function formatTime(sec) {
        if (!sec || isNaN(sec) || sec < 0) return '00:00';
        const m = Math.floor(sec / 60);
        const s = Math.floor(sec % 60);
        return (m < 10 ? '0' : '') + m + ':' + (s < 10 ? '0' : '') + s;
    }

    function updateTimeUI(current, total) {
        if (total > 0 && !isSeeking) {
            const pct = (current / total) * 100;
            $('#ctrl-seekbar').val(pct);
        }
        $('#ctrl-time-display').text(formatTime(current) + ' / ' + formatTime(total));
    }

    function handleYtState(state) {
        // 1 = PLAYING, 2 = PAUSED, 0 = ENDED, 3 = BUFFERING
        if (state === 1) {
            isPlaying = true;
            $('#video-pause-overlay').addClass('hidden');
            $('#video-custom-cover').addClass('hidden');
            $('#ctrl-play-icon').attr('data-lucide', 'pause');
            startTicker();
        } else if (state === 2) {
            isPlaying = false;
            // Tampilkan Pause Overlay Kustom (100% Menutupi Video tanpa Logo YouTube)
            $('#video-pause-overlay').removeClass('hidden');
            $('#ctrl-play-icon').attr('data-lucide', 'play');
            stopTicker();
        } else if (state === 0) {
            isPlaying = false;
            $('#video-pause-overlay').removeClass('hidden');
            $('#ctrl-play-icon').attr('data-lucide', 'play');
            stopTicker();
        }
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    function startTicker() {
        stopTicker();
        progressTimer = setInterval(function() {
            postToYt('getCurrentTime');
        }, 800);
    }

    function stopTicker() {
        if (progressTimer) {
            clearInterval(progressTimer);
            progressTimer = null;
        }
    }

    function startCustomPlay() {
        if (!currentYtId) return;

        // Sembunyikan custom cover dengan transisi halus
        $('#video-custom-cover').fadeOut(200, function() {
            $(this).addClass('hidden');
        });

        // Set src iframe dengan controls=0 dan autoplay=1 (Bebas total dari logo YouTube)
        const embedUrl = buildYoutubeEmbedUrl(currentYtId, true);
        $('#video-iframe').attr('src', embedUrl).removeClass('hidden');
        $('#player-click-layer').removeClass('hidden');
        $('#video-top-guard').removeClass('hidden');
        $('#video-bottom-guard').removeClass('hidden');
        $('#custom-player-controls').removeClass('hidden');

        isPlaying = true;
        $('#ctrl-play-icon').attr('data-lucide', 'pause');
        if (window.lucide) {
            lucide.createIcons();
        }
        startTicker();
    }

    function togglePlayPause() {
        if (!currentYtId) return;

        if (isPlaying) {
            postToYt('pauseVideo');
            handleYtState(2);
        } else {
            postToYt('playVideo');
            handleYtState(1);
        }
    }

    function seekRelative(offset) {
        if (!videoDuration) return;
        const target = Math.max(0, Math.min(videoDuration, videoCurrentTime + offset));
        videoCurrentTime = target;
        postToYt('seekTo', [target, true]);
        updateTimeUI(videoCurrentTime, videoDuration);
    }

    function onSeekInput(pct) {
        isSeeking = true;
        if (videoDuration > 0) {
            const previewTime = (pct / 100) * videoDuration;
            $('#ctrl-time-display').text(formatTime(previewTime) + ' / ' + formatTime(videoDuration));
        }
    }

    function onSeekChange(pct) {
        if (videoDuration > 0) {
            const targetSec = (pct / 100) * videoDuration;
            videoCurrentTime = targetSec;
            postToYt('seekTo', [targetSec, true]);
        }
        isSeeking = false;
    }

    function toggleMute() {
        isMuted = !isMuted;
        postToYt(isMuted ? 'mute' : 'unMute');
        $('#ctrl-volume-icon').attr('data-lucide', isMuted ? 'volume-x' : 'volume-2');
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    function setPlayerVolume(val) {
        const vol = Number(val);
        postToYt('setVolume', [vol]);
        if (vol === 0) {
            $('#ctrl-volume-icon').attr('data-lucide', 'volume-x');
        } else {
            $('#ctrl-volume-icon').attr('data-lucide', 'volume-2');
        }
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    function togglePlayerFullscreen() {
        const box = document.getElementById('player-box');
        if (!box) return;

        if (!document.fullscreenElement) {
            if (box.requestFullscreen) {
                box.requestFullscreen();
            } else if (box.webkitRequestFullscreen) {
                box.webkitRequestFullscreen();
            }
        } else {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            }
        }
    }

    function updateMarkButtonUI() {
        const isCompleted = completedMateriIds.includes(Number(currentId));
        const item = materis.find(m => m.id === currentId);
        const isPdf = item && ((item.tipe_materi === 'Dokumen PDF') || (item.file_materi && item.file_materi.toLowerCase().endsWith('.pdf')));
        const isOtherDoc = item && (item.tipe_materi !== 'Video');

        const $btn = $('#btn-mark-selesai');
        const $text = $('#text-mark-selesai');
        const $icon = $('#icon-mark-selesai');

        const labelSelesai = (isPdf || isOtherDoc) ? '✓ Selesai Dipelajari' : '✓ Selesai Ditonton';
        const labelBelum = (isPdf || isOtherDoc) ? 'Tandai Selesai Dipelajari' : 'Tandai Selesai Ditonton';

        if (isCompleted) {
            $btn.removeClass('bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-200')
                .addClass('bg-emerald-600 text-white hover:bg-emerald-700 border-emerald-600 shadow-xs');
            $text.text(labelSelesai);
            $icon.attr('data-lucide', 'check-check');
        } else {
            $btn.removeClass('bg-emerald-600 text-white hover:bg-emerald-700 border-emerald-600 shadow-xs')
                .addClass('bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border-emerald-200');
            $text.text(labelBelum);
            $icon.attr('data-lucide', 'check-circle');
        }

        if (window.lucide) {
            lucide.createIcons();
        }
    }

    function toggleMateriSelesai() {
        if (!currentId) return;

        const url = routeSelesaiTemplate.replace(':id', currentId);
        const $btn = $('#btn-mark-selesai');
        $btn.prop('disabled', true).addClass('opacity-75');

        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: csrfToken,
                action: 'toggle'
            },
            success: function(res) {
                $btn.prop('disabled', false).removeClass('opacity-75');
                if (res.success) {
                    completedMateriIds = (res.completed_materis || []).map(Number);

                    // Update Silabus badge
                    const $badge = $('#badge-selesai-' + currentId);
                    if (res.is_completed) {
                        $badge.removeClass('hidden').addClass('inline-flex items-center gap-0.5');
                    } else {
                        $badge.addClass('hidden').removeClass('inline-flex items-center gap-0.5');
                    }

                    // Update Progress Bar & Text
                    $('#progress-text').text('Selesai ' + res.modul_selesai + ' dari ' + res.total_modul + ' Modul');
                    $('#progress-percent').text(res.progress + '%');
                    $('#progress-bar').css('width', res.progress + '%');

                    updateMarkButtonUI();

                    if (window.lucide) {
                        lucide.createIcons();
                    }
                }
            },
            error: function(err) {
                $btn.prop('disabled', false).removeClass('opacity-75');
                console.error(err);
                alert('Gagal memperbarui status materi selesai. Silakan coba kembali.');
            }
        });
    }

    function pilihMateri(id) {
        const item = materis.find(m => m.id === id);
        if (!item) return;

        currentId = id;
        const index = materis.findIndex(m => m.id === id);

        // Update URL query string without reloading
        const newUrl = new URL(window.location.href);
        newUrl.searchParams.set('materi', id);
        window.history.replaceState({ path: newUrl.href }, '', newUrl.href);

        // Pause active video and reset ticker
        postToYt('pauseVideo');
        stopTicker();
        isPlaying = false;
        videoCurrentTime = 0;
        videoDuration = (item.durasi_menit || 0) * 60;
        updateTimeUI(0, videoDuration);

        // 1. Update Media Viewer (PDF / Presentation / Video / File)
        const ytId = getYoutubeId(item.link_video);
        currentYtId = ytId;
        const fileName = (item.file_materi || '').toLowerCase();
        const fileExt = fileName.includes('.') ? fileName.split('.').pop() : '';
        const isVideo = item.tipe_materi === 'Video';
        const isRealPdf = !isVideo && (fileExt === 'pdf');
        const isPresentation = !isVideo && (fileExt === 'ppt' || fileExt === 'pptx');

        if (isRealPdf) {
            // Sembunyikan Player Video, Presentation & File Reader
            $('#video-player-wrapper').addClass('hidden');
            $('#video-iframe').attr('src', '');
            $('#video-top-guard').addClass('hidden');
            $('#video-bottom-guard').addClass('hidden');
            $('#video-custom-cover').addClass('hidden');
            $('#video-pause-overlay').addClass('hidden');
            $('#player-click-layer').addClass('hidden');
            $('#custom-player-controls').addClass('hidden');
            $('#presentation-wrapper').addClass('hidden');
            $('#file-reader-wrapper').addClass('hidden');

            // Tampilkan PDF Viewer
            $('#pdf-viewer-wrapper').removeClass('hidden');

            const pdfBaseUrl = item.file_materi ? ('{{ asset("storage") }}/' + item.file_materi) : '';
            $('#pdf-materi-urutan').text('Modul #' + (item.urutan || (index + 1)));
            $('#pdf-header-title').text(item.judul_materi);

            if (pdfBaseUrl) {
                $('#pdf-embed-frame').attr('src', pdfBaseUrl + '#toolbar=0&navpanes=0');
            } else {
                $('#pdf-embed-frame').attr('src', '');
            }
        } else if (isPresentation) {
            // Sembunyikan Player Video, PDF Viewer & File Reader
            $('#video-player-wrapper').addClass('hidden');
            $('#video-iframe').attr('src', '');
            $('#video-top-guard').addClass('hidden');
            $('#video-bottom-guard').addClass('hidden');
            $('#video-custom-cover').addClass('hidden');
            $('#video-pause-overlay').addClass('hidden');
            $('#player-click-layer').addClass('hidden');
            $('#custom-player-controls').addClass('hidden');
            $('#pdf-viewer-wrapper').addClass('hidden');
            $('#pdf-embed-frame').attr('src', ''); // PENTING: Kosongkan iframe src agar browser tidak memicu auto-download
            $('#file-reader-wrapper').addClass('hidden');

            // Tampilkan Presentation Wrapper Card
            $('#presentation-wrapper').removeClass('hidden');
            $('#ppt-materi-urutan').text('Modul #' + (item.urutan || (index + 1)));
            $('#ppt-header-title').text(item.judul_materi);
            $('#ppt-body-title').text(item.judul_materi);
            $('#ppt-display-desc').text(item.deskripsi || 'Modul tayangan slide presentasi materi pelatihan.');
        } else if (isVideo) {
            $('#presentation-wrapper').addClass('hidden');
            $('#pdf-viewer-wrapper').addClass('hidden');
            $('#pdf-embed-frame').attr('src', '');
            $('#file-reader-wrapper').addClass('hidden');
            $('#video-player-wrapper').removeClass('hidden');

            if (ytId) {
                // Perbarui gambar cover dan data modul
                const thumbUrl = 'https://img.youtube.com/vi/' + ytId + '/hqdefault.jpg';
                $('#custom-cover-img').attr('src', thumbUrl);
                $('#custom-cover-title').text(item.judul_materi);
                $('#custom-cover-duration').text(item.durasi_menit > 0 ? (item.durasi_menit + ' Menit') : 'Sesi Video');

                // Tampilkan Custom Cover Aether (sembunyikan splash logo/icon YouTube)
                $('#video-custom-cover').removeClass('hidden').show();
                $('#video-pause-overlay').addClass('hidden');
                $('#video-iframe').attr('src', '').addClass('hidden');
                $('#player-click-layer').addClass('hidden');
                $('#video-top-guard').addClass('hidden');
                $('#video-bottom-guard').addClass('hidden');
                $('#video-fallback').addClass('hidden');
                $('#custom-player-controls').removeClass('hidden');
                $('#ctrl-play-icon').attr('data-lucide', 'play');
            } else {
                $('#video-custom-cover').addClass('hidden');
                $('#video-pause-overlay').addClass('hidden');
                $('#video-iframe').attr('src', '').addClass('hidden');
                $('#player-click-layer').addClass('hidden');
                $('#video-top-guard').addClass('hidden');
                $('#video-bottom-guard').addClass('hidden');
                $('#video-fallback').removeClass('hidden');
                $('#video-fallback-title').text(item.judul_materi);
                $('#custom-player-controls').addClass('hidden');
            }
        } else {
            $('#pdf-viewer-wrapper').addClass('hidden');
            $('#pdf-embed-frame').attr('src', '');
            $('#video-player-wrapper').addClass('hidden');
            $('#video-iframe').attr('src', '');
            $('#video-top-guard').addClass('hidden');
            $('#video-bottom-guard').addClass('hidden');
            $('#video-custom-cover').addClass('hidden');
            $('#video-pause-overlay').addClass('hidden');
            $('#player-click-layer').addClass('hidden');
            $('#custom-player-controls').addClass('hidden');
            $('#file-reader-wrapper').removeClass('hidden');

            // Set file reader details
            $('#file-badge-type').text(item.tipe_materi);
            $('#file-display-title').text(item.judul_materi);
            $('#file-display-desc').text(item.deskripsi || 'Modul file dan materi praktik terlampir siap dipelajari dan diunduh.');

            let iconName = 'file-text';
            if (item.tipe_materi === 'Dataset Praktik') iconName = 'database';
            else iconName = 'folder';

            $('#file-type-icon').attr('data-lucide', iconName);

            if (item.file_materi) {
                const fileUrl = '{{ asset("storage") }}/' + item.file_materi;
                $('#btn-download-main-file').attr('href', fileUrl).removeClass('opacity-50 pointer-events-none');
                $('#btn-preview-main-file').attr('href', fileUrl).removeClass('hidden');
            } else {
                $('#btn-download-main-file').attr('href', '#').addClass('opacity-50 pointer-events-none');
                $('#btn-preview-main-file').attr('href', '#').addClass('hidden');
            }
        }

        // 2. Attached file box (for video with file)
        if (item.tipe_materi === 'Video' && item.file_materi) {
            $('#attached-file-box').removeClass('hidden');
            $('#attached-filename').text(item.file_materi);
            $('#attached-download-btn').attr('href', '{{ asset("storage") }}/' + item.file_materi);
        } else {
            $('#attached-file-box').addClass('hidden');
        }

        // 3. Update Title & Meta
        $('#active-urutan').text('Modul #' + (item.urutan || (index + 1)));
        $('#active-badge-tipe').text(item.tipe_materi);
        $('#active-durasi span').text(item.durasi_menit > 0 ? (item.durasi_menit + ' Menit') : 'Sesi Praktik');
        $('#active-judul').text(item.judul_materi);
        $('#active-deskripsi').text(item.deskripsi || 'Tidak ada catatan deskripsi khusus untuk modul materi ini.');

        // 4. Update Silabus list items active state
        $('.materi-item').removeClass('bg-sky-50/80 border-l-[#1768B5]').addClass('hover:bg-gray-50 border-l-transparent');
        $('.materi-item').find('span:first').removeClass('bg-[#1768B5] text-white shadow-2xs').addClass('bg-gray-100 text-gray-600');
        $('.materi-item').find('h4').removeClass('text-[#1768B5]').addClass('text-gray-900');
        $('.materi-item').find('.lucide-volume-2').parent().remove();

        const $activeBtn = $('.materi-item[data-id="' + id + '"]');
        $activeBtn.addClass('bg-sky-50/80 border-l-[#1768B5]').removeClass('border-l-transparent');
        $activeBtn.find('span:first').addClass('bg-[#1768B5] text-white shadow-2xs').removeClass('bg-gray-100 text-gray-600');
        $activeBtn.find('h4').addClass('text-[#1768B5]').removeClass('text-gray-900');
        $activeBtn.append('<div class="shrink-0 text-[#1768B5] self-center"><i data-lucide="volume-2" class="w-4 h-4 animate-pulse"></i></div>');

        updateNavButtons();
        updateMarkButtonUI();

        if (window.lucide) {
            lucide.createIcons();
        }
    }

    function updateNavButtons() {
        const index = materis.findIndex(m => m.id === currentId);
        if (index <= 0) {
            $('#btn-prev').prop('disabled', true);
        } else {
            $('#btn-prev').prop('disabled', false);
        }

        if (index >= materis.length - 1 || index < 0) {
            $('#btn-next').prop('disabled', true);
        } else {
            $('#btn-next').prop('disabled', false);
        }
    }

    function navigasiMateri(step) {
        const index = materis.findIndex(m => m.id === currentId);
        const nextIndex = index + step;
        if (nextIndex >= 0 && nextIndex < materis.length) {
            pilihMateri(materis[nextIndex].id);
        }
    }
</script>
@endpush
