<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - {{ config('app.name') }}</title>
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
                size: A4 landscape;
                margin: 12mm 10mm 12mm 10mm;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans p-6 text-slate-800 antialiased">
    <!-- Top Action Bar (No Print) -->
    <div class="max-w-6xl mx-auto mb-4 flex flex-wrap items-center justify-between gap-3 no-print">
        <a href="{{ $jenis === 'masuk' ? route('kas-bank-masuk.index') : route('kas-bank-keluar.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs">
            &larr; Kembali ke Daftar
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ $excelUrl }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-lg border border-emerald-300 bg-emerald-50 text-emerald-800 text-xs font-bold hover:bg-emerald-100 transition shadow-xs">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                Export Excel
            </a>
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg {{ $jenis === 'masuk' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-rose-600 hover:bg-rose-700' }} text-white text-xs font-bold shadow-xs transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Cetak Lembar Laporan
            </button>
        </div>
    </div>

    <!-- Printable Report Container -->
    <div class="print-container max-w-6xl mx-auto bg-white rounded-2xl border border-slate-300 shadow-md p-8">
        <!-- Header Kop -->
        <div class="border-b-2 border-slate-900 pb-4 flex items-start justify-between">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/logo_aether.png') }}" alt="Aether" class="h-12 w-auto object-contain">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-slate-900 uppercase">AETHER ACADEMIC & RESEARCH</h2>
                    <p class="text-[11px] text-slate-500 font-medium">Pusat Bimbingan Skripsi, Konsultasi Riset & Publikasi Ilmiah</p>
                    <p class="text-[10px] text-slate-400">Jl. Ringroad Utara, Sleman, D.I. Yogyakarta | Telp: (0274) 889-102</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $jenis === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    {{ $jenis === 'masuk' ? 'REKAPITULASI KAS & BANK MASUK' : 'REKAPITULASI KAS & BANK KELUAR' }}
                </span>
                <p class="text-[11px] text-slate-500 mt-2">Dicetak: <span class="font-bold text-slate-800">{{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</span></p>
                <p class="text-[10px] text-slate-400">Oleh: {{ Auth::user()->name ?? 'Administrator' }}</p>
            </div>
        </div>

        <!-- Filter & Metadata Box -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 my-4 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Periode Transaksi:</span>
                <p class="font-bold text-slate-800 mt-0.5">{{ $meta['periode'] ?? 'Semua Periode' }}</p>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Akun Kas / Bank (CoA):</span>
                <p class="font-bold text-slate-800 mt-0.5">{{ $meta['kas_bank'] ?? 'Semua Akun Kas / Bank' }}</p>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">{{ $jenis === 'masuk' ? 'Akun Lawan (CoA):' : 'Akun Beban (CoA):' }}</span>
                <p class="font-bold text-slate-800 mt-0.5">{{ $meta['lawan_coa'] ?? 'Semua Akun' }}</p>
            </div>
        </div>

        <!-- Data Table -->
        <div class="my-4 overflow-x-auto">
            <table class="w-full text-xs border border-slate-300">
                <thead>
                    <tr class="{{ $jenis === 'masuk' ? 'bg-emerald-50/80 text-emerald-950' : 'bg-rose-50/80 text-rose-950' }} border-b border-slate-300 font-bold">
                        <th class="p-2 border-r border-slate-300 text-center w-10">No</th>
                        <th class="p-2 border-r border-slate-300 text-left w-28">No. Transaksi</th>
                        <th class="p-2 border-r border-slate-300 text-center w-24">Tanggal</th>
                        <th class="p-2 border-r border-slate-300 text-left">
                            {{ $jenis === 'masuk' ? 'Kas / Bank (Debit)' : 'Kas / Bank (Kredit)' }}
                        </th>
                        <th class="p-2 border-r border-slate-300 text-left">
                            {{ $jenis === 'masuk' ? 'Akun Lawan (Kredit)' : 'Akun Beban (Debit)' }}
                        </th>
                        <th class="p-2 border-r border-slate-300 text-left">
                            {{ $jenis === 'masuk' ? 'Diterima Dari' : 'Dibayarkan Kepada' }}
                        </th>
                        <th class="p-2 border-r border-slate-300 text-left w-28">Metode & Ref</th>
                        <th class="p-2 border-r border-slate-300 text-left">Keterangan</th>
                        <th class="p-2 text-right w-32">Nominal (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($transactions as $index => $item)
                    <tr class="hover:bg-slate-50">
                        <td class="p-2 border-r border-slate-200 text-center text-slate-500 font-mono">{{ $index + 1 }}</td>
                        <td class="p-2 border-r border-slate-200 font-mono font-bold text-slate-800">{{ $item->nomor_transaksi }}</td>
                        <td class="p-2 border-r border-slate-200 text-center font-mono">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                        <td class="p-2 border-r border-slate-200">
                            <span class="font-bold text-slate-900">{{ $item->kasBank->nama_akun ?? '-' }}</span>
                            <span class="block text-[10px] font-mono text-slate-500">{{ $item->kasBank->kode_akun ?? '' }}</span>
                        </td>
                        <td class="p-2 border-r border-slate-200">
                            <span class="font-bold text-slate-900">{{ $item->lawanCoa->nama_akun ?? '-' }}</span>
                            <span class="block text-[10px] font-mono text-slate-500">{{ $item->lawanCoa->kode_akun ?? '' }}</span>
                        </td>
                        <td class="p-2 border-r border-slate-200 text-slate-800">{{ $item->penerima_pembayar ?: '-' }}</td>
                        <td class="p-2 border-r border-slate-200">
                            <span class="font-medium text-slate-800">{{ $item->metode_pembayaran ?: '-' }}</span>
                            @if($item->nomor_referensi)
                                <span class="block text-[10px] font-mono text-slate-500">{{ $item->nomor_referensi }}</span>
                            @endif
                        </td>
                        <td class="p-2 border-r border-slate-200 text-slate-700">{{ $item->keterangan ?: '-' }}</td>
                        <td class="p-2 text-right font-mono font-bold {{ $jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                            {{ number_format($item->nominal, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-6 text-center text-slate-400 italic">
                            Tidak ada transaksi kas & bank yang ditemukan untuk filter ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 border-t-2 border-slate-400 font-bold text-slate-900">
                        <td colspan="8" class="p-2.5 text-right uppercase tracking-wider text-xs">
                            Total Rekapitulasi ({{ count($transactions) }} Transaksi):
                        </td>
                        <td class="p-2.5 text-right font-mono text-sm {{ $jenis === 'masuk' ? 'text-emerald-800' : 'text-rose-800' }}">
                            Rp {{ number_format($totalNominal, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Rekapitulasi Per Akun Kas / Bank (CoA) -->
        @if(count($rekapPerAkun) > 0)
        <div class="my-6">
            <h4 class="text-xs font-black uppercase text-slate-700 tracking-wider mb-2">Ringkasan per Akun Kas & Bank:</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2 text-xs">
                @foreach($rekapPerAkun as $rekap)
                <div class="p-2.5 rounded-lg border border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div>
                        <p class="font-bold text-slate-800">{{ $rekap['nama_akun'] }}</p>
                        <span class="text-[10px] font-mono text-slate-500">{{ $rekap['kode_akun'] }} ({{ $rekap['count'] }} transaksi)</span>
                    </div>
                    <div class="text-right font-mono font-bold {{ $jenis === 'masuk' ? 'text-emerald-700' : 'text-rose-700' }}">
                        Rp {{ number_format($rekap['total'], 0, ',', '.') }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Signature Section -->
        <div class="mt-8 pt-4 border-t border-slate-200 grid grid-cols-2 text-xs text-center">
            <div>
                <p class="text-slate-500 mb-14">Dibuat Oleh,</p>
                <p class="font-bold text-slate-900 uppercase underline">{{ Auth::user()->name ?? 'Staff Administrasi' }}</p>
                <p class="text-[10px] text-slate-400">Staff Keuangan & Kasir</p>
            </div>
            <div>
                <p class="text-slate-500 mb-14">Mengetahui & Menyetujui,</p>
                <p class="font-bold text-slate-900 uppercase underline">Finance & Operational Manager</p>
                <p class="text-[10px] text-slate-400">Pimpinan Keuangan Aether</p>
            </div>
        </div>
    </div>
</body>
</html>
