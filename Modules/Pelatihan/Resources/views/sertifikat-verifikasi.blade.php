<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Keaslian E-Sertifikat - {{ config('app.name', 'Aether Lab') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="bg-slate-50 text-slate-800 font-sans min-h-screen flex flex-col justify-between antialiased selection:bg-[#1768B5] selection:text-white">

    <!-- Top Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-30 shadow-2xs">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-[#102654] to-[#1768B5] text-white flex items-center justify-center font-black text-lg shadow-xs">
                    <i data-lucide="award" class="w-5 h-5 text-amber-300"></i>
                </div>
                <div>
                    <h1 class="text-sm font-black text-[#102654] tracking-tight uppercase">AETHER RESEARCH LAB</h1>
                    <p class="text-[10px] text-slate-500 font-medium">Sistem Verifikasi Kredensial Akademik Resmi</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('pelatihan.index') }}" class="text-xs font-bold text-[#1768B5] hover:text-[#102654] transition px-3 py-1.5 rounded-lg hover:bg-sky-50">
                    Katalog Pelatihan
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-3xl mx-auto w-full px-4 sm:px-6 py-8 sm:py-12 flex-1">
        
        @if($sertifikat && $isValidCompleted)
            <!-- Card Sertifikat Terverifikasi 100% Selesai -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden space-y-6">
                
                <!-- Status Banner Valid -->
                <div class="bg-gradient-to-r from-emerald-600 via-teal-700 to-[#102654] p-6 sm:p-8 text-white text-center space-y-3 relative overflow-hidden">
                    <div class="absolute -right-8 -bottom-8 opacity-10 pointer-events-none">
                        <i data-lucide="shield-check" class="w-56 h-56 text-white"></i>
                    </div>

                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center mx-auto text-emerald-200 border border-white/20 shadow-inner">
                        <i data-lucide="check-check" class="w-9 h-9 text-white"></i>
                    </div>

                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/30 text-white text-[11px] font-bold tracking-wider uppercase border border-emerald-400/40 backdrop-blur-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
                        Status: Terverifikasi Asli &amp; Sah
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        E-Sertifikat Kelulusan Terdaftar Resmi
                    </h2>
                    <p class="text-xs sm:text-sm text-emerald-100/90 max-w-lg mx-auto leading-relaxed">
                        Dokumen sertifikat ini valid dan terdaftar secara sah di pangkalan data kredensial pelatihan <strong>Aether Research Lab Indonesia</strong>.
                    </p>
                </div>

                <!-- Credential Details Table -->
                <div class="px-6 sm:px-8 py-2 space-y-5">
                    
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 flex-wrap gap-2">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nomor Kredensial:</span>
                        <span class="font-mono text-sm font-black text-[#1768B5] bg-sky-50 px-3 py-1 rounded-lg border border-sky-100">
                            {{ $sertifikat->nomor_sertifikat }}
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-slate-400 font-semibold block text-[11px]">Nama Penerima:</span>
                            <p class="text-base font-bold text-slate-900">
                                {{ $sertifikat->nama_pemesan ?: ($sertifikat->user->nm_user ?? 'Peserta Pelatihan') }}
                            </p>
                            @if(!empty($sertifikat->universitas))
                                <p class="text-[11px] text-slate-500">{{ $sertifikat->universitas }}</p>
                            @endif
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-1">
                            <span class="text-slate-400 font-semibold block text-[11px]">Nomor Registrasi:</span>
                            <p class="text-base font-mono font-bold text-slate-900">
                                {{ $sertifikat->nomor_registrasi }}
                            </p>
                            <p class="text-[11px] text-slate-500">ID Peserta Pelatihan #{{ $sertifikat->id }}</p>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 space-y-2 text-xs">
                        <span class="text-slate-400 font-semibold block text-[11px]">Program Pelatihan &amp; Workshop:</span>
                        <p class="text-sm sm:text-base font-bold text-[#102654]">
                            {{ $sertifikat->judul_kelas }}
                        </p>
                        <div class="flex flex-wrap items-center gap-4 text-slate-600 pt-1 text-[11px]">
                            <span class="flex items-center gap-1">
                                <i data-lucide="user-check" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Instruktur: <strong>{{ $sertifikat->instruktur ?: 'Instruktur Ahli Aether' }}</strong></span>
                            </span>
                            <span class="flex items-center gap-1">
                                <i data-lucide="calendar" class="w-3.5 h-3.5 text-slate-400"></i>
                                <span>Tanggal Terbit: <strong>{{ ($sertifikat->updated_at ?? now())->translatedFormat('d F Y') }}</strong></span>
                            </span>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-emerald-50/70 border border-emerald-100 text-xs text-emerald-900 flex items-start gap-3">
                        <i data-lucide="shield-check" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                        <div class="space-y-0.5">
                            <h4 class="font-bold text-emerald-950">Validasi Kelulusan 100% Memenuhi Syarat</h4>
                            <p class="text-emerald-800/90 leading-relaxed text-[11px]">
                                Pemilik sertifikat telah menuntaskan seluruh modul materi kurikulum serta evaluasi praktik dengan capaian kelulusan 100% paripurna.
                            </p>
                        </div>
                    </div>

                    <!-- Action Links -->
                    <div class="pt-4 pb-2 flex flex-col sm:flex-row items-center gap-3">
                        <a href="{{ route('pelatihan.sertifikat.preview', $sertifikat->id) }}" target="_blank" class="w-full sm:flex-1 py-3 px-4 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold text-center transition flex items-center justify-center gap-2 shadow-xs">
                            <i data-lucide="external-link" class="w-4 h-4"></i>
                            <span>Buka Lembar Sertifikat Asli</span>
                        </a>
                        <a href="{{ route('pelatihan.sertifikat.download', $sertifikat->id) }}" class="w-full sm:w-auto py-3 px-5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold text-center transition flex items-center justify-center gap-2">
                            <i data-lucide="download" class="w-4 h-4"></i>
                            <span>Unduh File PDF</span>
                        </a>
                    </div>

                </div>

                <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 text-center text-[11px] text-slate-400">
                    Sistem Verifikasi Kredensial Digital • Diproteksi oleh Digital Signature Aether Lab
                </div>

            </div>
        @elseif($sertifikat && !$isValidCompleted)
            <!-- State Kelas Belum 100% Selesai -->
            <div class="bg-white rounded-3xl border border-amber-200 shadow-xl overflow-hidden space-y-6">
                
                <div class="bg-gradient-to-r from-amber-600 via-amber-700 to-slate-900 p-6 sm:p-8 text-white text-center space-y-3 relative overflow-hidden">
                    <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center mx-auto text-amber-200 border border-white/20 shadow-inner">
                        <i data-lucide="clock" class="w-9 h-9 text-white"></i>
                    </div>

                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/30 text-white text-[11px] font-bold tracking-wider uppercase border border-amber-400/40 backdrop-blur-xs">
                        <span class="w-2 h-2 rounded-full bg-amber-300 animate-pulse"></span>
                        Status: Pembelajaran Belum 100% Selesai
                    </div>

                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
                        E-Sertifikat Belum Memenuhi Syarat
                    </h2>
                    <p class="text-xs sm:text-sm text-amber-100/90 max-w-lg mx-auto leading-relaxed">
                        Data pendaftaran ditemukan, namun peserta baru menyelesaikan <strong>{{ $sertifikat->progress }}%</strong> dari total materi pelatihan. Sertifikat kelulusan hanya akan diterbitkan setelah kelas 100% selesai.
                    </p>
                </div>

                <div class="px-6 sm:px-8 py-4 space-y-4 text-xs">
                    <div class="p-4 rounded-2xl bg-amber-50/70 border border-amber-200 text-amber-900 space-y-1">
                        <h4 class="font-bold text-amber-950">Informasi Progres Belajar:</h4>
                        <p class="text-[11px] text-amber-800">
                            Peserta: <strong>{{ $sertifikat->nama_pemesan ?: ($sertifikat->user->nm_user ?? 'Peserta Pelatihan') }}</strong><br>
                            Program: <strong>{{ $sertifikat->judul_kelas }}</strong><br>
                            Capaian Silabus: <strong>{{ $sertifikat->modul_selesai }} dari {{ $sertifikat->total_modul }} Modul ({{ $sertifikat->progress }}%)</strong>
                        </p>
                    </div>

                    <div class="pt-2 text-center">
                        <a href="{{ route('pelatihan.kelas-saya') }}" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold transition shadow-xs">
                            <i data-lucide="play-circle" class="w-4 h-4"></i>
                            <span>Lanjutkan Belajar di Kelas Saya</span>
                        </a>
                    </div>
                </div>

                <div class="bg-slate-50 px-6 py-4 border-t border-slate-100 text-center text-[11px] text-slate-400">
                    Sistem Verifikasi Kredensial Digital • Aether Research Lab
                </div>
            </div>
        @else
            <!-- State Not Found / Invalid -->
            <div class="bg-white rounded-3xl border border-rose-200 shadow-xl overflow-hidden p-8 sm:p-12 text-center space-y-6">
                <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-500 border border-rose-200 flex items-center justify-center mx-auto shadow-inner">
                    <i data-lucide="alert-octagon" class="w-8 h-8"></i>
                </div>

                <div class="space-y-2 max-w-md mx-auto">
                    <h2 class="text-xl font-bold text-slate-900">
                        Sertifikat Tidak Ditemukan atau Tidak Valid
                    </h2>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Nomor kredensial <span class="font-mono font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded">{{ $identifier ?: 'KOSONG' }}</span> tidak terdaftar dalam database sertifikat resmi Aether Research Lab.
                    </p>
                </div>

                <!-- Form Cari Ulang -->
                <form action="{{ route('pelatihan.sertifikat.verifikasi', '') }}" method="GET" class="max-w-md mx-auto flex gap-2">
                    <input type="text" name="q" value="{{ $identifier }}" placeholder="Masukkan Nomor Registrasi / Sertifikat..." class="flex-1 px-4 py-2.5 rounded-xl border border-slate-300 text-xs focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none">
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1768B5] text-white text-xs font-bold hover:bg-[#102654] transition shrink-0">
                        Verifikasi
                    </button>
                </form>

                <div class="pt-4">
                    <a href="{{ route('pelatihan.index') }}" class="text-xs font-bold text-[#1768B5] hover:underline">
                        &larr; Kembali ke Beranda Pelatihan
                    </a>
                </div>
            </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 text-center text-xs text-slate-400">
        <div class="max-w-5xl mx-auto px-4 space-y-1">
            <p>&copy; {{ date('Y') }} Aether Research Lab Indonesia. All rights reserved.</p>
            <p class="text-[11px] text-slate-400">Official Certification Authority for Academic Research &amp; Data Analysis Workshops.</p>
        </div>
    </footer>

    <script>
        if (window.lucide) {
            lucide.createIcons();
        }
    </script>
</body>
</html>
