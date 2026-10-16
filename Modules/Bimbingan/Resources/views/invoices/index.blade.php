@extends('layouts.app')

@section('title', 'Rekap Data Tagihan (Invoice) User - Aether')
@section('page_title', 'Rekap Tagihan (Invoice) User')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] rounded-2xl p-6 text-white shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#F6C75A] text-xs font-bold mb-2 border border-white/15 backdrop-blur-xs">
                <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                Data Tagihan ke User (Invoice)
            </div>
            <h1 class="text-2xl font-black tracking-tight">Rekap Data Tagihan (Invoice) User</h1>
            <p class="text-sm text-sky-100 mt-1 max-w-2xl leading-relaxed">
                Rekapitulasi seluruh faktur dan data tagihan pembayaran dari Bimbingan Skripsi, Olah Data Riset, dan Kelas &amp; Workshop yang diterbitkan kepada user/mahasiswa.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-white/15 hover:bg-white/25 border border-white/20 transition backdrop-blur-xs shadow-xs">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak Rekap Tagihan</span>
            </button>
            <a href="{{ route('bimbingan.pembayaran.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-sm transition">
                <i data-lucide="image" class="w-4 h-4"></i>
                <span>Bukti Bayar User</span>
            </a>
        </div>
    </div>

    <!-- Summary Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Tagihan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-xs transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Tagihan</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($summary['total_invoices']) }}</div>
            <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                <span>Nilai Tagihan:</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Tagihan Lunas -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-xs transition">
            <div class="flex items-center justify-between text-emerald-600 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Tagihan Lunas</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600 tracking-tight">{{ number_format($summary['total_lunas_count']) }}</div>
            <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                <span>Terbayar:</span>
                <span class="font-bold text-emerald-700">Rp {{ number_format($summary['total_lunas_nominal'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Belum Dibayar / Menunggu -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-xs transition">
            <div class="flex items-center justify-between text-amber-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Belum Dibayar</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600 tracking-tight">{{ number_format($summary['total_pending_count']) }}</div>
            <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                <span>Pending Tagihan:</span>
                <span class="font-bold text-amber-700">Rp {{ number_format($summary['total_pending_nominal'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Rata-rata Tagihan -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-xs transition">
            <div class="flex items-center justify-between text-purple-600 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Rata-Rata Tagihan</span>
                <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                    <i data-lucide="pie-chart" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-purple-700 tracking-tight">Rp {{ number_format($summary['avg_nominal'], 0, ',', '.') }}</div>
            <span class="text-[11px] text-slate-400">Rata-rata nominal per faktur</span>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-2xs">
        <form method="GET" action="{{ route('bimbingan.invoices.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 items-end">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-700 mb-1">Pencarian Data Tagihan</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari No. Invoice, Mahasiswa, Email, Layanan..."
                           class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition placeholder:text-slate-400">
                </div>
            </div>

            <!-- Filter: Jenis Program / Layanan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Program</label>
                <select name="jenis" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition font-medium text-slate-700">
                    <option value="Semua">Semua Program</option>
                    <option value="bimbingan" {{ request('jenis') === 'bimbingan' ? 'selected' : '' }}>🎓 Bimbingan Skripsi</option>
                    <option value="olahdata" {{ request('jenis') === 'olahdata' ? 'selected' : '' }}>📊 Olah Data</option>
                    <option value="pelatihan" {{ request('jenis') === 'pelatihan' ? 'selected' : '' }}>🏅 Kelas &amp; Workshop</option>
                </select>
            </div>

            <!-- Status Pembayaran / Tagihan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status Tagihan</label>
                <select name="status_pembayaran" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition text-slate-700">
                    <option value="Semua">Semua Status</option>
                    <option value="Menunggu Pembayaran" {{ request('status_pembayaran') === 'Menunggu Pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="Menunggu Verifikasi" {{ request('status_pembayaran') === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Lunas" {{ request('status_pembayaran') === 'Lunas' ? 'selected' : '' }}>Lunas</option>
                    <option value="Ditolak" {{ request('status_pembayaran') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                    <option value="Dibatalkan" {{ request('status_pembayaran') === 'Dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>

            <!-- Paket Bimbingan (Spesifik) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Paket Bimbingan</label>
                <select name="program_id" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition text-slate-700">
                    <option value="Semua">Semua Paket</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" {{ (string)request('program_id') === (string)$p->id ? 'selected' : '' }}>
                            {{ Str::limit($p->nama_program, 22) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Hidden Per Page Sync -->
            <input type="hidden" name="per_page" id="invoices_per_page_input" value="{{ request('per_page', 10) }}">

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3.5 bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('bimbingan.invoices.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition flex items-center justify-center" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="px-6 py-4.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/40">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">Data Tagihan &amp; Faktur Pembelian Program</h3>
                <p class="text-xs text-slate-500 mt-0.5">Menampilkan <span class="font-bold text-slate-700">{{ $invoices->firstItem() ?? 0 }} - {{ $invoices->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-slate-700">{{ $invoices->total() }}</span> tagihan yang diterbitkan ke user</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                @if(request()->anyFilled(['search', 'jenis', 'status_pembayaran', 'program_id', 'start_date', 'end_date']))
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200 text-xs font-bold">
                    <i data-lucide="filter" class="w-3.5 h-3.5 text-sky-600"></i>
                    Filter Aktif Diterapkan
                </span>
                @endif
                <div class="inline-flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-slate-200 text-xs text-slate-600 shadow-2xs">
                    <span class="text-slate-500 font-medium text-[11px]">Tampilkan:</span>
                    <select onchange="changePerPage(this.value)" class="text-xs font-bold text-slate-800 bg-transparent outline-none cursor-pointer">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 baris</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25 baris</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50 baris</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100 baris</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/90 border-b border-slate-200/80 text-slate-500 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-4 px-4 w-12 text-center">No</th>
                        <th class="py-4 px-4 min-w-[170px]">No. Tagihan &amp; Tanggal</th>
                        <th class="py-4 px-4 min-w-[200px]">Ditagihkan Kepada (User)</th>
                        <th class="py-4 px-4 min-w-[240px]">Program / Layanan</th>
                        <th class="py-4 px-4 min-w-[150px] text-right">Total Tagihan</th>
                        <th class="py-4 px-4 text-center min-w-[110px]">Bukti Bayar</th>
                        <th class="py-4 px-4 text-center min-w-[130px]">Status Tagihan</th>
                        <th class="py-4 px-4 text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($invoices as $index => $item)
                    <tr class="hover:bg-sky-50/30 transition-colors duration-150">
                        <td class="py-4 px-4 text-center text-slate-400 font-medium text-xs">
                            {{ $invoices->firstItem() + $index }}
                        </td>
                        <td class="py-4 px-4">
                            <a href="{{ route('bimbingan.invoices.show', ['id' => $item->id, 'jenis' => $item->jenis]) }}" class="inline-flex items-center gap-1 font-mono font-bold text-xs text-[#1768B5] hover:text-[#102654] hover:underline">
                                <span>{{ $item->nomor_transaksi }}</span>
                            </a>
                            <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5">
                                <i data-lucide="calendar" class="w-3 h-3 text-slate-400"></i>
                                <span>{{ $item->tanggal_pembelian ? $item->tanggal_pembelian->translatedFormat('d M Y, H:i') : ($item->created_at ? $item->created_at->translatedFormat('d M Y, H:i') : '-') }} WIB</span>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-[#1768B5]/15 to-[#102654]/20 text-[#1768B5] font-black text-xs flex items-center justify-center shrink-0 border border-[#1768B5]/20">
                                    {{ strtoupper(substr($item->nama_pemesan ?: ($item->user->nm_user ?? 'U'), 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-900 truncate leading-snug">{{ $item->nama_pemesan ?: ($item->user->nm_user ?? 'User') }}</div>
                                    <div class="text-[11px] text-slate-500 truncate flex items-center gap-1">
                                        <i data-lucide="mail" class="w-3 h-3 text-slate-400 shrink-0"></i>
                                        <span>{{ $item->email_pemesan ?: ($item->user->email ?? '-') }}</span>
                                    </div>
                                    @if($item->universitas && $item->universitas !== '-')
                                    <span class="inline-block text-[10px] text-sky-700 bg-sky-50 px-1.5 py-0.2 rounded border border-sky-100 font-medium mt-0.5 max-w-[200px] truncate">
                                        {{ $item->universitas }}
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <!-- Category Chip -->
                            <div class="mb-1">
                                @if($item->jenis === 'bimbingan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                        <i data-lucide="graduation-cap" class="w-3 h-3"></i>
                                        Bimbingan
                                    </span>
                                @elseif($item->jenis === 'olahdata')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                        <i data-lucide="bar-chart-2" class="w-3 h-3"></i>
                                        Olah Data
                                    </span>
                                @elseif($item->jenis === 'pelatihan')
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-50 text-purple-700 border border-purple-200">
                                        <i data-lucide="award" class="w-3 h-3"></i>
                                        Kelas &amp; Workshop
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $item->jenis_label }}
                                    </span>
                                @endif
                            </div>
                            <span class="font-bold text-slate-800 text-xs block leading-snug">{{ $item->nama_program }}</span>
                            <span class="text-[11px] text-slate-500 font-medium mt-0.5 block">
                                {{ $item->detail_layanan }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="font-black text-slate-900 text-sm tracking-tight">
                                Rp {{ number_format($item->total_bayar ?: ($item->biaya + 5000), 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                Layanan: Rp {{ number_format($item->biaya, 0, ',', '.') }} + Admin: Rp {{ number_format($item->admin_fee ?: 5000, 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="py-4 px-4 text-center">
                            @if($item->has_bukti)
                                <a href="{{ route('bimbingan.pembayaran.index', ['search' => $item->nomor_transaksi, 'jenis' => $item->jenis]) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 hover:bg-sky-100 border border-sky-200 transition">
                                    <i data-lucide="image" class="w-3 h-3 text-[#1768B5]"></i>
                                    <span>Ada Bukti</span>
                                </a>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-medium text-slate-400 bg-slate-100 border border-slate-200">
                                    <i data-lucide="clock" class="w-3 h-3 text-slate-400"></i>
                                    <span>Belum Upload</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-center">
                            @if($item->status_pembayaran === 'Lunas')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-600"></i>
                                    <span>Lunas</span>
                                </span>
                            @elseif($item->status_pembayaran === 'Menunggu Verifikasi')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-sky-50 text-sky-700 border border-sky-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-sky-500 animate-pulse"></span>
                                    <span>Perlu Verifikasi</span>
                                </span>
                            @elseif($item->status_pembayaran === 'Ditolak')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <i data-lucide="x-circle" class="w-3.5 h-3.5 text-rose-500"></i>
                                    <span>Ditolak</span>
                                </span>
                            @elseif($item->status_pembayaran === 'Dibatalkan')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    <span>Dibatalkan</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-amber-500"></i>
                                    <span>Menunggu Bayar</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('bimbingan.invoices.show', ['id' => $item->id, 'jenis' => $item->jenis]) }}" class="p-2 rounded-xl text-slate-500 hover:text-[#1768B5] hover:bg-sky-50 transition border border-transparent hover:border-sky-200" title="Lihat Detail Faktur">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('bimbingan.invoices.print', ['id' => $item->id, 'jenis' => $item->jenis]) }}" target="_blank" class="p-2 rounded-xl text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 transition border border-transparent hover:border-emerald-200" title="Cetak Faktur Resmi">
                                    <i data-lucide="printer" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-14 text-center text-slate-500">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="receipt" class="w-6 h-6"></i>
                            </div>
                            <p class="font-bold text-slate-700 text-sm">Tidak ada tagihan yang ditemukan</p>
                            <p class="text-xs text-slate-400 mt-0.5">Belum ada data tagihan yang diterbitkan atau tidak cocok dengan filter aktif.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->total() > 0)
        <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/50 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
                <div class="flex items-center gap-1.5">
                    <span>Baris per halaman:</span>
                    <select onchange="changePerPage(this.value)" class="bg-white border border-slate-200 text-slate-800 text-xs font-bold rounded-lg px-2.5 py-1 focus:ring-2 focus:ring-[#1768B5]/20 focus:border-[#1768B5] transition shadow-2xs cursor-pointer">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page', 10) == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
                <span class="text-slate-300">|</span>
                <span>Menampilkan <strong class="text-slate-800 font-bold">{{ $invoices->firstItem() ?? 0 }}</strong> - <strong class="text-slate-800 font-bold">{{ $invoices->lastItem() ?? 0 }}</strong> dari <strong class="text-slate-800 font-bold">{{ $invoices->total() }}</strong> data tagihan</span>
            </div>

            @if($invoices->hasPages())
            <div class="w-full md:w-auto overflow-x-auto flex justify-center">
                {{ $invoices->links() }}
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<script>
    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }
</script>
@endsection
