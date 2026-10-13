<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voucher {{ $transaksi->nomor_transaksi }} - {{ config('app.name') }}</title>
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
            }
            .voucher-container {
                border: none !important;
                box-shadow: none !important;
                width: 100% !important;
                max-width: 100% !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-gray-100 font-sans p-6 text-gray-800">
    <!-- Top Action Bar -->
    <div class="max-w-3xl mx-auto mb-4 flex items-center justify-between no-print">
        <a href="{{ $transaksi->jenis === 'masuk' ? route('kas-bank-masuk.index') : route('kas-bank-keluar.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
            &larr; Kembali ke Daftar
        </a>
        <div class="flex items-center gap-2">
            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-bold hover:bg-emerald-700 shadow-sm transition">
                Cetak Lembar Voucher
            </button>
        </div>
    </div>

    <!-- Voucher Sheet Container -->
    <div class="voucher-container max-w-3xl mx-auto bg-white rounded-2xl border border-gray-300 shadow-md p-8">
        <!-- Header -->
        <div class="border-b-2 border-gray-800 pb-4 flex items-start justify-between">
            <div class="flex items-center gap-3">
                @php
                    $logoPath = public_path('images/logo_aether.png');
                    $logoSrc = file_exists($logoPath) ? asset('images/logo_aether.png') : asset('images/logo_aether.jpeg');
                @endphp
                <img src="{{ $logoSrc }}" alt="Aether" class="h-12 w-auto object-contain">
                <div>
                    <h2 class="text-lg font-black tracking-tight text-gray-900 uppercase">AETHER ACADEMIC & RESEARCH</h2>
                    <p class="text-[11px] text-gray-500">Pusat Bimbingan Skripsi, Konsultasi Riset & Publikasi Ilmiah</p>
                    <p class="text-[10px] text-gray-400">Jl. Ringroad Utara, Sleman, D.I. Yogyakarta</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $transaksi->jenis === 'masuk' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                    {{ $transaksi->jenis === 'masuk' ? 'BUKTI KAS / BANK MASUK' : 'BUKTI KAS / BANK KELUAR' }}
                </span>
                <p class="text-xs font-mono font-bold text-gray-900 mt-2">No: {{ $transaksi->nomor_transaksi }}</p>
                <p class="text-[11px] text-gray-500">Tanggal: {{ \Carbon\Carbon::parse($transaksi->tanggal)->translatedFormat('d F Y') }}</p>
            </div>
        </div>

        <!-- Meta Details -->
        <div class="grid grid-cols-2 gap-4 my-4 p-3 rounded-xl bg-gray-50 border border-gray-100 text-xs">
            <div>
                <span class="text-gray-400 block text-[10px] uppercase font-bold">
                    {{ $transaksi->jenis === 'masuk' ? 'Diterima Dari' : 'Dibayarkan Kepada' }}:
                </span>
                <p class="font-bold text-gray-900 text-sm mt-0.5">{{ $transaksi->penerima_pembayar ?: '-' }}</p>
            </div>
            <div>
                <span class="text-gray-400 block text-[10px] uppercase font-bold">Metode / No. Referensi:</span>
                <p class="font-bold text-gray-900 mt-0.5">{{ $transaksi->metode_pembayaran ?: 'Transfer Bank' }} {{ $transaksi->nomor_referensi ? '('.$transaksi->nomor_referensi.')' : '' }}</p>
            </div>
        </div>

        <!-- Accounting Entry Table -->
        <div class="my-4">
            <table class="w-full text-xs border border-gray-300">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-300 text-gray-700 font-bold">
                        <th class="p-2 border-r border-gray-300 text-left w-24">Kode Akun</th>
                        <th class="p-2 border-r border-gray-300 text-left">Nama Akun (CoA)</th>
                        <th class="p-2 border-r border-gray-300 text-left">Kategori</th>
                        <th class="p-2 border-r border-gray-300 text-right w-32">Debet (Rp)</th>
                        <th class="p-2 text-right w-32">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody>
                    @if($transaksi->jenis === 'masuk')
                    {{-- Kas Masuk: Debet = Kas/Bank, Kredit = Akun Lawan --}}
                    <tr class="border-b border-gray-200">
                        <td class="p-2 border-r border-gray-300 font-mono font-bold">{{ $transaksi->kasBank->kode_akun ?? '-' }}</td>
                        <td class="p-2 border-r border-gray-300 font-bold text-gray-900">{{ $transaksi->kasBank->nama_akun ?? '-' }}</td>
                        <td class="p-2 border-r border-gray-300 text-gray-500">{{ $transaksi->kasBank->kategori ?? 'ASET LANCAR' }}</td>
                        <td class="p-2 border-r border-gray-300 text-right font-mono font-bold text-emerald-700">{{ number_format($transaksi->nominal, 0, ',', '.') }}</td>
                        <td class="p-2 text-right font-mono text-gray-400">0</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="p-2 border-r border-gray-300 font-mono font-bold pl-4">{{ $transaksi->lawanCoa->kode_akun ?? '-' }}</td>
                        <td class="p-2 border-r border-gray-300 font-bold text-gray-800 pl-4">{{ $transaksi->lawanCoa->nama_akun ?? '-' }}</td>
                        <td class="p-2 border-r border-gray-300 text-gray-500">{{ $transaksi->lawanCoa->kategori ?? '-' }}</td>
                        <td class="p-2 border-r border-gray-300 text-right font-mono text-gray-400">0</td>
                        <td class="p-2 text-right font-mono font-bold text-gray-800">{{ number_format($transaksi->nominal, 0, ',', '.') }}</td>
                    </tr>
                    @else
                    {{-- Kas Keluar: Debet = Beban (Akun Lawan), Kredit = Kas/Bank --}}
                    <tr class="border-b border-gray-200">
                        <td class="p-2 border-r border-gray-300 font-mono font-bold">{{ $transaksi->lawanCoa->kode_akun ?? '-' }}</td>
                        <td class="p-2 border-r border-gray-300 font-bold text-gray-900">{{ $transaksi->lawanCoa->nama_akun ?? '-' }}</td>
                        <td class="p-2 border-r border-gray-300 text-gray-500">{{ $transaksi->lawanCoa->kategori ?? 'BEBAN OPERASIONAL' }}</td>
                        <td class="p-2 border-r border-gray-300 text-right font-mono font-bold text-gray-900">{{ number_format($transaksi->nominal, 0, ',', '.') }}</td>
                        <td class="p-2 text-right font-mono text-gray-400">0</td>
                    </tr>
                    <tr class="border-b border-gray-200">
                        <td class="p-2 border-r border-gray-300 font-mono font-bold pl-4">{{ $transaksi->kasBank->kode_akun ?? '-' }}</td>
                        <td class="p-2 border-r border-gray-300 font-bold text-rose-700 pl-4">{{ $transaksi->kasBank->nama_akun ?? '-' }}</td>
                        <td class="p-2 border-r border-gray-300 text-gray-500">{{ $transaksi->kasBank->kategori ?? 'ASET LANCAR' }}</td>
                        <td class="p-2 border-r border-gray-300 text-right font-mono text-gray-400">0</td>
                        <td class="p-2 text-right font-mono font-bold text-rose-700">{{ number_format($transaksi->nominal, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-black border-t-2 border-gray-400">
                        <td colspan="3" class="p-2 border-r border-gray-300 text-right uppercase">Total:</td>
                        <td class="p-2 border-r border-gray-300 text-right font-mono">Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}</td>
                        <td class="p-2 text-right font-mono">Rp {{ number_format($transaksi->nominal, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Terbilang & Uraian -->
        <div class="space-y-2 my-4 text-xs">
            <div class="p-2.5 rounded-lg bg-gray-50 border border-gray-200">
                <span class="text-gray-400 text-[10px] uppercase font-bold block">Terbilang:</span>
                <p class="font-bold text-gray-900 italic capitalize"># {{ $terbilangText }} #</p>
            </div>
            <div class="p-2.5 rounded-lg bg-gray-50 border border-gray-200">
                <span class="text-gray-400 text-[10px] uppercase font-bold block">Keterangan / Keperluan:</span>
                <p class="text-gray-800">{{ $transaksi->keterangan ?: 'Tidak ada keterangan khusus.' }}</p>
            </div>
        </div>

        <!-- Signature Boxes -->
        <div class="grid grid-cols-4 gap-2 pt-6 text-center text-xs">
            <div>
                <p class="text-[11px] font-bold text-gray-500">Dibuat Oleh,</p>
                <div class="h-16 flex items-end justify-center">
                    <span class="text-[11px] font-bold text-gray-800">{{ $transaksi->user->nm_user ?? 'Admin Keuangan' }}</span>
                </div>
                <div class="border-t border-gray-400 mt-1 mx-2"></div>
                <p class="text-[10px] text-gray-400">Staff Keuangan</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-500">Diperiksa Oleh,</p>
                <div class="h-16 flex items-end justify-center">
                    <span class="text-[11px] font-bold text-gray-800">( ........................ )</span>
                </div>
                <div class="border-t border-gray-400 mt-1 mx-2"></div>
                <p class="text-[10px] text-gray-400">Supervisor / Audit</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-500">Disetujui Oleh,</p>
                <div class="h-16 flex items-end justify-center">
                    <span class="text-[11px] font-bold text-gray-800">( ........................ )</span>
                </div>
                <div class="border-t border-gray-400 mt-1 mx-2"></div>
                <p class="text-[10px] text-gray-400">Manajer Keuangan</p>
            </div>
            <div>
                <p class="text-[11px] font-bold text-gray-500">{{ $transaksi->jenis === 'masuk' ? 'Diterima Dari,' : 'Diterima Oleh,' }}</p>
                <div class="h-16 flex items-end justify-center">
                    <span class="text-[11px] font-bold text-gray-800">{{ $transaksi->penerima_pembayar ?: '( ........................ )' }}</span>
                </div>
                <div class="border-t border-gray-400 mt-1 mx-2"></div>
                <p class="text-[10px] text-gray-400">Klien / Vendor</p>
            </div>
        </div>
    </div>
</body>
</html>
