@extends('layouts.app')

@section('title', 'Neraca Keuangan - ' . config('app.name'))
@section('page_title', 'Neraca Keuangan')

@push('styles')
<style>
    .font-mono-num {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }
    .clickable-account-row {
        cursor: pointer;
        transition: all 0.15s ease-in-out;
    }
    .clickable-account-row:hover {
        background-color: #f1f5f9;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header & Action Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold mb-1.5 border border-blue-200">
                <i data-lucide="scale" class="w-3.5 h-3.5"></i>
                Laporan Posisi Keuangan (Balance Sheet)
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Neraca Keuangan</h1>
            <p class="text-xs text-gray-500">Ringkasan aset, liabilitas, dan ekuitas institusi akademik bimbingan skripsi per tanggal tertentu.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="cetakHtml()" class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl text-xs font-bold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 shadow-xs transition">
                <i data-lucide="printer" class="w-4 h-4 text-gray-500"></i>
                Cetak Lembar HTML
            </button>
            <button type="button" onclick="exportExcel()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 shadow-xs transition">
                <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
                Export Excel
            </button>
        </div>
    </div>

    <!-- KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Aset -->
        <div class="bg-white p-4 rounded-xl border border-blue-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Total Aset (Aktiva)</p>
                <h3 class="text-xl font-black text-blue-950 mt-1 font-mono-num" id="stat-total-aset">
                    Rp {{ number_format($report['aset']['total'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] text-blue-500">Aset Lancar &amp; Aset Tetap</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="wallet" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 2: Total Kewajiban -->
        <div class="bg-white p-4 rounded-xl border border-amber-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Total Kewajiban</p>
                <h3 class="text-xl font-black text-amber-950 mt-1 font-mono-num" id="stat-total-kewajiban">
                    Rp {{ number_format($report['kewajiban']['total'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] text-amber-500">Utang Lancar &amp; Jangka Panjang</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="receipt" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 3: Total Ekuitas -->
        <div class="bg-white p-4 rounded-xl border border-purple-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-purple-600 uppercase tracking-wider">Total Ekuitas (Modal)</p>
                <h3 class="text-xl font-black text-purple-950 mt-1 font-mono-num" id="stat-total-ekuitas">
                    Rp {{ number_format($report['ekuitas']['total'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] text-purple-500">Modal, Laba Ditahan &amp; Berjalan</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                <i data-lucide="landmark" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 4: Keseimbangan -->
        <div class="bg-white p-4 rounded-xl border {{ $report['is_balanced'] ? 'border-emerald-200 bg-emerald-50/20' : 'border-rose-200 bg-rose-50/20' }} shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold {{ $report['is_balanced'] ? 'text-emerald-700' : 'text-rose-700' }} uppercase tracking-wider">Keseimbangan Neraca</p>
                <h3 class="text-base font-black {{ $report['is_balanced'] ? 'text-emerald-900' : 'text-rose-900' }} mt-1 flex items-center gap-1.5" id="stat-status-keseimbangan">
                    @if($report['is_balanced'])
                        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                        <span>Seimbang (Balance)</span>
                    @else
                        <i data-lucide="alert-triangle" class="w-4 h-4 text-rose-600"></i>
                        <span>Belum Seimbang</span>
                    @endif
                </h3>
                <span class="text-[10px] text-slate-500" id="stat-selisih-info">
                    Selisih: Rp {{ number_format($report['selisih'], 0, ',', '.') }}
                </span>
            </div>
            <div class="h-11 w-11 rounded-xl {{ $report['is_balanced'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} flex items-center justify-center">
                <i data-lucide="scale" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-4">
        <form id="filter-form" onsubmit="event.preventDefault(); applyFilter();" class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2 text-xs font-bold text-gray-700">
                    <i data-lucide="filter" class="w-4 h-4 text-blue-600"></i>
                    Filter Periode Neraca
                </div>
                <!-- Quick Date Presets -->
                <div class="flex flex-wrap items-center gap-1.5">
                    <button type="button" onclick="setPreset('today')" class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition">Hari Ini</button>
                    <button type="button" onclick="setPreset('this_month')" class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition">Bulan Ini</button>
                    <button type="button" onclick="setPreset('this_year')" class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition">Tahun Ini</button>
                    <button type="button" onclick="setPreset('all')" class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition">Semua</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 pt-1">
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Dari Tanggal (Saldo Awal)</label>
                    <input type="date" name="start_date" id="filter-start-date" value="{{ $meta['start_date'] ?? '' }}" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Per Tanggal / Sampai Tanggal</label>
                    <input type="date" name="end_date" id="filter-end-date" value="{{ $meta['end_date'] ?? date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div class="flex items-end gap-2">
                    <button type="button" onclick="resetFilter()" class="flex-1 px-3.5 py-2 rounded-xl text-xs font-bold text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 transition">
                        Reset
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-xs transition">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Petunjuk Klik Rincian -->
    <div class="flex items-center gap-2 p-3 bg-blue-50/70 border border-blue-200/70 rounded-xl text-xs text-blue-800">
        <i data-lucide="mouse-pointer-click" class="w-4 h-4 text-blue-600 shrink-0"></i>
        <span><strong>Tips Interaktif:</strong> Seluruh baris akun pada tabel di bawah ini dapat diklik. Klik salah satu akun untuk langsung membuka halaman rincian transaksi &amp; buku besar akun tersebut.</span>
    </div>

    <!-- Main Table: Neraca Berpasangan (Aktiva vs Pasiva) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Kolom Kiri: ASET (AKTIVA) -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 bg-blue-900 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="briefcase" class="w-4 h-4 text-blue-300"></i>
                        <h2 class="text-sm font-black uppercase tracking-wide">1. Aset (Aktiva)</h2>
                    </div>
                    <span class="text-xs font-mono font-bold text-blue-200">Total: Rp {{ number_format($report['aset']['total'], 0, ',', '.') }}</span>
                </div>

                <div class="p-5 space-y-6">
                    <!-- A. Aset Lancar -->
                    <div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <span class="text-xs font-black text-slate-800 uppercase tracking-wide">A. Aset Lancar</span>
                            <span class="text-xs font-bold text-slate-700 font-mono">Rp {{ number_format($report['aset']['aset_lancar']['subtotal'], 0, ',', '.') }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 text-xs">
                            @forelse($report['aset']['aset_lancar']['accounts'] as $acc)
                            <div onclick="goToRincian({{ $acc->coa_id }}, 'neraca')" class="clickable-account-row py-2.5 px-2 flex items-center justify-between rounded-lg group">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-[11px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">{{ $acc->kode_akun }}</span>
                                        <span class="font-bold text-slate-800 group-hover:text-blue-700 transition">{{ $acc->nama_akun }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-0.5 ml-0.5">{{ $acc->transaksi_count }} transaksi pada periode ini</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-bold text-slate-900">Rp {{ number_format($acc->saldo_akhir, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @empty
                            <p class="py-3 text-center text-xs text-slate-400">Tidak ada akun aset lancar.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- B. Aset Tetap -->
                    <div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <span class="text-xs font-black text-slate-800 uppercase tracking-wide">B. Aset Tetap</span>
                            <span class="text-xs font-bold text-slate-700 font-mono">Rp {{ number_format($report['aset']['aset_tetap']['subtotal'], 0, ',', '.') }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 text-xs">
                            @forelse($report['aset']['aset_tetap']['accounts'] as $acc)
                            <div onclick="goToRincian({{ $acc->coa_id }}, 'neraca')" class="clickable-account-row py-2.5 px-2 flex items-center justify-between rounded-lg group">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-[11px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100">{{ $acc->kode_akun }}</span>
                                        <span class="font-bold {{ $acc->is_kontra ? 'text-rose-700' : 'text-slate-800' }} group-hover:text-blue-700 transition">
                                            {{ $acc->is_kontra ? '(-) ' : '' }}{{ $acc->nama_akun }}
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-0.5 ml-0.5">{{ $acc->transaksi_count }} transaksi pada periode ini</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-bold {{ $acc->is_kontra ? 'text-rose-700' : 'text-slate-900' }}">
                                        {{ $acc->is_kontra ? '-' : '' }}Rp {{ number_format($acc->saldo_akhir, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <p class="py-3 text-center text-xs text-slate-400">Tidak ada akun aset tetap.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Footer Total Aset -->
                <div class="px-5 py-3.5 bg-blue-50 border-t border-blue-100 flex items-center justify-between">
                    <span class="text-xs font-black text-blue-900 uppercase tracking-wide">TOTAL ASET (AKTIVA)</span>
                    <span class="text-sm font-black text-blue-950 font-mono">Rp {{ number_format($report['aset']['total'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: KEWAJIBAN & EKUITAS (PASIVA) -->
        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
                <div class="px-5 py-3.5 bg-slate-900 text-white flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i data-lucide="shield" class="w-4 h-4 text-slate-300"></i>
                        <h2 class="text-sm font-black uppercase tracking-wide">2. Kewajiban &amp; Ekuitas (Pasiva)</h2>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-300">Total: Rp {{ number_format($report['total_pasiva'], 0, ',', '.') }}</span>
                </div>

                <div class="p-5 space-y-6">
                    <!-- A. Kewajiban Lancar -->
                    <div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <span class="text-xs font-black text-slate-800 uppercase tracking-wide">A. Kewajiban Lancar</span>
                            <span class="text-xs font-bold text-slate-700 font-mono">Rp {{ number_format($report['kewajiban']['kewajiban_lancar']['subtotal'], 0, ',', '.') }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 text-xs">
                            @forelse($report['kewajiban']['kewajiban_lancar']['accounts'] as $acc)
                            <div onclick="goToRincian({{ $acc->coa_id }}, 'neraca')" class="clickable-account-row py-2.5 px-2 flex items-center justify-between rounded-lg group">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-[11px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100">{{ $acc->kode_akun }}</span>
                                        <span class="font-bold text-slate-800 group-hover:text-amber-700 transition">{{ $acc->nama_akun }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-0.5 ml-0.5">{{ $acc->transaksi_count }} transaksi</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-bold text-slate-900">Rp {{ number_format($acc->saldo_akhir, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @empty
                            <p class="py-2 text-center text-xs text-slate-400">Tidak ada kewajiban lancar.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- B. Kewajiban Jangka Panjang -->
                    <div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <span class="text-xs font-black text-slate-800 uppercase tracking-wide">B. Kewajiban Jangka Panjang</span>
                            <span class="text-xs font-bold text-slate-700 font-mono">Rp {{ number_format($report['kewajiban']['kewajiban_jangka_panjang']['subtotal'], 0, ',', '.') }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 text-xs">
                            @forelse($report['kewajiban']['kewajiban_jangka_panjang']['accounts'] as $acc)
                            <div onclick="goToRincian({{ $acc->coa_id }}, 'neraca')" class="clickable-account-row py-2.5 px-2 flex items-center justify-between rounded-lg group">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-[11px] font-bold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-100">{{ $acc->kode_akun }}</span>
                                        <span class="font-bold text-slate-800 group-hover:text-amber-700 transition">{{ $acc->nama_akun }}</span>
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-0.5 ml-0.5">{{ $acc->transaksi_count }} transaksi</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-bold text-slate-900">Rp {{ number_format($acc->saldo_akhir, 0, ',', '.') }}</span>
                                </div>
                            </div>
                            @empty
                            <p class="py-2 text-center text-xs text-slate-400">Tidak ada kewajiban jangka panjang.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- C. Ekuitas (Modal) -->
                    <div>
                        <div class="flex items-center justify-between pb-2 border-b border-slate-100">
                            <span class="text-xs font-black text-purple-900 uppercase tracking-wide">C. Ekuitas (Modal Sendiri)</span>
                            <span class="text-xs font-bold text-purple-900 font-mono">Rp {{ number_format($report['ekuitas']['total'], 0, ',', '.') }}</span>
                        </div>
                        <div class="divide-y divide-slate-100 text-xs">
                            @forelse($report['ekuitas']['accounts'] as $acc)
                            <div onclick="goToRincian({{ $acc->coa_id }}, 'neraca')" class="clickable-account-row py-2.5 px-2 flex items-center justify-between rounded-lg group">
                                <div class="flex flex-col">
                                    <div class="flex items-center gap-2">
                                        <span class="font-mono text-[11px] font-bold text-purple-700 bg-purple-50 px-1.5 py-0.5 rounded border border-purple-100">{{ $acc->kode_akun }}</span>
                                        <span class="font-bold {{ ($acc->is_kontra ?? false) ? 'text-rose-700' : 'text-slate-800' }} group-hover:text-purple-700 transition">
                                            {{ ($acc->is_kontra ?? false) ? '(-) ' : '' }}{{ $acc->nama_akun }}
                                        </span>
                                        @if($acc->is_calculated ?? false)
                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800">Dari Laba Rugi</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-400 mt-0.5 ml-0.5">{{ $acc->transaksi_count }} entri</span>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-bold {{ ($acc->is_kontra ?? false) ? 'text-rose-700' : 'text-slate-900' }}">
                                        {{ ($acc->is_kontra ?? false) ? '-' : '' }}Rp {{ number_format($acc->saldo_akhir, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <p class="py-2 text-center text-xs text-slate-400">Tidak ada data ekuitas.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Footer Total Pasiva -->
                <div class="px-5 py-3.5 bg-slate-100 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-black text-slate-900 uppercase tracking-wide">TOTAL PASIVA (KEWAJIBAN + EKUITAS)</span>
                    <span class="text-sm font-black text-slate-950 font-mono">Rp {{ number_format($report['total_pasiva'], 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function setPreset(preset) {
        const today = new Date();
        const formatDate = (d) => d.toISOString().split('T')[0];
        const startInput = document.getElementById('filter-start-date');
        const endInput = document.getElementById('filter-end-date');

        if (preset === 'today') {
            startInput.value = formatDate(today);
            endInput.value = formatDate(today);
        } else if (preset === 'this_month') {
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const lastDay = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            startInput.value = formatDate(firstDay);
            endInput.value = formatDate(lastDay);
        } else if (preset === 'this_year') {
            const firstDay = new Date(today.getFullYear(), 0, 1);
            const lastDay = new Date(today.getFullYear(), 11, 31);
            startInput.value = formatDate(firstDay);
            endInput.value = formatDate(lastDay);
        } else if (preset === 'all') {
            startInput.value = '';
            endInput.value = '';
        }
        applyFilter();
    }

    function applyFilter() {
        const start = document.getElementById('filter-start-date').value;
        const end = document.getElementById('filter-end-date').value;
        const params = new URLSearchParams(window.location.search);

        if (start) params.set('start_date', start); else params.delete('start_date');
        if (end) params.set('end_date', end); else params.delete('end_date');

        window.location.href = `{{ route('neraca.index') }}?${params.toString()}`;
    }

    function resetFilter() {
        window.location.href = `{{ route('neraca.index') }}`;
    }

    function cetakHtml() {
        const start = document.getElementById('filter-start-date').value;
        const end = document.getElementById('filter-end-date').value;
        const params = new URLSearchParams();
        if (start) params.set('start_date', start);
        if (end) params.set('end_date', end);
        window.open(`{{ route('neraca.cetak') }}?${params.toString()}`, '_blank');
    }

    function goToRincian(coaId, report) {
        const start = document.getElementById('filter-start-date')?.value || '';
        const end = document.getElementById('filter-end-date')?.value || '';
        const params = new URLSearchParams();
        if (start) params.set('start_date', start);
        if (end) params.set('end_date', end);
        const qs = params.toString() ? '?' + params.toString() : '';
        window.location.href = `/${report}/rincian/${coaId}${qs}`;
    }

    function exportExcel() {
        const start = document.getElementById('filter-start-date').value;
        const end = document.getElementById('filter-end-date').value;
        const params = new URLSearchParams();
        if (start) params.set('start_date', start);
        if (end) params.set('end_date', end);
        window.location.href = `{{ route('neraca.export-excel') }}?${params.toString()}`;
    }
</script>
@endpush
