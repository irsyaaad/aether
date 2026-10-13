<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Laba Rugi - {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: white !important;
                padding: 0 !important;
                color: #000 !important;
            }
            .print-container {
                border: none !important;
                box-shadow: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 12mm 10mm 12mm 10mm;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
        }
        .font-mono-num {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans p-6 text-slate-800 antialiased">
    <!-- Top Action Bar (No Print) -->
    <div class="max-w-5xl mx-auto mb-4 flex flex-wrap items-center justify-between gap-3 no-print">
        <a href="{{ route('rugi-laba.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs">
            &larr; Kembali ke Laba Rugi
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ $excelUrl }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-800 text-xs font-bold hover:bg-emerald-100 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Lembar Laporan
            </button>
        </div>
    </div>

    <!-- Printable Report Container -->
    <div class="print-container max-w-5xl mx-auto bg-white rounded-2xl border border-slate-300 shadow-md p-8">
        <!-- Header Kop Surat -->
        <div class="border-b-2 border-slate-900 pb-4 flex items-start justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo_aether.jpeg') }}" alt="Aether" class="h-12 w-auto object-contain rounded-lg border border-slate-100">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-slate-900 uppercase">AETHER ACADEMIC &amp; RESEARCH</h2>
                    <p class="text-[11px] text-slate-500 font-medium">Pusat Bimbingan Skripsi, Konsultasi Riset &amp; Publikasi Ilmiah</p>
                    <p class="text-[10px] text-slate-400">Jl. Ringroad Utara, Sleman, D.I. Yogyakarta | Telp: (0274) 889-102</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                    LAPORAN LABA RUGI
                </span>
                <p class="text-[11px] text-slate-500 mt-2">Dicetak: <span class="font-bold text-slate-800">{{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</span></p>
                <p class="text-[10px] text-slate-400">Oleh: {{ Auth::user()->nm_user ?? Auth::user()->username ?? 'Administrator' }}</p>
            </div>
        </div>

        <!-- Filter & Metadata Box -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 my-4 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Periode Transaksi</span>
                <span class="font-bold text-slate-800">{{ $meta['periode'] }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Hasil Usaha Bersih</span>
                <span class="font-bold {{ $report['is_laba'] ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $report['status_label'] }}
                </span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Nominal Bersih (Net)</span>
                <span class="font-bold text-slate-800 font-mono">Rp {{ number_format($report['laba_bersih'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Report Table -->
        <div class="my-6 space-y-6 text-xs">
            <!-- 1. PENDAPATAN -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="bg-emerald-900 text-white px-4 py-2 flex justify-between font-bold">
                    <span>1. PENDAPATAN (REVENUE)</span>
                    <span class="font-mono">Rp {{ number_format($report['total_pendapatan'], 0, ',', '.') }}</span>
                </div>
                <div class="p-4 space-y-4">
                    @foreach($report['pendapatan']['categories'] as $cat)
                    <div>
                        <div class="flex justify-between font-bold text-slate-700 pb-1 border-b border-slate-200">
                            <span>Kategori: {{ $cat['kategori'] }}</span>
                            <span class="font-mono text-emerald-800">Rp {{ number_format($cat['subtotal'], 0, ',', '.') }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 pl-3">
                            @foreach($cat['accounts'] as $acc)
                            <div class="flex justify-between py-1.5">
                                <span><span class="font-mono text-slate-400">{{ $acc->kode_akun }}</span> - {{ $acc->nama_akun }}</span>
                                <span class="font-mono font-semibold">Rp {{ number_format($acc->nominal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="bg-emerald-50 px-4 py-2 flex justify-between font-black text-emerald-950 border-t border-emerald-200">
                    <span>TOTAL PENDAPATAN</span>
                    <span class="font-mono text-sm">Rp {{ number_format($report['total_pendapatan'], 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- 2. BEBAN OPERASIONAL -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="bg-rose-900 text-white px-4 py-2 flex justify-between font-bold">
                    <span>2. BEBAN OPERASIONAL (EXPENSES)</span>
                    <span class="font-mono">Rp {{ number_format($report['total_beban'], 0, ',', '.') }}</span>
                </div>
                <div class="p-4 space-y-4">
                    @foreach($report['beban']['categories'] as $cat)
                    <div>
                        <div class="flex justify-between font-bold text-slate-700 pb-1 border-b border-slate-200">
                            <span>Kategori: {{ $cat['kategori'] }}</span>
                            <span class="font-mono text-rose-800">Rp {{ number_format($cat['subtotal'], 0, ',', '.') }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 pl-3">
                            @foreach($cat['accounts'] as $acc)
                            <div class="flex justify-between py-1.5">
                                <span><span class="font-mono text-slate-400">{{ $acc->kode_akun }}</span> - {{ $acc->nama_akun }}</span>
                                <span class="font-mono font-semibold">Rp {{ number_format($acc->nominal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="bg-rose-50 px-4 py-2 flex justify-between font-black text-rose-950 border-t border-rose-200">
                    <span>TOTAL BEBAN OPERASIONAL</span>
                    <span class="font-mono text-sm">Rp {{ number_format($report['total_beban'], 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- 3. HASIL AKHIR: LABA / RUGI BERSIH -->
            <div class="p-4 rounded-xl {{ $report['is_laba'] ? 'bg-emerald-100 border border-emerald-300' : 'bg-rose-100 border border-rose-300' }} flex justify-between items-center font-black">
                <div>
                    <span class="text-xs uppercase tracking-wider block {{ $report['is_laba'] ? 'text-emerald-900' : 'text-rose-900' }}">
                        HASIL USAHA PERIODE (NET PROFIT / LOSS)
                    </span>
                    <span class="text-sm {{ $report['is_laba'] ? 'text-emerald-950' : 'text-rose-950' }}">
                        {{ strtoupper($report['status_label']) }}
                    </span>
                </div>
                <span class="text-xl font-mono {{ $report['is_laba'] ? 'text-emerald-900' : 'text-rose-900' }}">
                    Rp {{ number_format($report['laba_bersih'], 0, ',', '.') }}
                </span>
            </div>
        </div>

        <!-- Tanda Tangan Pengesahan -->
        <div class="grid grid-cols-3 gap-6 pt-10 text-center text-xs">
            <div>
                <p class="text-slate-500 mb-14">Dibuat Oleh,</p>
                <div class="border-b border-slate-400 w-36 mx-auto mb-1"></div>
                <p class="font-bold text-slate-900">Staf Keuangan</p>
                <p class="text-[10px] text-slate-400">Aether Research</p>
            </div>
            <div>
                <p class="text-slate-500 mb-14">Diperiksa Oleh,</p>
                <div class="border-b border-slate-400 w-36 mx-auto mb-1"></div>
                <p class="font-bold text-slate-900">Supervisor Keuangan</p>
                <p class="text-[10px] text-slate-400">Verifikator Akuntansi</p>
            </div>
            <div>
                <p class="text-slate-500 mb-14">Disetujui Oleh,</p>
                <div class="border-b border-slate-400 w-36 mx-auto mb-1"></div>
                <p class="font-bold text-slate-900">Direktur Eksekutif</p>
                <p class="text-[10px] text-slate-400">Pimpinan Lembaga</p>
            </div>
        </div>
    </div>
</body>
</html>
