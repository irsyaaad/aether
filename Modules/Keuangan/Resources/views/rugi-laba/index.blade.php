@extends('layouts.app')

@section('title', 'Laba Rugi - ' . config('app.name'))
@section('page_title', 'Laba Rugi')

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
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold mb-1.5 border border-emerald-200">
                <i data-lucide="trending-up" class="w-3.5 h-3.5"></i>
                Laporan Kinerja Keuangan (Income Statement)
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Laporan Laba Rugi</h1>
            <p class="text-xs text-gray-500">Hasil usaha pendapatan jasa layanan akademik dikurangi beban operasional periode berjalan.</p>
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
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Card 1: Total Pendapatan -->
        <div class="bg-white p-4 rounded-xl border border-emerald-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Total Pendapatan</p>
                <h3 class="text-xl font-black text-emerald-950 mt-1 font-mono-num" id="stat-total-pendapatan">
                    Rp {{ number_format($report['total_pendapatan'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] text-emerald-500">Layanan Bimbingan &amp; Konsultasi</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="arrow-down-left" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 2: Total Beban -->
        <div class="bg-white p-4 rounded-xl border {{ $report['total_beban'] < 0 ? 'border-amber-200 bg-amber-50/20' : 'border-rose-100' }} shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold {{ $report['total_beban'] < 0 ? 'text-amber-700' : 'text-rose-600' }} uppercase tracking-wider">
                    Total Beban Operasional {{ $report['total_beban'] < 0 ? '(Kredit Bersih)' : '' }}
                </p>
                <h3 class="text-xl font-black {{ $report['total_beban'] < 0 ? 'text-amber-900' : 'text-rose-950' }} mt-1 font-mono-num" id="stat-total-beban">
                    Rp {{ number_format($report['total_beban'], 0, ',', '.') }}
                </h3>
                @if($report['total_beban'] < 0)
                <span class="text-[10px] text-amber-600 font-semibold block mt-0.5">
                    * Mutasi Kredit (Penerimaan/Refund) > Debit Pengeluaran
                </span>
                @else
                <span class="text-[10px] text-rose-500">Beban Operasional &amp; Riset</span>
                @endif
            </div>
            <div class="h-11 w-11 rounded-xl {{ $report['total_beban'] < 0 ? 'bg-amber-100 text-amber-700' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center">
                <i data-lucide="{{ $report['total_beban'] < 0 ? 'info' : 'arrow-up-right' }}" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 3: Laba / Rugi Bersih -->
        <div class="bg-white p-4 rounded-xl border {{ $report['is_laba'] ? 'border-emerald-200 bg-emerald-50/20' : 'border-rose-200 bg-rose-50/20' }} shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold {{ $report['is_laba'] ? 'text-emerald-700' : 'text-rose-700' }} uppercase tracking-wider">
                    {{ $report['status_label'] }} (Net Result)
                </p>
                <h3 class="text-xl font-black {{ $report['is_laba'] ? 'text-emerald-900' : 'text-rose-900' }} mt-1 font-mono-num" id="stat-laba-bersih">
                    Rp {{ number_format($report['laba_bersih'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] text-slate-500">Pendapatan Bersih Setelah Beban</span>
            </div>
            <div class="h-11 w-11 rounded-xl {{ $report['is_laba'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} flex items-center justify-center">
                <i data-lucide="{{ $report['is_laba'] ? 'trending-up' : 'trending-down' }}" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-4">
        <form id="filter-form" onsubmit="event.preventDefault(); applyFilter();" class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2 text-xs font-bold text-gray-700">
                    <i data-lucide="filter" class="w-4 h-4 text-emerald-600"></i>
                    Filter Rentang Tanggal Laba Rugi
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
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" id="filter-start-date" value="{{ $meta['start_date'] ?? '' }}" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="filter-end-date" value="{{ $meta['end_date'] ?? date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500">
                </div>

                <div class="flex items-end gap-2">
                    <button type="button" onclick="resetFilter()" class="flex-1 px-3.5 py-2 rounded-xl text-xs font-bold text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 transition">
                        Reset
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition">
                        Terapkan
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Petunjuk Klik Rincian -->
    <div class="flex items-center gap-2 p-3 bg-emerald-50/70 border border-emerald-200/70 rounded-xl text-xs text-emerald-800">
        <i data-lucide="mouse-pointer-click" class="w-4 h-4 text-emerald-600 shrink-0"></i>
        <span><strong>Tips Interaktif:</strong> Seluruh baris akun pada tabel di bawah ini dapat diklik. Klik salah satu akun untuk langsung membuka halaman rincian transaksi &amp; buku besar akun tersebut.</span>
    </div>

    <!-- Main Laba Rugi Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <!-- 1. PENDAPATAN SECTION -->
        <div class="px-5 py-3.5 bg-emerald-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="wallet" class="w-4 h-4 text-emerald-300"></i>
                <h2 class="text-sm font-black uppercase tracking-wide">1. PENDAPATAN (REVENUE)</h2>
            </div>
            <span class="text-xs font-mono font-bold text-emerald-200">Total: Rp {{ number_format($report['total_pendapatan'], 0, ',', '.') }}</span>
        </div>

        <div class="p-5 space-y-6">
            @forelse($report['pendapatan']['categories'] as $cat)
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-200 font-bold text-slate-800 text-xs">
                    <span>Kategori: {{ $cat['kategori'] }}</span>
                    <span class="font-mono text-emerald-800">Subtotal: Rp {{ number_format($cat['subtotal'], 0, ',', '.') }}</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($cat['accounts'] as $acc)
                    <div onclick="goToRincian({{ $acc->coa_id }}, 'rugi-laba')" class="clickable-account-row py-2.5 px-2 flex items-center justify-between rounded-lg group">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-[11px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">{{ $acc->kode_akun }}</span>
                                <span class="font-bold text-slate-800 group-hover:text-emerald-700 transition">{{ $acc->nama_akun }}</span>
                            </div>
                            <span class="text-[10px] text-slate-400 mt-0.5 ml-0.5">{{ $acc->transaksi_count }} transaksi pada periode ini</span>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-bold text-slate-900">Rp {{ number_format($acc->nominal, 0, ',', '.') }}</span>
                            <span class="block text-[10px] text-slate-400">Kredit: {{ number_format($acc->mutasi_kredit, 0, ',', '.') }} | Debit: {{ number_format($acc->mutasi_debit, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <p class="py-4 text-center text-xs text-slate-400">Tidak ada akun pendapatan.</p>
            @endforelse
        </div>

        <div class="px-5 py-3.5 bg-emerald-50 border-t border-b border-emerald-100 flex items-center justify-between">
            <span class="text-xs font-black text-emerald-900 uppercase tracking-wide">TOTAL PENDAPATAN</span>
            <span class="text-sm font-black text-emerald-950 font-mono">Rp {{ number_format($report['total_pendapatan'], 0, ',', '.') }}</span>
        </div>

        <!-- 2. BEBAN OPERASIONAL SECTION -->
        <div class="px-5 py-3.5 bg-rose-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="receipt" class="w-4 h-4 text-rose-300"></i>
                <h2 class="text-sm font-black uppercase tracking-wide">2. BEBAN OPERASIONAL (EXPENSES)</h2>
            </div>
            <span class="text-xs font-mono font-bold text-rose-200">Total: Rp {{ number_format($report['total_beban'], 0, ',', '.') }}</span>
        </div>

        <div class="p-5 space-y-6">
            @forelse($report['beban']['categories'] as $cat)
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-200 font-bold text-slate-800 text-xs">
                    <span>Kategori: {{ $cat['kategori'] }}</span>
                    <span class="font-mono text-rose-800">Subtotal: Rp {{ number_format($cat['subtotal'], 0, ',', '.') }}</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @foreach($cat['accounts'] as $acc)
                    <div onclick="goToRincian({{ $acc->coa_id }}, 'rugi-laba')" class="clickable-account-row py-2.5 px-2 flex items-center justify-between rounded-lg group">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-[11px] font-bold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">{{ $acc->kode_akun }}</span>
                                <span class="font-bold text-slate-800 group-hover:text-rose-700 transition">{{ $acc->nama_akun }}</span>
                                @if($acc->nominal < 0)
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded bg-amber-100 text-amber-800 border border-amber-200" title="Mutasi Kredit (Penerimaan) lebih besar dari Debit (Pengeluaran)">
                                    Pengembalian / Kredit Bersih
                                </span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-400 mt-0.5 ml-0.5">{{ $acc->transaksi_count }} transaksi pada periode ini</span>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-bold {{ $acc->nominal < 0 ? 'text-amber-700' : 'text-slate-900' }}">Rp {{ number_format($acc->nominal, 0, ',', '.') }}</span>
                            <span class="block text-[10px] text-slate-400">Debit (Keluar): {{ number_format($acc->mutasi_debit, 0, ',', '.') }} | Kredit (Masuk/Refund): {{ number_format($acc->mutasi_kredit, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @empty
            <p class="py-4 text-center text-xs text-slate-400">Tidak ada akun beban.</p>
            @endforelse
        </div>

        <div class="px-5 py-3.5 {{ $report['total_beban'] < 0 ? 'bg-amber-50 border-amber-200' : 'bg-rose-50 border-rose-100' }} border-t border-b flex items-center justify-between">
            <span class="text-xs font-black {{ $report['total_beban'] < 0 ? 'text-amber-900' : 'text-rose-900' }} uppercase tracking-wide">
                TOTAL BEBAN OPERASIONAL {{ $report['total_beban'] < 0 ? '(KREDIT BERSIH)' : '' }}
            </span>
            <span class="text-sm font-black {{ $report['total_beban'] < 0 ? 'text-amber-950' : 'text-rose-950' }} font-mono">
                Rp {{ number_format($report['total_beban'], 0, ',', '.') }}
            </span>
        </div>

        <!-- 3. GRAND TOTAL: LABA / RUGI BERSIH -->
        <div class="p-6 {{ $report['is_laba'] ? 'bg-emerald-50/50 border-t-2 border-emerald-500' : 'bg-rose-50/50 border-t-2 border-rose-500' }} flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-2xl {{ $report['is_laba'] ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }} flex items-center justify-center">
                    <i data-lucide="{{ $report['is_laba'] ? 'award' : 'alert-circle' }}" class="h-6 w-6"></i>
                </div>
                <div>
                    <span class="text-[11px] font-bold {{ $report['is_laba'] ? 'text-emerald-700' : 'text-rose-700' }} uppercase tracking-wider block">
                        HASIL USAHA PERIODE (NET PROFIT / LOSS)
                    </span>
                    <h3 class="text-lg font-black text-slate-900">
                        {{ strtoupper($report['status_label']) }}
                    </h3>
                </div>
            </div>
            <div class="text-right">
                <span class="text-2xl font-black font-mono-num {{ $report['is_laba'] ? 'text-emerald-700' : 'text-rose-700' }}">
                    Rp {{ number_format($report['laba_bersih'], 0, ',', '.') }}
                </span>
                <span class="block text-xs font-semibold text-slate-600 mt-1 font-mono-num">
                    {{ $report['formula_penjelasan'] ?? ('Total Pendapatan - Total Beban: Rp ' . number_format($report['total_pendapatan'], 0, ',', '.') . ' - Rp ' . number_format($report['total_beban'], 0, ',', '.')) }}
                </span>
                @if($report['total_beban'] < 0)
                <span class="block text-[11px] text-amber-700 mt-0.5 italic">
                    * Catatan: Beban bernilai minus karena mutasi kredit (pengembalian beban) melampaui pengeluaran, sehingga menambah hasil usaha bersih.
                </span>
                @endif
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

        window.location.href = `{{ route('rugi-laba.index') }}?${params.toString()}`;
    }

    function resetFilter() {
        window.location.href = `{{ route('rugi-laba.index') }}`;
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

    function cetakHtml() {
        const start = document.getElementById('filter-start-date').value;
        const end = document.getElementById('filter-end-date').value;
        const params = new URLSearchParams();
        if (start) params.set('start_date', start);
        if (end) params.set('end_date', end);
        window.open(`{{ route('rugi-laba.cetak') }}?${params.toString()}`, '_blank');
    }

    function exportExcel() {
        const start = document.getElementById('filter-start-date').value;
        const end = document.getElementById('filter-end-date').value;
        const params = new URLSearchParams();
        if (start) params.set('start_date', start);
        if (end) params.set('end_date', end);
        window.location.href = `{{ route('rugi-laba.export-excel') }}?${params.toString()}`;
    }
</script>
@endpush
