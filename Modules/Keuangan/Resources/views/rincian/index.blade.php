@extends('layouts.app')

@php
    $account = is_array($detail['account']) ? (object) $detail['account'] : $detail['account'];
@endphp

@section('title', 'Rincian Akun ' . ($account->nama_akun ?? 'Akun') . ' - ' . config('app.name'))
@section('page_title', 'Rincian Mutasi Akun')

@push('styles')
<style>
    .font-mono-num {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }
    @media print {
        header, sidebar, nav, .no-print, button, form, .filter-box {
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
    }
</style>
@endpush

@section('content')
<div class="space-y-6 print-container pb-12">
    <!-- Top Bar: Breadcrumb, Account Name, and Action Buttons -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-500 mb-1.5 no-print">
                <span>Keuangan</span>
                <span class="text-slate-300">&bull;</span>
                <a href="{{ $backUrl }}" class="hover:text-blue-600 font-semibold text-slate-600 transition">{{ $sourceReport }}</a>
                <span class="text-slate-300">&bull;</span>
                <span class="text-blue-600 font-bold">Rincian Buku Besar Akun</span>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <span class="font-mono text-sm font-black px-2.5 py-1 rounded-lg bg-blue-50 text-blue-700 border border-blue-200">
                    {{ $account->kode_akun ?? '-' }}
                </span>
                <h1 class="text-xl font-black text-slate-900 tracking-tight">
                    {{ $account->nama_akun ?? 'Akun' }}
                </h1>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase bg-slate-100 text-slate-600 border border-slate-200">
                    {{ $account->kategori ?? ($account->tipe ?? 'Umum') }}
                </span>
                <span class="px-2 py-0.5 rounded text-[11px] font-bold uppercase {{ ($detail['posisi_normal'] ?? 'Debit') === 'Debit' ? 'bg-blue-50 text-blue-700 border border-blue-200' : 'bg-indigo-50 text-indigo-700 border border-indigo-200' }}">
                    Normal: {{ $detail['posisi_normal'] ?? 'Debit' }}
                </span>
            </div>
            <p class="text-xs text-slate-500 mt-1">
                Menampilkan mutasi transaksi lengkap dan saldo berjalan periode: <strong class="text-slate-700">{{ $meta['periode'] ?? 'Semua Periode' }}</strong>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2 no-print">
            <a href="{{ $backUrl }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs transition">
                <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500"></i>
                Kembali ke {{ $sourceReport }}
            </a>

            @if(isset($account->id) && $account->id > 0)
            <a href="{{ route('jurnal.index', ['coa_id' => $account->id, 'start_date' => $filters['start_date'] ?? '', 'end_date' => $filters['end_date'] ?? '']) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-blue-700 bg-blue-50 border border-blue-200 hover:bg-blue-100 shadow-xs transition">
                <i data-lucide="external-link" class="w-4 h-4 text-blue-600"></i>
                Jurnal Umum
            </a>
            @endif

            <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-700 bg-white border border-slate-300 hover:bg-slate-50 shadow-xs transition">
                <i data-lucide="printer" class="w-4 h-4 text-slate-500"></i>
                Cetak Lembar
            </button>
        </div>
    </div>

    <!-- Filter Rentang Tanggal -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs no-print filter-box">
        <form method="GET" action="{{ url()->current() }}" id="filter-form" class="space-y-3">
            <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-slate-500"></i>
                    <span class="text-xs font-bold text-slate-800 uppercase tracking-wider">Filter Periode Transaksi</span>
                </div>
                <!-- Preset Buttons -->
                <div class="flex flex-wrap items-center gap-1.5">
                    <button type="button" onclick="setPreset('today')" class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 transition">Hari Ini</button>
                    <button type="button" onclick="setPreset('this_month')" class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 transition">Bulan Ini</button>
                    <button type="button" onclick="setPreset('this_year')" class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 transition">Tahun Ini</button>
                    <button type="button" onclick="setPreset('all')" class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-slate-100 hover:bg-slate-200 text-slate-700 transition">Semua</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Dari Tanggal (Saldo Awal)</label>
                    <input type="date" name="start_date" id="filter-start-date" value="{{ $filters['start_date'] ?? '' }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="filter-end-date" value="{{ $filters['end_date'] ?? date('Y-m-d') }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <div class="flex items-end gap-2">
                    <button type="button" onclick="resetFilter()" class="flex-1 px-3.5 py-2 rounded-xl text-xs font-bold text-slate-600 bg-white border border-slate-300 hover:bg-slate-50 transition">
                        Reset
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-xs transition">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Saldo Awal -->
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Saldo Awal</span>
                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                    <i data-lucide="archive" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-lg font-black text-slate-900 font-mono-num">
                    Rp {{ number_format($detail['saldo_awal'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="block text-[10px] text-slate-400 mt-0.5">
                    @if(($detail['saldo_awal'] ?? 0) != 0)
                        Akumulasi sebelum {{ $filters['start_date'] ? Carbon\Carbon::parse($filters['start_date'])->format('d M Y') : 'periode berjalan' }}
                    @else
                        Tidak ada transaksi sebelumnya (Rp 0)
                    @endif
                </span>
            </div>
        </div>

        <!-- Total Mutasi Debit -->
        <div class="bg-white p-5 rounded-2xl border border-blue-200/60 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Mutasi Debit (+)</span>
                <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i data-lucide="trending-up" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-lg font-black text-blue-700 font-mono-num">
                    Rp {{ number_format($detail['total_debit'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="block text-[10px] text-blue-500/80 mt-0.5">Penambahan transaksi debit</span>
            </div>
        </div>

        <!-- Total Mutasi Kredit -->
        <div class="bg-white p-5 rounded-2xl border border-indigo-200/60 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Mutasi Kredit (-)</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i data-lucide="trending-down" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-lg font-black text-indigo-700 font-mono-num">
                    Rp {{ number_format($detail['total_kredit'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="block text-[10px] text-indigo-500/80 mt-0.5">Penambahan transaksi kredit</span>
            </div>
        </div>

        <!-- Saldo Akhir -->
        <div class="bg-white p-5 rounded-2xl border border-emerald-200 shadow-xs bg-gradient-to-br from-white to-emerald-50/20">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-emerald-700 uppercase tracking-wider">Saldo Akhir</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-700 flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="mt-2">
                <span class="text-lg font-black text-emerald-800 font-mono-num">
                    Rp {{ number_format($detail['saldo_akhir'] ?? 0, 0, ',', '.') }}
                </span>
                <span class="block text-[10px] text-emerald-600 mt-0.5">Posisi per {{ $filters['end_date'] ? Carbon\Carbon::parse($filters['end_date'])->format('d M Y') : date('d M Y') }}</span>
            </div>
        </div>
    </div>

    <!-- Main Table Container: Rincian Mutasi Transaksi & Saldo Berjalan -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 bg-slate-900 text-white flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2.5">
                <i data-lucide="list" class="w-4 h-4 text-blue-400"></i>
                <h3 class="text-xs font-black uppercase tracking-wider text-white">Histori Buku Besar &amp; Mutasi Transaksi Akun</h3>
            </div>
            <span class="text-xs font-mono font-bold text-slate-300">
                Total: {{ count($detail['entries'] ?? []) }} Transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="bg-slate-100 text-slate-700 uppercase text-[10px] font-bold tracking-wider border-b border-slate-200">
                    <tr>
                        <th class="py-3 px-3 w-12 text-center">No</th>
                        <th class="py-3 px-3 w-28">Tanggal</th>
                        <th class="py-3 px-3 w-36">No. Transaksi</th>
                        <th class="py-3 px-3">Uraian / Keterangan &amp; Kontak</th>
                        <th class="py-3 px-3 w-48">Lawan Akun</th>
                        <th class="py-3 px-3 text-right w-32">Debit (Rp)</th>
                        <th class="py-3 px-3 text-right w-32">Kredit (Rp)</th>
                        <th class="py-3 px-3 text-right w-36 bg-slate-200/50">Saldo Berjalan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-sans text-slate-800">
                    <!-- Baris Saldo Awal Periode (Hanya tampil jika ada riwayat transaksi sebelumnya) -->
                    @if(($detail['saldo_awal'] ?? 0) != 0)
                    <tr class="bg-slate-50/70 font-semibold text-slate-600">
                        <td class="py-2.5 px-3 text-center text-slate-400">&bull;</td>
                        <td class="py-2.5 px-3 font-mono text-[11px]">{{ $filters['start_date'] ? Carbon\Carbon::parse($filters['start_date'])->format('d/m/Y') : '-' }}</td>
                        <td class="py-2.5 px-3 font-mono text-[11px] text-slate-400">-</td>
                        <td class="py-2.5 px-3 italic text-slate-700 font-bold" colspan="2">
                            [ SALDO AWAL DARI TRANSAKSI SEBELUMNYA ]
                        </td>
                        <td class="py-2.5 px-3 text-right font-mono-num text-slate-400">-</td>
                        <td class="py-2.5 px-3 text-right font-mono-num text-slate-400">-</td>
                        <td class="py-2.5 px-3 text-right font-mono-num font-bold text-slate-900 bg-slate-100/50">
                            Rp {{ number_format($detail['saldo_awal'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                    @endif

                    @forelse($detail['entries'] as $idx => $entry)
                    <tr class="hover:bg-slate-50/80 transition">
                        <td class="py-3 px-3 text-center font-medium text-slate-400">
                            {{ $idx + 1 }}
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap font-medium text-slate-800 font-mono text-[11px]">
                            {{ Carbon\Carbon::parse($entry->tanggal)->format('d/m/Y') }}
                        </td>
                        <td class="py-3 px-3 whitespace-nowrap">
                            @if(($entry->jenis ?? '') === 'masuk')
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    {{ $entry->nomor_transaksi }}
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    {{ $entry->nomor_transaksi }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            <div class="font-semibold text-slate-900">{{ $entry->keterangan ?: '-' }}</div>
                            @if(!empty($entry->penerima_pembayar) || !empty($entry->nomor_referensi))
                            <div class="text-[11px] text-slate-400 mt-0.5 flex flex-wrap items-center gap-1.5">
                                @if(!empty($entry->penerima_pembayar))
                                <span>Pihak: <strong class="text-slate-600">{{ $entry->penerima_pembayar }}</strong></span>
                                @endif
                                @if(!empty($entry->nomor_referensi))
                                <span>Ref: {{ $entry->nomor_referensi }}</span>
                                @endif
                            </div>
                            @endif
                        </td>
                        <td class="py-3 px-3">
                            @if(!empty($entry->lawan_nama_akun))
                                <div class="font-medium text-slate-800">{{ $entry->lawan_nama_akun }}</div>
                                <span class="font-mono text-[10px] text-slate-400">{{ $entry->lawan_kode_akun }}</span>
                            @else
                                <span class="text-slate-400 italic text-[11px]">-</span>
                            @endif
                        </td>
                        <td class="py-3 px-3 text-right font-mono-num font-bold text-blue-700">
                            {{ (float) $entry->debit > 0 ? 'Rp ' . number_format($entry->debit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono-num font-bold text-indigo-700">
                            {{ (float) $entry->kredit > 0 ? 'Rp ' . number_format($entry->kredit, 0, ',', '.') : '-' }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono-num font-bold text-slate-900 bg-slate-50/50">
                            Rp {{ number_format($entry->saldo_berjalan, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-slate-400">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto mb-2 text-slate-300"></i>
                            <p class="font-medium text-xs">Tidak ada mutasi transaksi untuk akun ini pada periode yang dipilih.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-100 border-t-2 border-slate-300 font-bold text-slate-900">
                    <tr>
                        <td colspan="5" class="py-3 px-3 text-right uppercase tracking-wider text-xs">
                            TOTAL MUTASI PERIODE INI:
                        </td>
                        <td class="py-3 px-3 text-right font-mono-num text-sm text-blue-900">
                            Rp {{ number_format($detail['total_debit'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono-num text-sm text-indigo-900">
                            Rp {{ number_format($detail['total_kredit'] ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-3 text-right font-mono-num text-sm text-emerald-800 bg-emerald-50 border-l border-emerald-200">
                            Rp {{ number_format($detail['saldo_akhir'] ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Educational Footer Notice -->
    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 flex items-start gap-2.5 no-print">
        <i data-lucide="info" class="w-4 h-4 text-blue-600 shrink-0 mt-0.5"></i>
        <div class="leading-relaxed text-[11px]">
            <strong>Catatan Audit &amp; Integritas Data:</strong> Seluruh baris transaksi di atas dihitung secara langsung dari <em>Model Jurnal Umum (Double-Entry Engine)</em> tanpa duplikasi query tabel lain. Saldo berjalan bertambah atau berkurang secara matematis sesuai dengan posisi saldo normal akun ini (<strong>{{ $detail['posisi_normal'] ?? 'Debit' }}</strong>).
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
        document.getElementById('filter-form').submit();
    }

    function resetFilter() {
        const startInput = document.getElementById('filter-start-date');
        const endInput = document.getElementById('filter-end-date');
        startInput.value = '';
        endInput.value = '';
        document.getElementById('filter-form').submit();
    }
</script>
@endpush
