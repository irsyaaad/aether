@extends('layouts.app')

@section('title', 'Rekap Data Tagihan (Invoice) User - Aether')
@section('page_title', 'Rekap Tagihan (Invoice) User')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#F6C75A] text-xs font-bold mb-2 border border-white/15">
                <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                Data Tagihan ke User (Invoice)
            </div>
            <h1 class="text-2xl font-black">Rekap Data Tagihan (Invoice) User</h1>
            <p class="text-sm text-sky-100 mt-1 max-w-2xl leading-relaxed">
                Rekapitulasi seluruh faktur dan data tagihan pembayaran program bimbingan yang diterbitkan kepada user/mahasiswa.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-white/15 hover:bg-white/25 border border-white/20 transition backdrop-blur-xs shadow-xs">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak Rekap Tagihan</span>
            </button>
            <a href="{{ route('bimbingan.pembayaran.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-md transition">
                <i data-lucide="image" class="w-4 h-4"></i>
                <span>Bukti Bayar User</span>
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Tagihan -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between text-gray-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Total Tagihan</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-gray-900">{{ number_format($summary['total_invoices']) }}</div>
            <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                <span>Nilai Tagihan:</span>
                <span class="font-bold text-gray-800">Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tagihan Lunas -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between text-emerald-600 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Tagihan Lunas</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">{{ number_format($summary['total_lunas_count']) }}</div>
            <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                <span>Terbayar:</span>
                <span class="font-bold text-emerald-700">Rp {{ number_format($summary['total_lunas_nominal'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Belum Dibayar / Menunggu -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between text-amber-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Belum Dibayar</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ number_format($summary['total_pending_count']) }}</div>
            <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                <span>Pending Tagihan:</span>
                <span class="font-bold text-amber-700">Rp {{ number_format($summary['total_pending_nominal'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Rata-rata Tagihan -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between text-purple-600 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Rata-Rata Tagihan</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i data-lucide="pie-chart" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-purple-700">Rp {{ number_format($summary['avg_nominal'], 0, ',', '.') }}</div>
            <span class="text-[11px] text-gray-400">Rata-rata nominal per faktur</span>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs">
        <form method="GET" action="{{ route('bimbingan.invoices.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-gray-700 mb-1">Pencarian Data Tagihan</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari No. Invoice, Nama Mahasiswa, Email, Program..."
                           class="w-full pl-9 pr-3.5 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition">
                </div>
            </div>

            <!-- Status Pembayaran / Tagihan -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Status Tagihan</label>
                <select name="status_pembayaran" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition">
                    <option value="Semua">Semua Status</option>
                    <option value="Menunggu Pembayaran" {{ request('status_pembayaran') === 'Menunggu Pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="Menunggu Verifikasi" {{ request('status_pembayaran') === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Lunas" {{ request('status_pembayaran') === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="Dibatalkan" {{ request('status_pembayaran') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Program -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Paket Program</label>
                <select name="program_id" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition">
                    <option value="Semua">Semua Program</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ (string)request('program_id') === (string)$p->id ? 'selected' : '' }}>
                            {{ Str::limit($p->nama_program, 28) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3.5 bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('bimbingan.invoices.index') }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition flex items-center justify-center" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Data Tagihan &amp; Faktur Pembelian Program</h3>
                <p class="text-xs text-gray-500">Menampilkan {{ $invoices->firstItem() ?? 0 }} - {{ $invoices->lastItem() ?? 0 }} dari total {{ $invoices->total() }} tagihan yang diterbitkan ke user</p>
            </div>
            @if(request()->anyFilled(['search', 'status_pembayaran', 'program_id', 'start_date', 'end_date']))
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200 text-xs font-medium">
                <i data-lucide="info" class="w-3.5 h-3.5"></i>
                Filter Aktif Diterapkan
            </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">No. Tagihan &amp; Tanggal</th>
                        <th class="py-3.5 px-4">Ditagihkan Kepada (User)</th>
                        <th class="py-3.5 px-4">Program Riset</th>
                        <th class="py-3.5 px-4 text-right">Biaya Program</th>
                        <th class="py-3.5 px-4 text-right">Biaya Admin</th>
                        <th class="py-3.5 px-4 text-right">Total Tagihan</th>
                        <th class="py-3.5 px-4 text-center">Bukti Bayar</th>
                        <th class="py-3.5 px-4 text-center">Status Tagihan</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $index => $item)
                    <tr class="hover:bg-sky-50/40 transition">
                        <td class="py-3.5 px-4 text-center text-gray-400 font-medium">
                            {{ $invoices->firstItem() + $index }}
                        </td>
                        <td class="py-3.5 px-4">
                            <a href="{{ route('bimbingan.invoices.show', $item->id) }}" class="font-mono font-bold text-[#1768B5] hover:underline block">
                                {{ $item->nomor_transaksi }}
                            </a>
                            <span class="text-[11px] text-gray-500">
                                {{ $item->tanggal_pembelian ? $item->tanggal_pembelian->translatedFormat('d M Y, H:i') : ($item->created_at ? $item->created_at->translatedFormat('d M Y, H:i') : '-') }} WIB
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-gray-900">{{ $item->nama_pemesan ?: ($item->user->nm_user ?? 'User') }}</div>
                            <div class="text-[11px] text-gray-500">{{ $item->email_pemesan ?: ($item->user->email ?? '-') }}</div>
                            @if($item->universitas)
                            <div class="text-[10px] text-sky-700 font-medium mt-0.5">{{ $item->universitas }}</div>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-semibold text-gray-800 block">{{ $item->nama_program }}</span>
                            <span class="text-[10px] text-gray-500 bg-gray-100 px-1.5 py-0.5 rounded">
                                {{ $item->total_sesi }} Sesi (Sisa {{ $item->sisa_sesi }})
                            </span>
                        </td>
                        <td class="py-3.5 px-4 text-right font-medium text-gray-700">
                            Rp {{ number_format($item->biaya, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-right text-gray-500">
                            Rp {{ number_format($item->admin_fee ?: 5000, 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-gray-900">
                            Rp {{ number_format($item->total_bayar ?: ($item->biaya + 5000), 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($item->bukti_pembayaran_url)
                                <a href="{{ route('bimbingan.pembayaran.index', ['search' => $item->nomor_transaksi]) }}" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-[#1768B5] hover:bg-sky-200 transition">
                                    <i data-lucide="image" class="w-3 h-3"></i>
                                    Ada Bukti
                                </a>
                            @else
                                <span class="text-[10px] text-gray-400">Belum Upload</span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($item->status_pembayaran === 'Lunas')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    Lunas
                                </span>
                            @elseif($item->status_pembayaran === 'Menunggu Verifikasi')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    Menunggu Verifikasi
                                </span>
                            @elseif($item->status_pembayaran === 'Dibatalkan')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700 border border-gray-200">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    <i data-lucide="clock" class="w-3 h-3"></i>
                                    Menunggu Bayar
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('bimbingan.invoices.show', $item->id) }}" class="p-1.5 rounded-lg text-gray-600 hover:text-[#1768B5] hover:bg-sky-50 transition" title="Lihat Detail Tagihan">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('bimbingan.invoices.print', $item->id) }}" target="_blank" class="p-1.5 rounded-lg text-gray-600 hover:text-emerald-600 hover:bg-emerald-50 transition" title="Cetak Faktur Tagihan">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="py-12 text-center text-gray-500">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="receipt" class="w-6 h-6"></i>
                            </div>
                            <p class="font-bold text-gray-700 text-sm">Tidak ada tagihan yang ditemukan</p>
                            <p class="text-xs text-gray-400 mt-0.5">Belum ada data tagihan yang diterbitkan atau tidak cocok dengan filter.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
            {{ $invoices->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
