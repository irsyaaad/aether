<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sertifikat Kelulusan - {{ $sertifikat->nomor_sertifikat }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { 
                background: white !important; 
                padding: 0 !important;
                -webkit-print-color-adjust: exact !important; 
                print-color-adjust: exact !important; 
            }
            .cert-canvas {
                box-shadow: none !important;
                border: 4px double #102654 !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
            }
            @page {
                size: A4 landscape;
                margin: 8mm;
            }
        }
    </style>
</head>
<body class="bg-slate-900 min-h-screen text-slate-800 font-sans p-4 sm:p-8 flex flex-col items-center justify-center">

    <!-- Top Action Toolbar (No Print) -->
    <div class="no-print w-full max-w-4xl bg-slate-800/90 backdrop-blur-md border border-slate-700 p-4 rounded-2xl shadow-xl flex flex-wrap items-center justify-between gap-3 mb-6">
        <div class="flex items-center gap-3">
            <a href="{{ route('pelatihan.sertifikat') }}" class="p-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-300 transition flex items-center justify-center">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
            </a>
            <div>
                <h4 class="text-xs sm:text-sm font-bold text-white flex items-center gap-1.5">
                    <span>E-Sertifikat Kelulusan Resmi</span>
                    <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] border border-emerald-500/30 font-mono">VALID</span>
                </h4>
                <p class="text-[11px] text-slate-400 font-mono">{{ $sertifikat->nomor_sertifikat }}</p>
            </div>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('pelatihan.sertifikat.download', $sertifikat->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] text-white text-xs font-bold transition shadow-sm">
                <i data-lucide="download" class="w-3.5 h-3.5"></i>
                <span>Unduh File PDF</span>
            </a>
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-700 hover:bg-slate-600 text-slate-200 border border-slate-600 text-xs font-semibold transition">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Cetak / Print PDF</span>
            </button>
            <a href="{{ route('pelatihan.sertifikat') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 text-xs font-medium transition">
                Tutup
            </a>
        </div>
    </div>

    <!-- Official Certificate Sheet (Landscape Canvas) -->
    <div class="cert-canvas w-full max-w-4xl bg-white rounded-3xl p-4 sm:p-6 shadow-2xl border-4 border-double border-[#102654] relative overflow-hidden">
        
        <!-- Corner Ornaments -->
        <div class="absolute top-2 left-2 w-12 h-12 border-t-2 border-l-2 border-[#C5A059]"></div>
        <div class="absolute top-2 right-2 w-12 h-12 border-t-2 border-r-2 border-[#C5A059]"></div>
        <div class="absolute bottom-2 left-2 w-12 h-12 border-b-2 border-l-2 border-[#C5A059]"></div>
        <div class="absolute bottom-2 right-2 w-12 h-12 border-b-2 border-r-2 border-[#C5A059]"></div>

        <!-- Inner Border Frame -->
        <div class="border-2 border-[#C5A059] p-8 sm:p-12 text-center rounded-2xl relative">
            
            <!-- Watermark / Background Crest Accent -->
            <div class="absolute inset-0 flex items-center justify-center opacity-5 pointer-events-none">
                <i data-lucide="award" class="w-96 h-96 text-[#102654]"></i>
            </div>

            <!-- Top Header -->
            <div class="relative z-10 space-y-1">
                <span class="text-[10px] sm:text-xs font-black uppercase tracking-[0.25em] text-[#C5A059] block">
                    Aether Academic &amp; Research Excellence
                </span>
                <h2 class="text-xl sm:text-2xl font-black text-[#102654] uppercase tracking-wider">
                    AETHER RESEARCH LAB INDONESIA
                </h2>
                <div class="w-24 h-0.5 bg-gradient-to-r from-transparent via-[#C5A059] to-transparent mx-auto mt-2"></div>
            </div>

            <!-- Certificate Title -->
            <div class="relative z-10 my-6 sm:my-8 space-y-1">
                <h1 class="text-3xl sm:text-4xl font-serif font-black text-[#102654] tracking-tight">
                    SERTIFIKAT KELULUSAN
                </h1>
                <p class="text-xs sm:text-sm font-semibold text-slate-500 uppercase tracking-[0.2em]">
                    Certificate of Completion
                </p>
                <div class="inline-block mt-1 px-3 py-1 bg-slate-100 rounded-lg text-slate-600 font-mono text-[11px] font-bold">
                    No. Kredensial: {{ $sertifikat->nomor_sertifikat }}
                </div>
            </div>

            <!-- Recipient Section -->
            <div class="relative z-10 my-6 space-y-2">
                <p class="text-xs sm:text-sm text-slate-500 italic">
                    Diberikan dengan penuh kehormatan kepada:
                </p>
                <div class="text-2xl sm:text-4xl font-serif font-bold text-[#1768B5] tracking-tight decoration-[#C5A059] underline underline-offset-8">
                    {{ $sertifikat->nama_pemesan ?: ($sertifikat->user->nm_user ?? 'Peserta Pelatihan') }}
                </div>
                @if(!empty($sertifikat->universitas))
                    <p class="text-xs text-slate-600 font-medium pt-1">
                        {{ $sertifikat->universitas }}
                    </p>
                @endif
            </div>

            <!-- Statement Description -->
            <div class="relative z-10 max-w-2xl mx-auto my-6 space-y-2">
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                    Telah berhasil menyelesaikan secara tuntas seluruh silabus materi, evaluasi kompetensi metodologi riset, dan praktik analisis data pada program workshop:
                </p>
                <div class="text-base sm:text-lg font-bold text-[#102654] px-4 py-1.5 bg-amber-50/60 rounded-xl inline-block border border-amber-200/60">
                    &ldquo;{{ $sertifikat->judul_kelas }}&rdquo;
                </div>
            </div>

            <!-- Footer Signatures & Credential Badge -->
            <div class="relative z-10 pt-8 mt-6 border-t border-slate-200/80 grid grid-cols-1 sm:grid-cols-3 gap-6 items-end">
                <!-- Instruktur -->
                <div class="space-y-1">
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Instruktur Utama</p>
                    <div class="h-12 flex items-center justify-center">
                        <span class="font-serif italic text-lg text-slate-700">Signed</span>
                    </div>
                    <p class="text-xs font-bold text-[#102654] border-t border-[#C5A059] pt-1">
                        {{ $sertifikat->instruktur ?: 'Instruktur Ahli Aether' }}
                    </p>
                    <p class="text-[10px] text-slate-500">Lead Research Specialist</p>
                </div>

                <!-- QR Code Verifikasi Kredensial (Menggantikan Badge Valid) -->
                <div class="space-y-1.5 flex flex-col items-center">
                    <a href="{{ $sertifikat->verify_url }}" target="_blank" title="Klik untuk membuka verifikasi keaslian sertifikat" class="p-2 bg-white rounded-2xl border border-slate-200 shadow-xs hover:border-[#1768B5] hover:shadow-md transition block group">
                        <div class="w-20 h-20 flex items-center justify-center overflow-hidden">
                            {!! SimpleSoftwareIO\QrCode\Facades\QrCode::size(80)->color(16, 38, 84)->generate($sertifikat->verify_url) !!}
                        </div>
                    </a>
                    <div class="text-[10px] text-slate-500 font-mono text-center leading-tight">
                        <span class="text-[#1768B5] font-bold block uppercase tracking-wider text-[9px]">Pindai untuk Verifikasi</span>
                        {{ $sertifikat->nomor_registrasi }}
                    </div>
                </div>

                <!-- Direktur -->
                <div class="space-y-1">
                    <p class="text-[10px] uppercase tracking-wider text-slate-400 font-bold">Direktur Pelatihan &amp; Riset</p>
                    <div class="h-12 flex items-center justify-center">
                        <span class="font-serif italic text-lg text-slate-700">Verified</span>
                    </div>
                    <p class="text-xs font-bold text-[#102654] border-t border-[#C5A059] pt-1">
                        Dr. M. Irsyadul Anam, M.Kom.
                    </p>
                    <p class="text-[10px] text-slate-500">Director of Aether Research Lab</p>
                </div>
            </div>

        </div>
    </div>

    <script>
        if (window.lucide) {
            lucide.createIcons();
        }
    </script>
</body>
</html>
