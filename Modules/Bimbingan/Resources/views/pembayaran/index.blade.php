@extends('layouts.app')

@section('title', 'Daftar Pembayaran Program - Aether')
@section('page_title', 'Daftar Pembayaran Program')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] rounded-2xl p-6 text-white shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#F6C75A] text-xs font-bold mb-2 border border-white/15 backdrop-blur-xs">
                <i data-lucide="image" class="w-3.5 h-3.5"></i>
                Bukti Pembayaran User
            </div>
            <h1 class="text-2xl font-black tracking-tight">Daftar Bukti Bayar User</h1>
            <p class="text-sm text-sky-100 mt-1 max-w-2xl leading-relaxed">
                Daftar transaksi pembayaran dari seluruh program (Bimbingan Skripsi, Olah Data Riset, dan Kelas &amp; Workshop) untuk diverifikasi dan dikonfirmasi status pembayarannya.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('bimbingan.invoices.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-white/15 hover:bg-white/25 border border-white/20 transition backdrop-blur-xs shadow-xs">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                <span>Rekap Tagihan (Invoice)</span>
            </a>
            <a href="{{ route('bimbingan.programs') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-sm transition">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                <span>Katalog Bimbingan</span>
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Pembayaran -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-xs transition">
            <div class="flex items-center justify-between text-slate-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Transaksi</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-slate-900 tracking-tight">{{ number_format($summary['total_transaksi'] ?? $summary['total_bukti_uploaded']) }}</div>
            <span class="text-[11px] text-slate-400">{{ number_format($summary['total_bukti_uploaded']) }} bukti bayar diunggah</span>
        </div>

        <!-- Terverifikasi Lunas -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-xs transition">
            <div class="flex items-center justify-between text-emerald-600 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Terverifikasi (Lunas)</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600 tracking-tight">Rp {{ number_format($summary['total_nominal_masuk'], 0, ',', '.') }}</div>
            <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                <span>Bukti Sah:</span>
                <span class="font-bold text-emerald-700">{{ number_format($summary['total_lunas_count']) }} Transaksi</span>
            </div>
        </div>

        <!-- Menunggu Verifikasi Admin -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-xs transition">
            <div class="flex items-center justify-between text-amber-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Verifikasi</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600 tracking-tight">{{ number_format($summary['total_menunggu_verifikasi_count']) }}</div>
            <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                <span>Perlu Ditinjau:</span>
                <span class="font-bold text-amber-700">Rp {{ number_format($summary['total_menunggu_verifikasi_nominal'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Bukti Ditolak -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200/90 shadow-2xs hover:shadow-xs transition">
            <div class="flex items-center justify-between text-rose-600 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Bukti Ditolak</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-rose-600 tracking-tight">{{ number_format($summary['total_ditolak_count']) }}</div>
            <span class="text-[11px] text-slate-400">Tidak valid / ditolak</span>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 p-5 shadow-2xs">
        <form method="GET" action="{{ route('bimbingan.pembayaran.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3.5 items-end">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-slate-700 mb-1">Cari Transaksi / Pembayar</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="No. Transaksi, Mahasiswa, Kode Bayar, WA..."
                           class="w-full pl-9 pr-3.5 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition placeholder:text-slate-400">
                </div>
            </div>

            <!-- Filter: Kategori Program -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Program</label>
                <select name="jenis" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition font-medium text-slate-700">
                    <option value="Semua">Semua Program</option>
                    <option value="bimbingan" {{ request('jenis') === 'bimbingan' ? 'selected' : '' }}>🎓 Bimbingan Skripsi</option>
                    <option value="olahdata" {{ request('jenis') === 'olahdata' ? 'selected' : '' }}>📊 Olah Data</option>
                    <option value="pelatihan" {{ request('jenis') === 'pelatihan' ? 'selected' : '' }}>🏅 Kelas &amp; Workshop</option>
                </select>
            </div>

            <!-- Status Pembayaran / Verifikasi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Status Pembayaran</label>
                <select name="status_pembayaran" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition text-slate-700">
                    <option value="Semua">Semua Status</option>
                    <option value="Menunggu Verifikasi" {{ request('status_pembayaran') === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Lunas" {{ request('status_pembayaran') === 'Lunas' ? 'selected' : '' }}>Lunas / Terverifikasi</option>
                    <option value="Menunggu Pembayaran" {{ request('status_pembayaran') === 'Menunggu Pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="Ditolak" {{ request('status_pembayaran') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Filter: Bukti Upload -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Bukti Transfer</label>
                <select name="has_bukti" class="w-full px-3 py-2 text-xs border border-slate-200 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition text-slate-700">
                    <option value="">Semua Data</option>
                    <option value="1" {{ request('has_bukti') === '1' ? 'selected' : '' }}>Ada Bukti Foto</option>
                    <option value="0" {{ request('has_bukti') === '0' ? 'selected' : '' }}>Belum Upload</option>
                </select>
            </div>

            <!-- Hidden Per Page Sync -->
            <input type="hidden" name="per_page" id="pembayaran_per_page_input" value="{{ request('per_page', 10) }}">

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3.5 bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('bimbingan.pembayaran.index') }}" class="py-2 px-3 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold rounded-xl transition flex items-center justify-center" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-sm overflow-hidden">
        <div class="px-6 py-4.5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-50/40">
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 tracking-tight">Daftar Bukti Bayar User</h3>
                <p class="text-xs text-slate-500 mt-0.5">Menampilkan <span class="font-bold text-slate-700">{{ $pembayarans->firstItem() ?? 0 }} - {{ $pembayarans->lastItem() ?? 0 }}</span> dari total <span class="font-bold text-slate-700">{{ $pembayarans->total() }}</span> transaksi pembayaran</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                @if(request()->anyFilled(['search', 'jenis', 'status_pembayaran', 'has_bukti']))
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
                        <th class="py-4 px-4 min-w-[170px]">No. Transaksi &amp; Waktu</th>
                        <th class="py-4 px-4 min-w-[200px]">Mahasiswa / Pemesan</th>
                        <th class="py-4 px-4 min-w-[240px]">Program / Layanan</th>
                        <th class="py-4 px-4 min-w-[160px]">Metode Bayar</th>
                        <th class="py-4 px-4 text-right min-w-[140px]">Nominal Bayar</th>
                        <th class="py-4 px-4 text-center min-w-[130px]">Bukti Bayar User</th>
                        <th class="py-4 px-4 text-center min-w-[140px]">Status Verifikasi</th>
                        <th class="py-4 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pembayarans as $index => $item)
                    <tr class="hover:bg-sky-50/30 transition-colors duration-150">
                        <td class="py-4 px-4 text-center text-slate-400 font-medium text-xs">
                            {{ $pembayarans->firstItem() + $index }}
                        </td>
                        <td class="py-4 px-4">
                            <span class="font-mono font-bold text-xs text-slate-900 block tracking-tight">{{ $item->nomor_transaksi }}</span>
                            <div class="text-[11px] text-slate-400 flex items-center gap-1 mt-0.5 font-medium">
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
                                    <div class="text-[11px] text-slate-500 font-mono truncate flex items-center gap-1">
                                        <i data-lucide="phone" class="w-3 h-3 text-slate-400 shrink-0"></i>
                                        <span>{{ $item->telepon_pemesan ?: ($item->email_pemesan ?: '-') }}</span>
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
                            <!-- Category Badge -->
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
                            <span class="text-[11px] text-slate-500 font-medium mt-0.5 block">{{ $item->detail_layanan }}</span>
                        </td>
                        <td class="py-4 px-4">
                            <div class="font-bold text-slate-800 text-xs">{{ $item->metode_pembayaran ?: '-' }}</div>
                            @if($item->kode_pembayaran && $item->kode_pembayaran !== '-')
                            <div class="inline-flex items-center gap-1 font-mono text-[10px] text-slate-600 bg-slate-100 px-1.5 py-0.5 rounded mt-0.5 border border-slate-200">
                                <i data-lucide="hash" class="w-2.5 h-2.5 text-slate-400"></i>
                                <span>{{ $item->kode_pembayaran }}</span>
                            </div>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-right">
                            <div class="font-black text-slate-900 text-sm tracking-tight">
                                Rp {{ number_format($item->total_bayar ?: ($item->biaya + 5000), 0, ',', '.') }}
                            </div>
                        </td>
                        <td class="py-4 px-4 text-center">
                            @if($item->bukti_pembayaran_url)
                                <button type="button"
                                        onclick="showProofModal('{{ $item->bukti_pembayaran_url }}', '{{ $item->nomor_transaksi }}', '{{ addslashes($item->nama_pemesan ?? '') }}', '{{ $item->formatted_biaya }}', '{{ $item->metode_pembayaran }}', '{{ $item->jenis_label }}')"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-bold bg-sky-50 text-sky-700 hover:bg-[#1768B5] hover:text-white border border-sky-200 shadow-2xs transition">
                                    <i data-lucide="image" class="w-3.5 h-3.5"></i>
                                    <span>Lihat Foto Bukti</span>
                                </button>
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
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    <span>Perlu Verifikasi</span>
                                </span>
                            @elseif($item->status_pembayaran === 'Ditolak')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    <i data-lucide="x-circle" class="w-3.5 h-3.5 text-rose-500"></i>
                                    <span>Ditolak</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 text-slate-400"></i>
                                    <span>Menunggu Bayar</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1">
                                <a href="{{ route('bimbingan.invoices.show', ['id' => $item->id, 'jenis' => $item->jenis]) }}" class="p-2 rounded-xl text-slate-500 hover:text-[#1768B5] hover:bg-sky-50 transition border border-transparent hover:border-sky-200" title="Lihat Tagihan / Invoice">
                                    <i data-lucide="receipt" class="w-4 h-4"></i>
                                </a>

                                @if(!$isRole99)
                                <button type="button"
                                        onclick="openStatusModal({{ $item->id }}, '{{ $item->nomor_transaksi }}', '{{ $item->status_pembayaran }}', '{{ addslashes($item->catatan ?? '') }}', '{{ $item->jenis }}', '{{ $item->jenis_label }}')"
                                        class="p-2 rounded-xl text-slate-500 hover:text-emerald-700 hover:bg-emerald-50 transition border border-transparent hover:border-emerald-200" title="Verifikasi / Update Status Bukti Bayar">
                                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-14 text-center text-slate-500">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="credit-card" class="w-6 h-6"></i>
                            </div>
                            <p class="font-bold text-slate-700 text-sm">Belum ada transaksi pembayaran yang ditemukan</p>
                            <p class="text-xs text-slate-400 mt-0.5">Daftar ini merekap seluruh transaksi pembayaran program bimbingan, olah data, dan pelatihan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pembayarans->total() > 0)
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
                <span>Menampilkan <strong class="text-slate-800 font-bold">{{ $pembayarans->firstItem() ?? 0 }}</strong> - <strong class="text-slate-800 font-bold">{{ $pembayarans->lastItem() ?? 0 }}</strong> dari <strong class="text-slate-800 font-bold">{{ $pembayarans->total() }}</strong> transaksi pembayaran</span>
            </div>

            @if($pembayarans->hasPages())
            <div class="w-full md:w-auto overflow-x-auto flex justify-center">
                {{ $pembayarans->links() }}
            </div>
            @endif
        </div>
        @endif
    </div>
</div>

<!-- Modal: Preview Bukti Transfer (Lightbox) -->
<div id="proofModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl space-y-0 animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h4 class="text-sm font-bold text-slate-900" id="proofModalTitle">Bukti Pembayaran User</h4>
                <p class="text-[11px] text-slate-500 mt-0.5" id="proofModalSub"></p>
            </div>
            <button onclick="closeProofModal()" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="p-4 bg-slate-900 flex items-center justify-center min-h-[300px] max-h-[70vh] overflow-auto">
            <img id="proofModalImg" src="" alt="Bukti Transfer" class="max-w-full max-h-[65vh] object-contain rounded-lg shadow-md">
        </div>
        <div class="p-4 bg-white border-t border-slate-100 flex items-center justify-between">
            <a id="proofDownloadBtn" href="" target="_blank" download class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold transition">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Unduh Gambar Bukti</span>
            </a>
            <button onclick="closeProofModal()" class="px-4 py-2 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal: Verifikasi Bukti Pembayaran (Admin Only) -->
@if(!$isRole99)
<div id="statusModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
            <div>
                <h4 class="text-sm font-bold text-slate-900">Verifikasi Bukti Pembayaran</h4>
                <span class="text-[11px] font-bold text-[#1768B5]" id="statusModalJenisBadge"></span>
            </div>
            <button onclick="closeStatusModal()" class="w-8 h-8 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="statusForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="jenis" id="statusModalJenis" value="bimbingan">

            <div>
                <span class="text-xs text-slate-500 font-semibold block">Nomor Transaksi:</span>
                <span class="text-sm font-mono font-black text-slate-900 block" id="statusModalNomorTrx"></span>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Keputusan Verifikasi *</label>
                <select name="status_pembayaran" id="statusSelect" required class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none">
                    <option value="Lunas">Setujui / Lunas (Otomatis Aktifkan Program)</option>
                    <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                    <option value="Ditolak">Tolak Bukti Bayar (Minta User Upload Ulang)</option>
                    <option value="Menunggu Pembayaran">Kembalikan ke Menunggu Pembayaran</option>
                </select>
                <p class="text-[11px] text-slate-400 mt-1" id="statusHelperText">
                    Status <strong>Lunas</strong> akan memverifikasi transfer dan mengaktifkan program layanan user.
                </p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Verifikasi / Alasan Penolakan</label>
                <textarea name="catatan" id="catatanInput" rows="2" placeholder="Contoh: Bukti transfer valid dan dana telah terkonfirmasi masuk..." class="w-full px-3 py-2 text-xs border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none resize-none"></textarea>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold shadow-xs transition">
                    Simpan Keputusan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function showProofModal(imgUrl, noTrx, name, nominal, metode, jenisLabel) {
        document.getElementById('proofModalImg').src = imgUrl;
        document.getElementById('proofDownloadBtn').href = imgUrl;
        document.getElementById('proofModalTitle').innerText = 'Bukti Pembayaran - ' + noTrx;
        document.getElementById('proofModalSub').innerText = (jenisLabel ? '[' + jenisLabel + '] ' : '') + 'Pemesan: ' + (name || 'Mahasiswa') + (metode ? ' • ' + metode : '');
        document.getElementById('proofModal').classList.remove('hidden');
    }

    function closeProofModal() {
        document.getElementById('proofModal').classList.add('hidden');
    }

    function openStatusModal(id, noTrx, currentStatus, currentCatatan, jenis, jenisLabel) {
        var form = document.getElementById('statusForm');
        form.action = "{{ url('bimbingan/pembayaran') }}/" + id + "/status";
        document.getElementById('statusModalJenis').value = jenis || 'bimbingan';
        document.getElementById('statusModalJenisBadge').innerText = jenisLabel ? 'Layanan: ' + jenisLabel : '';
        document.getElementById('statusModalNomorTrx').innerText = noTrx;
        document.getElementById('statusSelect').value = currentStatus;
        document.getElementById('catatanInput').value = currentCatatan || '';
        document.getElementById('statusModal').classList.remove('hidden');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.add('hidden');
    }

    function changePerPage(value) {
        const url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    }
</script>
@endsection
