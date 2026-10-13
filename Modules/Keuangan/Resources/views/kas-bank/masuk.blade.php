@extends('layouts.app')

@section('title', 'Transaksi Kas & Bank Masuk - ' . config('app.name'))
@section('page_title', 'Kas / Bank Masuk')

@push('styles')
<style>
    #kas-masuk-table_wrapper .dataTables_length select {
        padding: 0.25rem 1.75rem 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    #kas-masuk-table_wrapper .dataTables_filter input {
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        outline: none;
    }

    /* Select2 Custom Modern Theme */
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        height: 42px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.75rem !important;
        padding: 6px 12px !important;
        background-color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        transition: all 0.2s ease-in-out;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #059669 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.18) !important;
        outline: none !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #1e293b !important;
        font-size: 0.8125rem !important;
        line-height: 1.25rem !important;
        padding-left: 0 !important;
        font-weight: 600 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8 !important;
        font-weight: normal !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px !important;
        right: 10px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__clear {
        color: #94a3b8 !important;
        font-size: 1.125rem !important;
        margin-right: 22px !important;
        line-height: 38px !important;
    }
    .select2-dropdown {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.875rem !important;
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.12), 0 10px 15px -6px rgba(0, 0, 0, 0.08) !important;
        overflow: hidden !important;
        z-index: 99999 !important;
        background-color: #ffffff !important;
    }
    .select2-search--dropdown {
        padding: 8px 10px !important;
        background-color: #f8fafc !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.5rem !important;
        padding: 7px 12px !important;
        font-size: 0.8125rem !important;
        outline: none !important;
        background-color: #ffffff !important;
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #059669 !important;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.15) !important;
    }
    .select2-results__option {
        padding: 8px 12px !important;
        font-size: 0.8125rem !important;
        border-bottom: 1px solid #f8fafc;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #ecfdf5 !important;
        color: #065f46 !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #d1fae5 !important;
        color: #065f46 !important;
        font-weight: 600 !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Top Action & Overview Header -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold mb-1.5 border border-emerald-200">
                <i data-lucide="arrow-down-left" class="w-3.5 h-3.5"></i>
                Penerimaan Kas & Rekening Bank
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Transaksi Kas & Bank Masuk</h1>
            <p class="text-xs text-gray-500">Pencatatan uang masuk ke <strong>Kas & Bank (CoA Aset Lancar)</strong> dengan akun lawan <strong>Beban / Pendapatan (CoA)</strong>.</p>
        </div>
        <div class="flex items-center gap-2">
            @if(check_access('kas-bank-masuk.create'))
            <a href="{{ route('kas-bank-masuk.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Catat Kas Masuk
            </a>
            @endif
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Bulan Ini -->
        <div class="bg-white p-4 rounded-xl border border-emerald-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Kas Masuk Bulan Ini</p>
                <h3 class="text-xl font-black text-gray-900 mt-1" id="stat-total-bulan-ini">
                    <span class="inline-block w-24 h-6 bg-slate-200 rounded animate-pulse"></span>
                </h3>
                <span class="text-[10px] text-gray-500" id="stat-count-bulan-ini">Memuat data...</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="trending-up" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 2: Hari Ini -->
        <div class="bg-white p-4 rounded-xl border border-teal-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-teal-600 uppercase tracking-wider">Kas Masuk Hari Ini</p>
                <h3 class="text-xl font-black text-gray-900 mt-1" id="stat-total-hari-ini">
                    <span class="inline-block w-24 h-6 bg-slate-200 rounded animate-pulse"></span>
                </h3>
                <span class="text-[10px] text-gray-500">Penerimaan hari ini</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <i data-lucide="calendar" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 3: Total Akumulasi -->
        <div class="bg-white p-4 rounded-xl border border-blue-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Total Akumulasi Masuk</p>
                <h3 class="text-xl font-black text-gray-900 mt-1" id="stat-total-akumulasi">
                    <span class="inline-block w-24 h-6 bg-slate-200 rounded animate-pulse"></span>
                </h3>
                <span class="text-[10px] text-blue-500">Seluruh riwayat transaksi</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="wallet" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 4: Akun Kas Aktif -->
        <div class="bg-white p-4 rounded-xl border border-indigo-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Akun Kas & Bank</p>
                <h3 class="text-xl font-black text-gray-900 mt-1" id="stat-total-akun">{{ count($kasBankList) }} Akun</h3>
                <span class="text-[10px] text-indigo-500">Terdaftar di CoA Neraca</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i data-lucide="credit-card" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    <!-- Saldo Berjalan Kas & Bank Real-time Bar -->
    <div class="bg-white p-4 rounded-2xl border border-gray-200 shadow-xs">
        <div class="flex items-center justify-between mb-3">
            <span class="text-xs font-bold text-gray-700 flex items-center gap-1.5">
                <i data-lucide="pie-chart" class="w-4 h-4 text-emerald-600"></i>
                Saldo Terkini Kas & Rekening Bank (CoA)
            </span>
            <span class="text-[11px] text-gray-400">Saldo Awal + Masuk - Keluar</span>
        </div>
        <div id="container-saldo-kas-bank" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div class="col-span-full py-4 text-center text-xs text-gray-400">
                <i data-lucide="loader-2" class="w-5 h-5 animate-spin mx-auto text-emerald-600 mb-1"></i>
                Memuat saldo akun kas & bank...
            </div>
        </div>
    </div>

    <!-- Filter & Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        <!-- Filter Controls -->
        <div class="p-4 border-b border-gray-100 bg-gray-50/60 flex flex-wrap items-center justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2 w-full lg:w-auto">
                <div>
                    <input type="date" id="filter-start-date" class="px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs text-gray-700 bg-white" title="Dari Tanggal">
                </div>
                <span class="text-xs text-gray-400">s/d</span>
                <div>
                    <input type="date" id="filter-end-date" class="px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs text-gray-700 bg-white" title="Sampai Tanggal">
                </div>
                <div>
                    <select id="filter-kas-bank" class="px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs text-gray-700 bg-white">
                        <option value="">Semua Akun Kas/Bank</option>
                        @foreach($kasBankList as $kb)
                            <option value="{{ $kb->id }}">{{ $kb->kode_akun }} - {{ $kb->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select id="filter-lawan" class="px-2.5 py-1.5 rounded-lg border border-gray-300 text-xs text-gray-700 bg-white">
                        <option value="">Semua Akun Lawan</option>
                        <optgroup label="Akun Beban (CoA)">
                            @foreach($bebanList as $bb)
                                <option value="{{ $bb->id }}">{{ $bb->kode_akun }} - {{ $bb->nama_akun }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Akun Pendapatan (CoA)">
                            @foreach($pendapatanList as $pd)
                                <option value="{{ $pd->id }}">{{ $pd->kode_akun }} - {{ $pd->nama_akun }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                </div>
                <button type="button" onclick="applyFilter()" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition">
                    Filter
                </button>
                <button type="button" onclick="resetFilter()" class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 transition">
                    Reset
                </button>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" onclick="exportExcel()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 hover:bg-emerald-100 transition shadow-xs">
                    <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5"></i>
                    Export Excel
                </button>
                <button type="button" onclick="cetakRekap()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 transition shadow-xs">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    Cetak Rekapan
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="p-4 overflow-x-auto">
            <table id="kas-masuk-table" class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-gray-200 text-[11px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50">
                        <th class="py-3 px-3">No</th>
                        <th class="py-3 px-3">No. Transaksi & Tanggal</th>
                        <th class="py-3 px-3">Akun (Debet / Kredit)</th>
                        <th class="py-3 px-3">Diterima Dari</th>
                        <th class="py-3 px-3 text-right">Nominal</th>
                        <th class="py-3 px-3">Keterangan</th>
                        <th class="py-3 px-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Populated by DataTables -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Transaksi -->
<div id="modal-detail" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 backdrop-blur-xs p-3 sm:p-6">
    <div class="min-h-full flex items-start sm:items-center justify-center py-6 sm:py-10">
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden transform transition-all">
            <div class="p-5 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                        <i data-lucide="file-text" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Rincian Transaksi Kas Masuk</h3>
                        <p class="text-[11px] text-slate-500">Informasi voucher dan rincian jurnal</p>
                    </div>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-slate-400 hover:text-slate-600 p-1.5 rounded-lg hover:bg-slate-100 transition">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-6 space-y-3.5 text-xs" id="detail-content">
                <!-- Populated by JS -->
            </div>
            <div class="p-4 border-t border-slate-100 bg-slate-50/80 flex items-center justify-between">
                <a id="btn-cetak-voucher" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-emerald-700 bg-emerald-100/80 hover:bg-emerald-200 transition">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Cetak Lembar Voucher
                </a>
                <button type="button" onclick="closeDetailModal()" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-white transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var table;

    $(document).ready(function() {
        lucide.createIcons();
        table = $('#kas-masuk-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('kas-bank-masuk.data') }}",
                data: function(d) {
                    d.start_date = $('#filter-start-date').val();
                    d.end_date = $('#filter-end-date').val();
                    d.kas_bank_id = $('#filter-kas-bank').val();
                    d.lawan_coa_id = $('#filter-lawan').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nomor_transaksi', name: 'nomor_transaksi' },
                { data: 'akun', name: 'akun' },
                { data: 'penerima_pembayar', name: 'penerima_pembayar' },
                { data: 'nominal', name: 'nominal' },
                { data: 'keterangan', name: 'keterangan' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ],
            order: [[1, 'desc']],
            drawCallback: function() {
                lucide.createIcons();
            }
        });

        loadStats();
    });

    function loadStats() {
        $.get("{{ route('kas-bank-masuk.stats') }}", function(res) {
            if (res.status === 'success') {
                var d = res.data;
                $('#stat-total-bulan-ini').text('Rp ' + d.total_bulan_ini_formatted);
                $('#stat-count-bulan-ini').text(d.count_bulan_ini + ' transaksi bulan ini');
                $('#stat-total-hari-ini').text('Rp ' + d.total_hari_ini_formatted);
                $('#stat-total-akumulasi').text('Rp ' + d.total_akumulasi_formatted);
                $('#stat-total-akun').text(d.total_akun + ' Akun');

                var saldoHtml = '';
                if (d.saldo_kas_bank && d.saldo_kas_bank.length > 0) {
                    $.each(d.saldo_kas_bank, function(idx, kb) {
                        saldoHtml += `
                            <div class="p-3 rounded-xl border border-slate-100 bg-slate-50/70 flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold text-gray-800">${kb.nama_akun}</span>
                                        <span class="text-[10px] font-mono px-1.5 py-0.2 rounded bg-white text-gray-600 border border-gray-200">${kb.kode_akun}</span>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        Masuk: <span class="text-emerald-600 font-semibold font-mono">+${kb.total_masuk_formatted}</span>
                                        | Keluar: <span class="text-rose-600 font-semibold font-mono">-${kb.total_keluar_formatted}</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-xs font-mono font-bold text-gray-900">Rp ${kb.saldo_akhir_formatted}</span>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    saldoHtml = '<div class="col-span-full py-2 text-center text-xs text-gray-400">Belum ada akun kas & bank terdaftar.</div>';
                }
                $('#container-saldo-kas-bank').html(saldoHtml);
            }
        });
    }

    function applyFilter() {
        table.ajax.reload();
    }

    function resetFilter() {
        $('#filter-start-date').val('');
        $('#filter-end-date').val('');
        $('#filter-kas-bank').val('');
        $('#filter-lawan').val('');
        table.ajax.reload();
    }

    function getFilterParams() {
        var params = new URLSearchParams();
        var startDate = $('#filter-start-date').val();
        var endDate = $('#filter-end-date').val();
        var kasBank = $('#filter-kas-bank').val();
        var lawan = $('#filter-lawan').val();

        if (startDate) params.append('start_date', startDate);
        if (endDate) params.append('end_date', endDate);
        if (kasBank) params.append('kas_bank_id', kasBank);
        if (lawan) params.append('lawan_coa_id', lawan);

        return params.toString();
    }

    function cetakRekap() {
        var qs = getFilterParams();
        var url = "{{ route('kas-bank-masuk.cetak') }}" + (qs ? '?' + qs : '');
        window.open(url, '_blank');
    }

    function exportExcel() {
        var qs = getFilterParams();
        var url = "{{ route('kas-bank-masuk.export-excel') }}" + (qs ? '?' + qs : '');
        window.location.href = url;
    }

    function showDetail(id) {
        $.get("{{ url('kas-bank-masuk') }}/" + id, function(res) {
            if (res.status === 'success') {
                var d = res.data;
                var html = `
                    <div class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                        <div><span class="text-gray-400">No. Transaksi:</span><p class="font-mono font-bold text-gray-900">${d.nomor_transaksi}</p></div>
                        <div><span class="text-gray-400">Tanggal:</span><p class="font-bold text-gray-900">${d.formatted_tanggal}</p></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                        <div><span class="text-gray-400">Kas / Bank (Debit):</span><p class="font-bold text-emerald-700">${d.kas_bank ? d.kas_bank.nama_akun : '-'}</p><p class="text-[11px] font-mono text-gray-500">${d.kas_bank ? d.kas_bank.kode_akun : ''}</p></div>
                        <div><span class="text-gray-400">Akun Lawan (Kredit):</span><p class="font-bold text-gray-900">${d.lawan_coa ? d.lawan_coa.nama_akun : '-'}</p><p class="text-[11px] font-mono text-gray-500">${d.lawan_coa ? d.lawan_coa.kode_akun : ''}</p></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                        <div><span class="text-gray-400">Nominal:</span><p class="text-base font-mono font-black text-emerald-600">+ Rp ${d.formatted_nominal}</p></div>
                        <div><span class="text-gray-400">Metode:</span><p class="font-bold text-gray-800">${d.metode_pembayaran || '-'}</p></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                        <div><span class="text-gray-400">Diterima Dari:</span><p class="font-medium text-gray-800">${d.penerima_pembayar || '-'}</p></div>
                        <div><span class="text-gray-400">No. Referensi:</span><p class="font-mono text-gray-800">${d.nomor_referensi || '-'}</p></div>
                    </div>
                    <div class="border-b border-gray-100 pb-2">
                        <span class="text-gray-400">Keterangan:</span>
                        <p class="text-gray-700 mt-0.5">${d.keterangan || '-'}</p>
                    </div>
                `;

                if (d.lampiran_url) {
                    html += `
                        <div>
                            <span class="text-gray-400 block mb-1">Bukti Transaksi:</span>
                            <a href="${d.lampiran_url}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-gray-200 text-xs text-blue-600 hover:bg-blue-50 font-medium">
                                <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                Buka File Lampiran
                            </a>
                        </div>
                    `;
                }

                $('#detail-content').html(html);
                $('#btn-cetak-voucher').attr('href', "{{ url('kas-bank/voucher') }}/" + d.id);
                $('#modal-detail').removeClass('hidden');
                lucide.createIcons();
            }
        });
    }

    function closeDetailModal() {
        $('#modal-detail').addClass('hidden');
    }

    function deleteTransaksi(id) {
        Swal.fire({
            title: 'Hapus Transaksi?',
            text: 'Data transaksi kas masuk ini akan dihapus dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('kas-bank-masuk') }}/" + id,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            table.ajax.reload(null, false);
                            loadStats();
                            Swal.fire('Terhapus!', res.message, 'success');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Gagal!', 'Gagal menghapus transaksi.', 'error');
                    }
                });
            }
        });
    }
</script>
@endpush
