<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas (Cash Flow) - {{ config('app.name') }}</title>
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
        <a href="{{ route('cashflow.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs">
            &larr; Kembali ke Arus Kas
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ $excelUrl }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-800 text-xs font-bold hover:bg-emerald-100 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-xs transition">
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
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-blue-100 text-blue-800">
                    LAPORAN ARUS KAS
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
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Perubahan Bersih Kas</span>
                <span class="font-bold font-mono text-slate-800">Rp {{ number_format($report['rekapitulasi']['kenaikan_bersih'], 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Saldo Akhir Kas &amp; Bank</span>
                <span class="font-bold font-mono text-blue-700">Rp {{ number_format($report['rekapitulasi']['saldo_akhir_kas'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Report Table -->
        <div class="my-6 space-y-6 text-xs">
            <!-- 1. OPERASIONAL -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="bg-blue-900 text-white px-4 py-2 flex justify-between font-bold">
                    <span>1. ARUS KAS DARI AKTIVITAS OPERASIONAL</span>
                    <span class="font-mono">Net: Rp {{ number_format($report['operasional']['net_operasional'], 0, ',', '.') }}</span>
                </div>
                <div class="p-4 space-y-3">
                    <div>
                        <span class="font-bold text-emerald-800 block mb-1">A. Penerimaan Kas dari Operasional:</span>
                        <div class="divide-y divide-slate-100 pl-3">
                            @forelse($report['operasional']['inflows'] as $item)
                            <div class="flex justify-between py-1">
                                <span><span class="font-mono text-slate-400">{{ $item['kode_akun'] }}</span> - {{ $item['nama_akun'] }} ({{ $item['kategori'] }})</span>
                                <span class="font-mono font-semibold text-emerald-800">Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                            </div>
                            @empty
                            <p class="py-1 text-slate-400 italic">Tidak ada penerimaan operasional.</p>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <span class="font-bold text-rose-800 block mb-1">B. Pengeluaran Kas untuk Operasional:</span>
                        <div class="divide-y divide-slate-100 pl-3">
                            @forelse($report['operasional']['outflows'] as $item)
                            <div class="flex justify-between py-1">
                                <span><span class="font-mono text-slate-400">{{ $item['kode_akun'] }}</span> - {{ $item['nama_akun'] }} ({{ $item['kategori'] }})</span>
                                <span class="font-mono font-semibold text-rose-800">-Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                            </div>
                            @empty
                            <p class="py-1 text-slate-400 italic">Tidak ada pengeluaran operasional.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="bg-blue-50 px-4 py-2 flex justify-between font-black text-blue-950 border-t border-blue-200">
                    <span>ARUS KAS BERSIH DARI AKTIVITAS OPERASIONAL</span>
                    <span class="font-mono text-sm">Rp {{ number_format($report['operasional']['net_operasional'], 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- 2. INVESTASI -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="bg-slate-800 text-white px-4 py-2 flex justify-between font-bold">
                    <span>2. ARUS KAS DARI AKTIVITAS INVESTASI</span>
                    <span class="font-mono">Net: Rp {{ number_format($report['investasi']['net_investasi'], 0, ',', '.') }}</span>
                </div>
                <div class="p-4 space-y-1">
                    @if(empty($report['investasi']['inflows']) && empty($report['investasi']['outflows']))
                    <p class="text-slate-400 italic">Tidak ada transaksi arus kas investasi.</p>
                    @else
                        @foreach($report['investasi']['inflows'] as $item)
                        <div class="flex justify-between py-1 pl-3">
                            <span>Penjualan {{ $item['nama_akun'] }}</span>
                            <span class="font-mono font-semibold text-emerald-800">Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        @foreach($report['investasi']['outflows'] as $item)
                        <div class="flex justify-between py-1 pl-3">
                            <span>Perolehan {{ $item['nama_akun'] }}</span>
                            <span class="font-mono font-semibold text-rose-800">-Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    @endif
                </div>
                <div class="bg-slate-100 px-4 py-2 flex justify-between font-black text-slate-950 border-t border-slate-200">
                    <span>ARUS KAS BERSIH DARI AKTIVITAS INVESTASI</span>
                    <span class="font-mono text-sm">Rp {{ number_format($report['investasi']['net_investasi'], 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- 3. PENDANAAN -->
            <div class="border border-slate-200 rounded-xl overflow-hidden">
                <div class="bg-slate-800 text-white px-4 py-2 flex justify-between font-bold">
                    <span>3. ARUS KAS DARI AKTIVITAS PENDANAAN</span>
                    <span class="font-mono">Net: Rp {{ number_format($report['pendanaan']['net_pendanaan'], 0, ',', '.') }}</span>
                </div>
                <div class="p-4 space-y-1">
                    @if(empty($report['pendanaan']['inflows']) && empty($report['pendanaan']['outflows']))
                    <p class="text-slate-400 italic">Tidak ada transaksi arus kas pendanaan.</p>
                    @else
                        @foreach($report['pendanaan']['inflows'] as $item)
                        <div class="flex justify-between py-1 pl-3">
                            <span>Penerimaan {{ $item['nama_akun'] }}</span>
                            <span class="font-mono font-semibold text-emerald-800">Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                        @foreach($report['pendanaan']['outflows'] as $item)
                        <div class="flex justify-between py-1 pl-3">
                            <span>Penarikan {{ $item['nama_akun'] }}</span>
                            <span class="font-mono font-semibold text-rose-800">-Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    @endif
                </div>
                <div class="bg-slate-100 px-4 py-2 flex justify-between font-black text-slate-950 border-t border-slate-200">
                    <span>ARUS KAS BERSIH DARI AKTIVITAS PENDANAAN</span>
                    <span class="font-mono text-sm">Rp {{ number_format($report['pendanaan']['net_pendanaan'], 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- 4. REKAPITULASI DAN SALDO KAS AKHIR -->
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-300 space-y-2">
                <div class="flex justify-between font-bold text-slate-700 pb-1 border-b border-slate-200">
                    <span>KENAIKAN / (PENURUNAN) BERSIH KAS &amp; BANK</span>
                    <span class="font-mono">Rp {{ number_format($report['rekapitulasi']['kenaikan_bersih'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-bold text-slate-700 pb-1 border-b border-slate-200">
                    <span>SALDO AWAL KAS &amp; BANK</span>
                    <span class="font-mono">Rp {{ number_format($report['rekapitulasi']['saldo_awal_kas'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between font-black text-blue-950 pt-1 text-sm bg-blue-100/60 p-2 rounded">
                    <span>SALDO AKHIR KAS &amp; BANK</span>
                    <span class="font-mono">Rp {{ number_format($report['rekapitulasi']['saldo_akhir_kas'], 0, ',', '.') }}</span>
                </div>
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
