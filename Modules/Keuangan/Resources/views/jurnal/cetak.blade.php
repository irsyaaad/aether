<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Jurnal Umum - {{ config('app.name') }}</title>
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
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
        .font-mono-num {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
        }
    </style>
</head>
<body class="bg-slate-100 font-sans p-6 text-slate-800 antialiased">
    <!-- Top Action Bar (No Print) -->
    <div class="max-w-5xl mx-auto mb-4 flex flex-wrap items-center justify-between gap-3 no-print">
        <a href="{{ route('jurnal.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-300 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition shadow-xs">
            &larr; Kembali ke Daftar Jurnal
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
                    <h2 class="text-lg font-black tracking-tight text-slate-900 uppercase">AETHER ACADEMIC & RESEARCH</h2>
                    <p class="text-[11px] text-slate-500 font-medium">Pusat Bimbingan Skripsi, Konsultasi Riset & Publikasi Ilmiah</p>
                    <p class="text-[10px] text-slate-400">Jl. Ringroad Utara, Sleman, D.I. Yogyakarta | Telp: (0274) 889-102</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-blue-100 text-blue-800">
                    LAPORAN JURNAL UMUM
                </span>
                <p class="text-[11px] text-slate-500 mt-2">Dicetak: <span class="font-bold text-slate-800">{{ \Carbon\Carbon::now()->translatedFormat('d F Y H:i') }} WIB</span></p>
                <p class="text-[10px] text-slate-400">Oleh: {{ Auth::user()->name ?? 'Administrator' }}</p>
            </div>
        </div>

        <!-- Filter & Metadata Box -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 my-4 p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs">
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Periode Transaksi:</span>
                <p class="font-bold text-slate-800 mt-0.5">{{ $meta['periode'] }}</p>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Total Transaksi / Baris:</span>
                <p class="font-bold text-slate-800 mt-0.5">{{ $summary->total_transaksi }} Transaksi ({{ $summary->total_baris }} Baris Entri)</p>
            </div>
            <div>
                <span class="text-slate-400 block text-[10px] uppercase font-bold">Status Keseimbangan:</span>
                <p class="font-bold {{ $summary->is_balanced ? 'text-emerald-700' : 'text-rose-700' }} mt-0.5">
                    {{ $summary->status_label }}
                </p>
            </div>
        </div>

        <!-- Journal Table -->
        <table class="w-full text-left text-xs border border-slate-300 mt-4 border-collapse">
            <thead>
                <tr class="bg-slate-900 text-white font-bold border-b border-slate-900">
                    <th class="py-2.5 px-2 text-center w-10 border-r border-slate-700">No</th>
                    <th class="py-2.5 px-2 border-r border-slate-700">Tanggal, No. Bukti &amp; Keterangan</th>
                    <th class="py-2.5 px-2 w-60 border-r border-slate-700">Akun (CoA)</th>
                    <th class="py-2.5 px-2 text-center w-16 border-r border-slate-700">Posisi</th>
                    <th class="py-2.5 px-2 text-right w-32 border-r border-slate-700">Debit (Rp)</th>
                    <th class="py-2.5 px-2 text-right w-32">Kredit (Rp)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @forelse($entries as $index => $row)
                    @php
                        $isDebit = ($row->posisi === 'Debit');
                        $isFirstRowOfTx = ($index === 0 || $entries[$index - 1]->transaksi_id !== $row->transaksi_id);
                    @endphp
                    <tr class="{{ $isFirstRowOfTx ? 'border-t border-slate-300' : '' }}">
                        <td class="py-2 px-2 text-center text-slate-400 border-r border-slate-200 align-top">
                            {{ $isFirstRowOfTx ? $loop->iteration : '' }}
                        </td>
                        <td class="py-2 px-2 border-r border-slate-200 text-slate-700 align-top">
                            @if($isFirstRowOfTx)
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</span>
                                    <span class="font-mono font-bold text-slate-800 text-[11px] px-1.5 py-0.2 rounded bg-slate-100 border border-slate-300">{{ $row->nomor_transaksi }}</span>
                                </div>
                                <div class="font-semibold text-slate-800">{{ $row->keterangan ?: '-' }}</div>
                                @if($row->penerima_pembayar || $row->nomor_referensi)
                                    <div class="text-[10px] text-slate-500 mt-0.5">
                                        @if($row->penerima_pembayar) Pihak: {{ $row->penerima_pembayar }} @endif
                                        @if($row->nomor_referensi) &bull; Ref: {{ $row->nomor_referensi }} @endif
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td class="py-2 px-2 border-r border-slate-200 whitespace-nowrap align-top">
                            @if($isDebit)
                                <span class="font-bold text-slate-900 block">{{ $row->nama_akun }}</span>
                                <span class="font-mono text-[10px] text-slate-500">{{ $row->kode_akun }} &bull; {{ $row->kategori_coa }}</span>
                            @else
                                <span class="pl-4 text-slate-700 italic block font-medium">&bull; {{ $row->nama_akun }}</span>
                                <span class="pl-4 font-mono text-[10px] text-slate-400 block">{{ $row->kode_akun }} &bull; {{ $row->kategori_coa }}</span>
                            @endif
                        </td>
                        <td class="py-2 px-2 text-center border-r border-slate-200 text-[10px] font-bold text-slate-500 uppercase align-top">
                            {{ $row->posisi }}
                        </td>
                        <td class="py-2 px-2 text-right border-r border-slate-200 font-mono-num font-semibold text-slate-800 whitespace-nowrap align-top">
                            {{ (float) $row->debit > 0 ? number_format($row->debit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-2 px-2 text-right font-mono-num font-semibold text-slate-800 whitespace-nowrap align-top">
                            {{ (float) $row->kredit > 0 ? number_format($row->kredit, 0, ',', '.') : '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400">
                            Tidak ada data jurnal pada periode ini
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="bg-slate-100 font-bold border-t-2 border-slate-800 text-slate-900">
                    <td colspan="4" class="py-2.5 px-3 text-right uppercase border-r border-slate-300">
                        TOTAL JURNAL UMUM:
                    </td>
                    <td class="py-2.5 px-3 text-right font-mono-num text-sm border-r border-slate-300">
                        Rp {{ number_format($summary->total_debit, 0, ',', '.') }}
                    </td>
                    <td class="py-2.5 px-3 text-right font-mono-num text-sm">
                        Rp {{ number_format($summary->total_kredit, 0, ',', '.') }}
                    </td>
                </tr>
                <tr class="bg-slate-50 border-t border-slate-300 text-xs font-bold text-slate-800">
                    <td colspan="4" class="py-1.5 px-3 text-right uppercase border-r border-slate-300">
                        STATUS KESEIMBANGAN:
                    </td>
                    <td colspan="2" class="py-1.5 px-3 text-center {{ $summary->is_balanced ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $summary->status_label }}
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Signature Section -->
        <div class="grid grid-cols-3 gap-6 mt-12 text-center text-xs">
            <div>
                <p class="text-slate-500">Dibuat Oleh,</p>
                <p class="font-bold text-slate-700 mt-1">Staf Keuangan</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900 underline">{{ Auth::user()->name ?? 'Staf Administrasi' }}</p>
            </div>
            <div>
                <p class="text-slate-500">Diperiksa Oleh,</p>
                <p class="font-bold text-slate-700 mt-1">Supervisor Keuangan</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900 underline">( ........................................ )</p>
            </div>
            <div>
                <p class="text-slate-500">Mengetahui,</p>
                <p class="font-bold text-slate-700 mt-1">Pimpinan / Direktur</p>
                <div class="h-16"></div>
                <p class="font-bold text-slate-900 underline">( ........................................ )</p>
            </div>
        </div>
    </div>
</body>
</html>
