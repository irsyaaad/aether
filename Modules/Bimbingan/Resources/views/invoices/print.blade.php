<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $transaksi->nomor_transaksi }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .print-shadow-none { box-shadow: none !important; border: 1px solid #e5e7eb !important; }
        }
    </style>
</head>
<body class="bg-gray-100 text-gray-900 font-sans p-4 sm:p-8">
    <div class="max-w-2xl mx-auto space-y-4">
        <!-- Print Action Toolbar -->
        <div class="no-print flex items-center justify-between bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <span class="text-xs font-semibold text-gray-600">Tekan tombol cetak untuk mencetak atau simpan sebagai PDF</span>
            <div class="flex items-center gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold rounded-lg shadow-sm transition">
                    Cetak / Print PDF
                </button>
                <button onclick="window.close()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-lg transition">
                    Tutup
                </button>
            </div>
        </div>

        <!-- Official Invoice Sheet -->
        <div class="bg-white p-8 rounded-2xl border border-gray-200 shadow-sm print-shadow-none space-y-6">
            <!-- Header -->
            <div class="flex items-start justify-between pb-6 border-b border-gray-200">
                <div>
                    <h1 class="text-2xl font-black text-[#102654] tracking-tight">AETHER RESEARCH LAB</h1>
                    <p class="text-xs text-gray-500 mt-0.5">Platform Bimbingan &amp; Analisis Riset Skripsi Indonesia</p>
                    <p class="text-[11px] text-gray-400 mt-1">support@aether.test • www.aether.test</p>
                </div>
                <div class="text-right">
                    <span class="text-xs uppercase font-bold text-gray-400 block tracking-widest">OFFICIAL INVOICE</span>
                    <span class="text-lg font-mono font-black text-gray-900">{{ $transaksi->nomor_transaksi }}</span>
                    <div class="mt-1">
                        @if($transaksi->status_pembayaran === 'Lunas')
                            <span class="inline-block px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-bold rounded-full">
                                LUNAS / PAID
                            </span>
                        @else
                            <span class="inline-block px-2.5 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold rounded-full">
                                UNPAID / BELUM LUNAS
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Invoice Info & Bill To -->
            <div class="grid grid-cols-2 gap-6 text-xs">
                <div>
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-1 text-[10px]">Ditagihkan Kepada:</span>
                    <p class="font-bold text-sm text-gray-900">{{ $transaksi->nama_pemesan ?: ($transaksi->user->nm_user ?? 'Mahasiswa') }}</p>
                    <p class="text-gray-600">{{ $transaksi->email_pemesan ?: ($transaksi->user->email ?? '-') }}</p>
                    <p class="text-gray-600 font-mono">{{ $transaksi->telepon_pemesan ?: '-' }}</p>
                    @if($transaksi->universitas)
                    <p class="text-sky-800 font-semibold mt-1">{{ $transaksi->universitas }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <span class="font-bold text-gray-400 uppercase tracking-wider block mb-1 text-[10px]">Rincian Faktur:</span>
                    <p><span class="text-gray-500">Tanggal Transaksi:</span> <span class="font-bold">{{ $transaksi->tanggal_pembelian ? $transaksi->tanggal_pembelian->translatedFormat('d M Y, H:i') : ($transaksi->created_at ? $transaksi->created_at->translatedFormat('d M Y, H:i') : '-') }} WIB</span></p>
                    <p><span class="text-gray-500">Metode Bayar:</span> <span class="font-bold">{{ $transaksi->metode_pembayaran ?: 'Virtual Account' }}</span></p>
                    @if($transaksi->kode_pembayaran)
                    <p><span class="text-gray-500">Kode Bayar:</span> <span class="font-mono font-bold">{{ $transaksi->kode_pembayaran }}</span></p>
                    @endif
                </div>
            </div>

            <!-- Line Items Table -->
            <div class="pt-2">
                <table class="w-full text-left text-xs border border-gray-200">
                    <thead class="bg-gray-50 text-gray-600 font-bold uppercase text-[10px]">
                        <tr class="border-b border-gray-200">
                            <th class="py-2.5 px-3">Deskripsi Item</th>
                            <th class="py-2.5 px-3 text-center w-20">Sesi</th>
                            <th class="py-2.5 px-3 text-right w-32">Harga</th>
                            <th class="py-2.5 px-3 text-right w-32">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr>
                            <td class="py-3 px-3">
                                <div class="font-bold text-gray-900">{{ $transaksi->nama_program }}</div>
                                <div class="text-[11px] text-gray-500">Sesi Konsultasi &amp; Bimbingan Riset Skripsi Bersama Mentor</div>
                            </td>
                            <td class="py-3 px-3 text-center font-bold text-gray-700">{{ $transaksi->total_sesi }}x</td>
                            <td class="py-3 px-3 text-right">Rp {{ number_format($transaksi->biaya, 0, ',', '.') }}</td>
                            <td class="py-3 px-3 text-right font-bold text-gray-900">Rp {{ number_format($transaksi->biaya, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-2.5 px-3 text-gray-600" colspan="3">Biaya Administrasi &amp; Layanan Sistem</td>
                            <td class="py-2.5 px-3 text-right font-medium">Rp {{ number_format($transaksi->admin_fee ?: 5000, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="bg-gray-50 font-black text-sm">
                            <td class="py-3 px-3 uppercase tracking-wide" colspan="3">Total Pembayaran</td>
                            <td class="py-3 px-3 text-right text-base text-gray-900">
                                Rp {{ number_format($transaksi->total_bayar ?: ($transaksi->biaya + 5000), 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer Notes -->
            <div class="pt-6 border-t border-gray-200 flex items-end justify-between text-[11px] text-gray-500">
                <div>
                    <p class="font-bold text-gray-700">Catatan Penting:</p>
                    <p>Faktur ini diterbitkan secara otomatis dan sah sebagai bukti transaksi resmi Aether Data Lab.</p>
                </div>
                <div class="text-right">
                    <p class="italic text-gray-400">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
