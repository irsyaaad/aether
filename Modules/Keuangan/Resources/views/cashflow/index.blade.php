@extends('layouts.app')

@section('title', 'Arus Kas (Cash Flow) - ' . config('app.name'))
@section('page_title', 'Arus Kas (Cash Flow)')

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
                <i data-lucide="wallet" class="w-3.5 h-3.5"></i>
                Laporan Arus Kas (Cash Flow Statement - Direct Method)
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Laporan Arus Kas</h1>
            <p class="text-xs text-gray-500">Penerimaan dan pengeluaran kas &amp; bank berdasarkan aktivitas operasional, investasi, dan pendanaan.</p>
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
        <!-- Card 1: Total Kas Masuk -->
        <div class="bg-white p-4 rounded-xl border border-emerald-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Total Kas Masuk (Inflow)</p>
                <h3 class="text-xl font-black text-emerald-950 mt-1 font-mono-num" id="stat-total-inflow">
                    Rp {{ number_format($report['rekapitulasi']['total_kas_masuk'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] text-emerald-500">Seluruh Penerimaan Kas &amp; Bank</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="arrow-down-left" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 2: Total Kas Keluar -->
        <div class="bg-white p-4 rounded-xl border border-rose-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Total Kas Keluar (Outflow)</p>
                <h3 class="text-xl font-black text-rose-950 mt-1 font-mono-num" id="stat-total-outflow">
                    Rp {{ number_format($report['rekapitulasi']['total_kas_keluar'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] text-rose-500">Seluruh Pengeluaran Kas &amp; Bank</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <i data-lucide="arrow-up-right" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 3: Kenaikan Bersih -->
        <div class="bg-white p-4 rounded-xl border border-blue-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Perubahan Bersih Kas</p>
                <h3 class="text-xl font-black text-blue-950 mt-1 font-mono-num" id="stat-net-kas">
                    Rp {{ number_format($report['rekapitulasi']['kenaikan_bersih'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] text-blue-500">Arus Masuk - Arus Keluar</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="trending-up" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 4: Saldo Akhir Kas & Bank -->
        <div class="bg-white p-4 rounded-xl border border-indigo-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Saldo Akhir Kas &amp; Bank</p>
                <h3 class="text-xl font-black text-indigo-950 mt-1 font-mono-num" id="stat-saldo-akhir">
                    Rp {{ number_format($report['rekapitulasi']['saldo_akhir_kas'], 0, ',', '.') }}
                </h3>
                <span class="text-[10px] text-indigo-500">Kas Tunai, BCA, &amp; Mandiri</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i data-lucide="landmark" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-4">
        <form id="filter-form" onsubmit="event.preventDefault(); applyFilter();" class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2 text-xs font-bold text-gray-700">
                    <i data-lucide="filter" class="w-4 h-4 text-blue-600"></i>
                    Filter Rentang Tanggal Arus Kas
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
                    <input type="date" name="start_date" id="filter-start-date" value="{{ $meta['start_date'] ?? '' }}" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Sampai Tanggal</label>
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

    <!-- Main Cash Flow Report Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <!-- 1. AKTIVITAS OPERASIONAL -->
        <div class="px-5 py-3.5 bg-blue-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="activity" class="w-4 h-4 text-blue-300"></i>
                <h2 class="text-sm font-black uppercase tracking-wide">1. ARUS KAS DARI AKTIVITAS OPERASIONAL</h2>
            </div>
            <span class="text-xs font-mono font-bold text-blue-200">Net: Rp {{ number_format($report['operasional']['net_operasional'], 0, ',', '.') }}</span>
        </div>

        <div class="p-5 space-y-5">
            <!-- Penerimaan Operasional -->
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-200 text-xs font-bold text-emerald-800">
                    <span class="flex items-center gap-1.5"><i data-lucide="arrow-down-left" class="w-3.5 h-3.5"></i> Penerimaan Kas dari Operasional (Inflow)</span>
                    <span class="font-mono">Rp {{ number_format($report['operasional']['total_inflow'], 0, ',', '.') }}</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($report['operasional']['inflows'] as $item)
                    <div onclick="goToRincian({{ $item['coa_id'] }}, 'cashflow')" class="clickable-account-row py-2.5 px-2 flex items-center justify-between rounded-lg group">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-[11px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-100">{{ $item['kode_akun'] }}</span>
                                <span class="font-bold text-slate-800 group-hover:text-blue-700 transition">{{ $item['nama_akun'] }}</span>
                                <span class="text-[10px] text-slate-400">({{ $item['kategori'] }})</span>
                            </div>
                            <span class="text-[10px] text-slate-400 mt-0.5 ml-0.5">{{ $item['transaksi_count'] }} transaksi kas masuk</span>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-bold text-emerald-800">Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="py-2.5 text-center text-xs text-slate-400">Tidak ada penerimaan kas operasional pada periode ini.</p>
                    @endforelse
                </div>
            </div>

            <!-- Pengeluaran Operasional -->
            <div>
                <div class="flex items-center justify-between pb-2 border-b border-slate-200 text-xs font-bold text-rose-800">
                    <span class="flex items-center gap-1.5"><i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i> Pengeluaran Kas untuk Operasional (Outflow)</span>
                    <span class="font-mono">-Rp {{ number_format($report['operasional']['total_outflow'], 0, ',', '.') }}</span>
                </div>
                <div class="divide-y divide-slate-100 text-xs">
                    @forelse($report['operasional']['outflows'] as $item)
                    <div onclick="goToRincian({{ $item['coa_id'] }}, 'cashflow')" class="clickable-account-row py-2.5 px-2 flex items-center justify-between rounded-lg group">
                        <div class="flex flex-col">
                            <div class="flex items-center gap-2">
                                <span class="font-mono text-[11px] font-bold text-rose-700 bg-rose-50 px-1.5 py-0.5 rounded border border-rose-100">{{ $item['kode_akun'] }}</span>
                                <span class="font-bold text-slate-800 group-hover:text-rose-700 transition">{{ $item['nama_akun'] }}</span>
                                <span class="text-[10px] text-slate-400">({{ $item['kategori'] }})</span>
                            </div>
                            <span class="text-[10px] text-slate-400 mt-0.5 ml-0.5">{{ $item['transaksi_count'] }} transaksi kas keluar</span>
                        </div>
                        <div class="text-right">
                            <span class="font-mono font-bold text-rose-800">-Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @empty
                    <p class="py-2.5 text-center text-xs text-slate-400">Tidak ada pengeluaran kas operasional pada periode ini.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="px-5 py-3 bg-blue-50 border-t border-b border-blue-100 flex items-center justify-between">
            <span class="text-xs font-black text-blue-900 uppercase tracking-wide">ARUS KAS BERSIH DARI AKTIVITAS OPERASIONAL</span>
            <span class="text-sm font-black text-blue-950 font-mono">Rp {{ number_format($report['operasional']['net_operasional'], 0, ',', '.') }}</span>
        </div>

        <!-- 2. AKTIVITAS INVESTASI -->
        <div class="px-5 py-3.5 bg-slate-800 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="layers" class="w-4 h-4 text-slate-300"></i>
                <h2 class="text-sm font-black uppercase tracking-wide">2. ARUS KAS DARI AKTIVITAS INVESTASI</h2>
            </div>
            <span class="text-xs font-mono font-bold text-slate-300">Net: Rp {{ number_format($report['investasi']['net_investasi'], 0, ',', '.') }}</span>
        </div>

        <div class="p-5 space-y-4 text-xs">
            @if(empty($report['investasi']['inflows']) && empty($report['investasi']['outflows']))
            <p class="py-2 text-center text-slate-400">Tidak ada transaksi arus kas investasi (perolehan/penjualan aset tetap) pada periode ini.</p>
            @else
                @foreach($report['investasi']['inflows'] as $item)
                <div onclick="goToRincian({{ $item['coa_id'] }}, 'cashflow')" class="clickable-account-row py-2 px-2 flex justify-between items-center rounded-lg">
                    <span>Penjualan {{ $item['nama_akun'] }}</span>
                    <span class="font-mono font-bold text-emerald-700">Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                </div>
                @endforeach
                @foreach($report['investasi']['outflows'] as $item)
                <div onclick="goToRincian({{ $item['coa_id'] }}, 'cashflow')" class="clickable-account-row py-2 px-2 flex justify-between items-center rounded-lg">
                    <span>Perolehan {{ $item['nama_akun'] }}</span>
                    <span class="font-mono font-bold text-rose-700">-Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            @endif
        </div>

        <div class="px-5 py-3 bg-slate-100 border-t border-b border-slate-200 flex items-center justify-between">
            <span class="text-xs font-black text-slate-900 uppercase tracking-wide">ARUS KAS BERSIH DARI AKTIVITAS INVESTASI</span>
            <span class="text-sm font-black text-slate-950 font-mono">Rp {{ number_format($report['investasi']['net_investasi'], 0, ',', '.') }}</span>
        </div>

        <!-- 3. AKTIVITAS PENDANAAN -->
        <div class="px-5 py-3.5 bg-slate-800 text-white flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i data-lucide="landmark" class="w-4 h-4 text-slate-300"></i>
                <h2 class="text-sm font-black uppercase tracking-wide">3. ARUS KAS DARI AKTIVITAS PENDANAAN</h2>
            </div>
            <span class="text-xs font-mono font-bold text-slate-300">Net: Rp {{ number_format($report['pendanaan']['net_pendanaan'], 0, ',', '.') }}</span>
        </div>

        <div class="p-5 space-y-4 text-xs">
            @if(empty($report['pendanaan']['inflows']) && empty($report['pendanaan']['outflows']))
            <p class="py-2 text-center text-slate-400">Tidak ada transaksi arus kas pendanaan (setoran modal/prive/pinjaman bank) pada periode ini.</p>
            @else
                @foreach($report['pendanaan']['inflows'] as $item)
                <div onclick="goToRincian({{ $item['coa_id'] }}, 'cashflow')" class="clickable-account-row py-2 px-2 flex justify-between items-center rounded-lg">
                    <span>Penerimaan {{ $item['nama_akun'] }}</span>
                    <span class="font-mono font-bold text-emerald-700">Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                </div>
                @endforeach
                @foreach($report['pendanaan']['outflows'] as $item)
                <div onclick="goToRincian({{ $item['coa_id'] }}, 'cashflow')" class="clickable-account-row py-2 px-2 flex justify-between items-center rounded-lg">
                    <span>Penarikan/Pembayaran {{ $item['nama_akun'] }}</span>
                    <span class="font-mono font-bold text-rose-700">-Rp {{ number_format($item['total_nominal'], 0, ',', '.') }}</span>
                </div>
                @endforeach
            @endif
        </div>

        <div class="px-5 py-3 bg-slate-100 border-t border-b border-slate-200 flex items-center justify-between">
            <span class="text-xs font-black text-slate-900 uppercase tracking-wide">ARUS KAS BERSIH DARI AKTIVITAS PENDANAAN</span>
            <span class="text-sm font-black text-slate-950 font-mono">Rp {{ number_format($report['pendanaan']['net_pendanaan'], 0, ',', '.') }}</span>
        </div>

        <!-- 4. REKAPITULASI DAN SALDO KAS AKHIR -->
        <div class="p-6 bg-slate-50 border-t-2 border-slate-900 space-y-3 text-xs">
            <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                <span class="font-bold text-slate-700">KENAIKAN / (PENURUNAN) BERSIH KAS &amp; BANK</span>
                <span class="font-mono font-black text-sm text-slate-900">Rp {{ number_format($report['rekapitulasi']['kenaikan_bersih'], 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center pb-2 border-b border-slate-200">
                <span class="font-bold text-slate-700">SALDO AWAL KAS &amp; BANK (AWAL PERIODE)</span>
                <span class="font-mono font-black text-sm text-slate-900">Rp {{ number_format($report['rekapitulasi']['saldo_awal_kas'], 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center p-3 rounded-xl bg-blue-100/70 border border-blue-200">
                <div>
                    <span class="font-black text-blue-950 uppercase text-xs block">SALDO AKHIR KAS &amp; BANK (AKHIR PERIODE)</span>
                    <span class="text-[10px] text-blue-700">Terverifikasi sesuai dengan posisi akun Kas &amp; Bank pada Neraca</span>
                </div>
                <span class="font-mono font-black text-xl text-blue-950">Rp {{ number_format($report['rekapitulasi']['saldo_akhir_kas'], 0, ',', '.') }}</span>
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

        window.location.href = `{{ route('cashflow.index') }}?${params.toString()}`;
    }

    function resetFilter() {
        window.location.href = `{{ route('cashflow.index') }}`;
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
        window.open(`{{ route('cashflow.cetak') }}?${params.toString()}`, '_blank');
    }

    function exportExcel() {
        const start = document.getElementById('filter-start-date').value;
        const end = document.getElementById('filter-end-date').value;
        const params = new URLSearchParams();
        if (start) params.set('start_date', start);
        if (end) params.set('end_date', end);
        window.location.href = `{{ route('cashflow.export-excel') }}?${params.toString()}`;
    }
</script>
@endpush
