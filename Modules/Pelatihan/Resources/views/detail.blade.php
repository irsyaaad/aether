@extends('layouts.app')

@section('title', ($kelas->judul ?? 'Detail Workshop') . ' - Aether Academy')
@section('page_title', 'Detail Kelas & Workshop Riset')

@section('content')
<div class="space-y-6 pb-16 lg:pb-6">

    <!-- Top Breadcrumb & Quick Action -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-white p-4 sm:px-6 sm:py-3.5 rounded-2xl border border-gray-200 shadow-xs">
        <div class="flex items-center gap-2 flex-wrap text-xs">
            <a href="{{ route('pelatihan.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Katalog Pelatihan</span>
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-500 font-medium">{{ $kelas->kategori ?? 'Workshop Riset' }}</span>
            <span class="text-gray-300">/</span>
            <span class="text-[#1768B5] font-bold truncate max-w-xs sm:max-w-md">{{ $kelas->judul }}</span>
        </div>

        <div class="flex items-center gap-2">
            @if($alreadyPurchased)
                <a href="{{ route('pelatihan.show', $kelas->id) }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition">
                    <i data-lucide="play-circle" class="w-4 h-4"></i>
                    <span>Masuk Ruang Belajar</span>
                </a>
            @else
                <a href="{{ route('pelatihan.payment', $kelas->id) }}" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-xl text-xs font-bold text-white bg-[#102654] hover:bg-[#1768B5] shadow-xs transition">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Daftar Sekarang</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Hero Header Banner (Ebizmark Style) -->
    <div class="bg-gradient-to-br from-[#102654] via-[#163675] to-[#1768B5] rounded-3xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <!-- Abstract Background Ornaments -->
        <div class="absolute -right-16 -top-16 w-80 h-80 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute right-1/3 -bottom-20 w-64 h-64 bg-sky-400/10 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 max-w-4xl space-y-4">
            <!-- Badges -->
            <div class="flex items-center gap-2 flex-wrap">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-[#F6C75A] text-xs font-extrabold border border-white/20">
                    <i data-lucide="award" class="w-3.5 h-3.5"></i>
                    Aether Academic Workshop
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/20 text-white backdrop-blur-md">
                    {{ $kelas->kategori ?? 'Riset & Metodologi' }}
                </span>
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-sky-400/25 text-sky-200 border border-sky-300/30">
                    Level: {{ $kelas->level ?? 'Semua Tingkat' }}
                </span>
                @if(!empty($kelas->badge))
                <span class="px-3 py-1 rounded-full text-xs font-black bg-[#F6C75A] text-[#102654] shadow-sm">
                    ⭐ {{ $kelas->badge }}
                </span>
                @endif
            </div>

            <!-- Title -->
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black leading-tight tracking-tight text-white drop-shadow-sm">
                {{ $kelas->judul }}
            </h1>

            <!-- Subtitle -->
            <p class="text-sm sm:text-base text-sky-100 max-w-3xl leading-relaxed">
                {{ $kelas->silabus_singkat ?: 'Pelatihan intensif metodologi riset, pengolahan data praktis hands-on, dan panduan penulisan naskah Bab 4 siap sidang bersama instruktur ahli.' }}
            </p>

            <!-- Metadata Highlights (Grid) -->
            <div class="pt-4 border-t border-white/15 grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center shrink-0 text-[#F6C75A]">
                        <i data-lucide="user-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <span class="text-gray-300 text-[11px] block">Instruktur Ahli</span>
                        <strong class="text-white truncate block max-w-[140px]">{{ $kelas->mentor ? $kelas->mentor->nama_lengkap : ($kelas->instruktur ?? 'Mentor Aether') }}</strong>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center shrink-0 text-[#F6C75A]">
                        <i data-lucide="video" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <span class="text-gray-300 text-[11px] block">Format Workshop</span>
                        <strong class="text-white block">{{ $kelas->tipe_kelas ?: 'Online Live Zoom' }}</strong>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center shrink-0 text-[#F6C75A]">
                        <i data-lucide="file-check" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <span class="text-gray-300 text-[11px] block">Sertifikat Resmi</span>
                        <strong class="text-white block">32 JP Bernomor</strong>
                    </div>
                </div>

                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center shrink-0 text-[#F6C75A]">
                        <i data-lucide="users" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <span class="text-gray-300 text-[11px] block">Sisa Kuota Kursi</span>
                        <strong class="text-emerald-300 block font-mono font-bold">{{ $sisaKuota }} Peserta Lagi</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Layout (70% Content Kiri & 30% Sticky Sidebar Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-10 gap-6 items-start">

        <!-- ========================================== -->
        <!-- SISI KIRI (70%): DETAIL KELAS & WORKSHOP   -->
        <!-- ========================================== -->
        <div class="lg:col-span-7 space-y-6">

            <!-- Card 1: Deskripsi & Target Capaian -->
            <div class="bg-white rounded-3xl border border-gray-200/90 p-6 sm:p-8 shadow-xs space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 text-[#1768B5] text-xs font-bold mb-3 border border-sky-100">
                        <i data-lucide="info" class="w-3.5 h-3.5"></i>
                        Tentang Workshop Ini
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                        Deskripsi &amp; Sasaran Pelatihan
                    </h2>
                </div>

                <div class="prose prose-sm text-gray-600 leading-relaxed text-xs sm:text-sm space-y-3">
                    @if(!empty($kelas->deskripsi))
                        {!! nl2br(e($kelas->deskripsi)) !!}
                    @else
                        <p>
                            Workshop intensif ini dirancang untuk memberikan pemahaman holistik dan keterampilan praktis (*hands-on*) dalam pengolahan serta analisis data ilmiah. Peserta dibimbing mulai dari perumusan model konseptual, penyiapan dataset, eksekusi software statistik, deteksi eror, hingga interpretasi output yang siap disajikan ke dalam naskah Bab 4 skripsi, tesis, disertasi, atau artikel jurnal ilmiah bereputasi.
                        </p>
                    @endif
                </div>

                <!-- 4 Pilar Target Capaian Pelatihan -->
                <div class="pt-6 border-t border-gray-100">
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-400 mb-4">
                        Kompetensi yang Akan Anda Kuasai:
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="p-4 rounded-2xl bg-sky-50/60 border border-sky-100 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#1768B5] text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-xs">
                                01
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900">Perumusan Model &amp; Hipotesis</h4>
                                <p class="text-[11px] text-gray-500 mt-0.5">Memetakan kerangka konseptual yang kokoh sesuai teori rujukan utama.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-purple-50/60 border border-purple-100 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-purple-700 text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-xs">
                                02
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900">Pembersihan &amp; Olah Data Riil</h4>
                                <p class="text-[11px] text-gray-500 mt-0.5">Mengatasi data outlier, uji kelayakan instrumen, dan tabulasi kuesioner.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-emerald-700 text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-xs">
                                03
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900">Interpretasi Standar Penguji</h4>
                                <p class="text-[11px] text-gray-500 mt-0.5">Menjawab pertanyaan penguji dengan dalil statistik dan standar baku.</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-amber-50/60 border border-amber-100 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-600 text-white flex items-center justify-center shrink-0 font-bold text-xs shadow-xs">
                                04
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900">Penyusunan Bab 4 Siap Sidang</h4>
                                <p class="text-[11px] text-gray-500 mt-0.5">Tata letak tabel hasil, narasi pembahasan, dan kesimpulan hipotesis.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Siapa yang Wajib Mengikuti -->
                <div class="pt-6 border-t border-gray-100 flex items-center gap-3 bg-gray-50/80 p-4 rounded-2xl border border-gray-100">
                    <div class="w-10 h-10 rounded-xl bg-white shadow-xs flex items-center justify-center text-[#1768B5] shrink-0 border border-gray-200/80">
                        <i data-lucide="target" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <span class="text-xs font-bold text-gray-900 block">Target Peserta:</span>
                        <span class="text-[11px] text-gray-500">Mahasiswa S1/S2/S3 tingkat akhir, Dosen Pembimbing, Peneliti Lembaga, serta Praktisi Analisis Data.</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Silabus & Kurikulum Modul Pembelajaran (Interactive Sesi Modul) -->
            <div class="bg-white rounded-3xl border border-gray-200/90 p-6 sm:p-8 shadow-xs space-y-6" id="section-silabus">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold mb-2 border border-purple-100">
                            <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                            Kurikulum &amp; Silabus
                        </div>
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                            Modul Sesi Pembelajaran
                        </h2>
                        <p class="text-xs text-gray-500 mt-1">
                            Disusun secara runtut dari konsep dasar hingga interpretasi tingkat lanjut.
                        </p>
                    </div>

                    <div class="flex items-center gap-2 text-xs">
                        <span class="px-3 py-1.5 rounded-xl bg-gray-100 font-bold text-gray-700">
                            Total: {{ $materis->count() }} Modul
                        </span>
                        @if($totalDurasiMenit > 0)
                        <span class="px-3 py-1.5 rounded-xl bg-sky-50 font-bold text-[#1768B5] border border-sky-100">
                            ⏱️ ~{{ $totalDurasiMenit }} Menit
                        </span>
                        @endif
                    </div>
                </div>

                <!-- Daftar Modul Accordion / Cards -->
                <div class="space-y-3">
                    @forelse($materis as $index => $m)
                    <div class="p-4 sm:p-5 rounded-2xl border border-gray-200/90 hover:border-[#1768B5]/50 transition-all bg-white hover:shadow-xs group">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3.5">
                                <span class="w-8 h-8 rounded-xl bg-sky-100 text-[#1768B5] font-black text-xs flex items-center justify-center shrink-0 mt-0.5">
                                    #{{ $m->urutan ?? ($index + 1) }}
                                </span>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h4 class="text-xs sm:text-sm font-bold text-gray-900 group-hover:text-[#1768B5] transition-colors">
                                            {{ $m->judul_materi }}
                                        </h4>
                                        @if($m->is_free_preview)
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                            Free Preview
                                        </span>
                                        @endif
                                    </div>

                                    @if(!empty($m->deskripsi))
                                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                        {{ $m->deskripsi }}
                                    </p>
                                    @endif

                                    <!-- Modul Meta -->
                                    <div class="flex items-center gap-3 text-[11px] text-gray-400 mt-2.5 flex-wrap">
                                        @php
                                            $tipeIcon = 'video';
                                            if($m->tipe_materi === 'Dokumen PDF') $tipeIcon = 'file-text';
                                            elseif($m->tipe_materi === 'Dataset Praktik') $tipeIcon = 'database';
                                            elseif($m->tipe_materi === 'Kuis/Tugas') $tipeIcon = 'check-square';
                                        @endphp
                                        <span class="inline-flex items-center gap-1 font-semibold text-gray-600 bg-gray-50 px-2 py-0.5 rounded-md border border-gray-100">
                                            <i data-lucide="{{ $tipeIcon }}" class="w-3 h-3 text-[#1768B5]"></i>
                                            {{ $m->tipe_materi }}
                                        </span>
                                        @if($m->durasi_menit > 0)
                                        <span class="inline-flex items-center gap-1 font-mono text-gray-500">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            {{ $m->durasi_menit }} Menit
                                        </span>
                                        @endif
                                        @if(!empty($m->file_materi))
                                        <span class="inline-flex items-center gap-1 text-[#1768B5] font-mono">
                                            <i data-lucide="paperclip" class="w-3 h-3"></i>
                                            {{ basename($m->file_materi) }}
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            @if($m->is_free_preview)
                            <a href="{{ route('pelatihan.show', ['id' => $kelas->id, 'materi' => $m->id]) }}" class="shrink-0 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold border border-emerald-200 transition inline-flex items-center gap-1" title="Tonton Pratinjau Gratis">
                                <i data-lucide="play" class="w-3 h-3 fill-current"></i>
                                <span class="hidden sm:inline">Lihat Preview</span>
                            </a>
                            @else
                            <div class="shrink-0 p-2 text-gray-300" title="Materi Terkunci untuk Peserta Terdaftar">
                                <i data-lucide="lock" class="w-4 h-4"></i>
                            </div>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center text-gray-400 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                        <i data-lucide="book-open" class="w-8 h-8 mx-auto mb-2 text-gray-300"></i>
                        <p class="text-xs">Modul dan silabus pembelajaran sedang disusun oleh instruktur ahli.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Card 3: 6 Fasilitas & Benefit Eksklusif Peserta (Ebizmark Style) -->
            <div class="bg-white rounded-3xl border border-gray-200/90 p-6 sm:p-8 shadow-xs space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold mb-2 border border-emerald-100">
                        <i data-lucide="gift" class="w-3.5 h-3.5"></i>
                        Fasilitas &amp; Keuntungan
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                        Benefit yang Didapatkan Peserta
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">
                        Investasi sekali untuk bekal riset dan kelulusan akademik Anda.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Benefit 1 -->
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-sky-50/40 hover:border-sky-100 transition-all space-y-2">
                        <div class="w-10 h-10 rounded-xl bg-sky-100 text-[#1768B5] flex items-center justify-center">
                            <i data-lucide="award" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900">E-Sertifikat Resmi 32 JP</h4>
                        <p class="text-[11px] text-gray-500 leading-relaxed">
                            Dilengkapi QR Code verifikasi online dan nomor registrasi resmi untuk lampiran SKPI / BKD.
                        </p>
                    </div>

                    <!-- Benefit 2 -->
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-sky-50/40 hover:border-sky-100 transition-all space-y-2">
                        <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center">
                            <i data-lucide="play-circle" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900">Rekaman Video Lifetime</h4>
                        <p class="text-[11px] text-gray-500 leading-relaxed">
                            Akses tayangan ulang tanpa batas waktu melalui portal belajar Aether saat Anda membutuhkan kembali.
                        </p>
                    </div>

                    <!-- Benefit 3 -->
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-sky-50/40 hover:border-sky-100 transition-all space-y-2">
                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                            <i data-lucide="database" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900">Dataset &amp; Template Bab 4</h4>
                        <p class="text-[11px] text-gray-500 leading-relaxed">
                            File dataset latihan riil (.sav, .csv) serta template Word Bab 4 format baku perguruan tinggi.
                        </p>
                    </div>

                    <!-- Benefit 4 -->
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-sky-50/40 hover:border-sky-100 transition-all space-y-2">
                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                            <i data-lucide="message-square" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900">Grup Mentoring Eksklusif</h4>
                        <p class="text-[11px] text-gray-500 leading-relaxed">
                            Wadah diskusi langsung bersama mentor dan komunitas sesama akademisi se-Indonesia.
                        </p>
                    </div>

                    <!-- Benefit 5 -->
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-sky-50/40 hover:border-sky-100 transition-all space-y-2">
                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center">
                            <i data-lucide="help-circle" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900">Live Q&amp;A dan Bedah Kasus</h4>
                        <p class="text-[11px] text-gray-500 leading-relaxed">
                            Kesempatan membedah kendala model dan anomali data penelitian spesifik yang Anda hadapi.
                        </p>
                    </div>

                    <!-- Benefit 6 -->
                    <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-sky-50/40 hover:border-sky-100 transition-all space-y-2">
                        <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center">
                            <i data-lucide="file-code" class="w-5 h-5"></i>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900">Slide Materi &amp; Do-File</h4>
                        <p class="text-[11px] text-gray-500 leading-relaxed">
                            Buku panduan digital, file presentasi PPT mentor, dan file sintaks/script program statistik.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Card 4: Profil Instruktur / Mentor Ahli -->
            @php
                $mentor = $kelas->mentor;
            @endphp
            <div class="bg-white rounded-3xl border border-gray-200/90 p-6 sm:p-8 shadow-xs space-y-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-[#1768B5] text-xs font-bold mb-2 border border-blue-100">
                        <i data-lucide="user-check" class="w-3.5 h-3.5"></i>
                        Pemateri &amp; Pengampu
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">
                        Profil Instruktur Workshop
                    </h2>
                </div>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-5 p-5 rounded-2xl bg-gradient-to-r from-gray-50 to-sky-50/40 border border-gray-200/80">
                    <div class="w-20 h-20 rounded-2xl bg-gradient-to-tr from-[#102654] via-[#1768B5] to-sky-400 text-white flex items-center justify-center font-black text-2xl shrink-0 shadow-md border-2 border-white">
                        @if($mentor && !empty($mentor->nama_lengkap))
                            {{ strtoupper(substr($mentor->nama_lengkap, 0, 2)) }}
                        @else
                            {{ strtoupper(substr($kelas->instruktur ?? 'MA', 0, 2)) }}
                        @endif
                    </div>
                    <div class="space-y-1">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900">
                            {{ $mentor ? $mentor->nama_lengkap : ($kelas->instruktur ?? 'Nur Atika Rochmah, M.Si.') }}
                        </h3>
                        <p class="text-xs font-semibold text-[#1768B5]">
                            {{ $mentor->spesialisasi ?? 'Konsultan Statistik, Analisis Multivariat & Publikasi Ilmiah' }}
                        </p>
                        <p class="text-xs text-gray-500">
                            {{ $mentor->institusi ?? 'Laboratorium Metodologi Riset & Sains Data Terapan' }}
                        </p>
                        <div class="flex items-center gap-2 pt-2 text-[11px] text-gray-600">
                            <span class="inline-flex items-center gap-1 bg-white px-2.5 py-1 rounded-lg border border-gray-200 font-medium">
                                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
                                Mentor Tersertifikasi
                            </span>
                            <span class="inline-flex items-center gap-1 bg-white px-2.5 py-1 rounded-lg border border-gray-200 font-medium">
                                <i data-lucide="graduation-cap" class="w-3.5 h-3.5 text-[#1768B5]"></i>
                                {{ $mentor->pendidikan_terakhir ?? 'Magister / Doktoral' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 5: FAQ (Pertanyaan yang Sering Diajukan) -->
            <div class="bg-white rounded-3xl border border-gray-200/90 p-6 sm:p-8 shadow-xs space-y-5">
                <div>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight">
                        Pertanyaan Umum (FAQ)
                    </h2>
                    <p class="text-xs text-gray-500 mt-1">Jawaban atas pertanyaan yang sering diajukan calon peserta.</p>
                </div>

                <div class="space-y-3 text-xs" id="accordion-faq">
                    <div class="border border-gray-200 rounded-2xl p-4 space-y-1.5">
                        <h4 class="font-bold text-gray-900 flex items-center gap-2">
                            <i data-lucide="help-circle" class="w-4 h-4 text-[#1768B5]"></i>
                            Apakah pemula tanpa latar belakang statistik bisa mengikuti workshop ini?
                        </h4>
                        <p class="text-gray-600 pl-6 leading-relaxed">
                            Sangat bisa! Seluruh materi dirancang bertahap dari pemahaman dasar, langkah instalasi software, logika uji hipotesis, hingga cara membaca angka output statistik tanpa bahasa teknis yang rumit.
                        </p>
                    </div>

                    <div class="border border-gray-200 rounded-2xl p-4 space-y-1.5">
                        <h4 class="font-bold text-gray-900 flex items-center gap-2">
                            <i data-lucide="help-circle" class="w-4 h-4 text-[#1768B5]"></i>
                            Bagaimana jika saya berhalangan hadir saat sesi live Zoom berlangsung?
                        </h4>
                        <p class="text-gray-600 pl-6 leading-relaxed">
                            Jangan khawatir. Rekaman video utuh berkualitas HD beserta materi modul dan dataset dapat Anda akses selamanya (*lifetime access*) melalui portal belajar Aether akun Anda.
                        </p>
                    </div>

                    <div class="border border-gray-200 rounded-2xl p-4 space-y-1.5">
                        <h4 class="font-bold text-gray-900 flex items-center gap-2">
                            <i data-lucide="help-circle" class="w-4 h-4 text-[#1768B5]"></i>
                            Apakah e-sertifikat resmi dan dapat digunakan untuk pelengkap sidang / BKD?
                        </h4>
                        <p class="text-gray-600 pl-6 leading-relaxed">
                            Ya, e-sertifikat diterbitkan resmi dengan nomor registrasi, bobot 32 JP, dan barcode verifikasi keaslian digital yang dapat dicek langsung secara publik.
                        </p>
                    </div>
                </div>
            </div>

        </div>

        <!-- ========================================== -->
        <!-- SISI KANAN (30%): STICKY ACTION CARD       -->
        <!-- ========================================== -->
        <div class="lg:col-span-3 space-y-4 lg:sticky lg:top-6">

            <div class="bg-white rounded-3xl border border-gray-200/90 shadow-lg overflow-hidden">
                <!-- Thumbnail Header Card -->
                <div class="relative w-full h-44 bg-slate-900 overflow-hidden">
                    @if(!empty($kelas->thumbnail_url))
                        <img src="{{ $kelas->thumbnail_url }}" alt="{{ $kelas->judul }}" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/30 to-transparent"></div>
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-[#102654] via-[#1768B5] to-sky-600 flex flex-col items-center justify-center p-4 text-center text-white">
                            <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center mb-2 border border-white/20">
                                <i data-lucide="presentation" class="w-6 h-6 text-[#F6C75A]"></i>
                            </div>
                            <span class="text-xs font-bold">{{ $kelas->kategori ?? 'Workshop Riset' }}</span>
                        </div>
                    @endif

                    <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white text-xs">
                        <span class="px-2.5 py-1 rounded-full bg-black/60 backdrop-blur-md text-[11px] font-bold border border-white/20">
                            {{ $kelas->tipe_kelas ?: 'Online Live' }}
                        </span>
                        <span class="px-2.5 py-1 rounded-full bg-[#F6C75A] text-[#102654] text-[11px] font-black shadow-xs">
                            {{ $materis->count() }} Modul
                        </span>
                    </div>
                </div>

                <!-- Card Body & Pricing -->
                <div class="p-6 space-y-5">
                    <!-- Pricing Area -->
                    <div class="space-y-1">
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider block">Investasi Pelatihan</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-black text-gray-900 font-mono">
                                {{ $kelas->harga_formatted }}
                            </span>
                            @if($kelas->harga > 0)
                            <span class="text-xs text-gray-400 line-through font-mono">
                                Rp {{ number_format($kelas->harga * 1.5, 0, ',', '.') }}
                            </span>
                            @endif
                        </div>
                        <span class="inline-block text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-200">
                            Hemat 35% Spesial Batch Ini
                        </span>
                    </div>

                    <!-- Kuota Progress -->
                    <div class="p-3.5 rounded-2xl bg-amber-50/70 border border-amber-200/80 space-y-2">
                        <div class="flex items-center justify-between text-xs font-bold text-amber-900">
                            <span class="flex items-center gap-1.5">
                                <i data-lucide="flame" class="w-4 h-4 text-rose-500 fill-current"></i>
                                Sisa Kuota Terbatas
                            </span>
                            <span class="font-mono text-rose-600">{{ $sisaKuota }} Kursi Tersedia</span>
                        </div>
                        @php
                            $persenTerisi = $kuotaMaksimal > 0 ? min(100, round(($terdaftarCount / $kuotaMaksimal) * 100)) : 65;
                            if($persenTerisi < 50) $persenTerisi = 65;
                        @endphp
                        <div class="w-full bg-amber-200/60 rounded-full h-2 overflow-hidden">
                            <div class="bg-rose-500 h-2 rounded-full transition-all duration-500" style="width: {{ $persenTerisi }}%"></div>
                        </div>
                        <p class="text-[10px] text-amber-700 leading-tight">
                            Pendaftaran akan ditutup otomatis ketika kuota peserta terpenuhi.
                        </p>
                    </div>

                    <!-- ACTION BUTTON UTAMA (DAFTAR SEKARANG -> MASUK MENU PAYMENT) -->
                    <div>
                        @if($alreadyPurchased)
                            <a href="{{ route('pelatihan.show', $kelas->id) }}" class="w-full py-4 px-6 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-black text-sm text-center shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2 transition-all">
                                <i data-lucide="play-circle" class="w-5 h-5"></i>
                                <span>Akses Ruang Belajar</span>
                            </a>
                        @else
                            <a href="{{ route('pelatihan.payment', $kelas->id) }}" class="w-full py-4 px-6 rounded-2xl bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] hover:from-[#0d1f44] hover:to-[#1768B5] text-white font-black text-sm text-center shadow-lg shadow-sky-600/30 flex items-center justify-center gap-2 group transition-all duration-300 transform hover:-translate-y-0.5">
                                <i data-lucide="shopping-cart" class="w-5 h-5"></i>
                                <span>Daftar Sekarang</span>
                                <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-1 transition-transform"></i>
                            </a>
                        @endif
                    </div>

                    <!-- Summary Benefits Checklist -->
                    <div class="space-y-2.5 text-xs text-gray-600 pt-3 border-t border-gray-100">
                        <span class="font-bold text-gray-900 block text-[11px] uppercase tracking-wider">Fasilitas Termasuk:</span>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                            <span>Akses Sesi Live &amp; Rekaman Selamanya</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                            <span>{{ $materis->count() }} Modul, Dataset &amp; Template Bab 4</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                            <span>E-Sertifikat 32 JP Bernomor Resmi</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                            <span>Konsultasi &amp; Tanya Jawab Kasus Data</span>
                        </div>
                    </div>

                    <!-- Trust Badges -->
                    <div class="pt-3 border-t border-gray-100 text-center">
                        <span class="text-[10px] text-gray-400 block mb-2">Metode Pembayaran Aman &amp; Terverifikasi:</span>
                        <div class="flex items-center justify-center gap-2 text-gray-500 text-[10px] font-bold">
                            <span class="px-2 py-0.5 rounded bg-gray-100 border border-gray-200">QRIS</span>
                            <span class="px-2 py-0.5 rounded bg-gray-100 border border-gray-200">BCA</span>
                            <span class="px-2 py-0.5 rounded bg-gray-100 border border-gray-200">Mandiri</span>
                            <span class="px-2 py-0.5 rounded bg-gray-100 border border-gray-200">BRI</span>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Floating Action Bar for Mobile Screens (Bawah Layar HP) -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md p-3.5 border-t border-gray-200 shadow-2xl flex items-center justify-between gap-4">
        <div>
            <span class="text-[10px] text-gray-400 block font-semibold uppercase">Total Investasi</span>
            <span class="text-lg font-black text-gray-900 font-mono">{{ $kelas->harga_formatted }}</span>
        </div>
        <div>
            @if($alreadyPurchased)
                <a href="{{ route('pelatihan.show', $kelas->id) }}" class="px-6 py-2.5 rounded-xl bg-emerald-600 text-white font-bold text-xs flex items-center gap-1.5 shadow-md">
                    <i data-lucide="play-circle" class="w-4 h-4"></i>
                    <span>Masuk Kelas</span>
                </a>
            @else
                <a href="{{ route('pelatihan.payment', $kelas->id) }}" class="px-6 py-2.5 rounded-xl bg-[#102654] hover:bg-[#1768B5] text-white font-bold text-xs flex items-center gap-1.5 shadow-md">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    <span>Daftar Sekarang</span>
                </a>
            @endif
        </div>
    </div>

</div>
@endsection
