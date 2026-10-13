@extends('layouts.app')

@section('title', 'Transaksi Kas & Bank Keluar - ' . config('app.name'))
@section('page_title', 'Kas / Bank Keluar')

@push('styles')
<style>
    #kas-keluar-table_wrapper .dataTables_length select {
        padding: 0.25rem 1.75rem 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    #kas-keluar-table_wrapper .dataTables_filter input {
        padding: 0.35rem 0.75rem;
        font-size: 0.75rem;
        border-radius: 0.5rem;
        border: 1px solid #d1d5db;
        outline: none;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Top Action & Overview Header -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-bold mb-1.5 border border-rose-200">
                <i data-lucide="arrow-up-right" class="w-3.5 h-3.5"></i>
                Pengeluaran Kas & Rekening Bank
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Transaksi Kas & Bank Keluar</h1>
            <p class="text-xs text-gray-500">Pencatatan pengeluaran dana dari <strong>Kas & Bank (CoA)</strong> untuk pembayaran <strong>Beban Operasional (CoA Beban)</strong>.</p>
        </div>
        <div class="flex items-center gap-2">
            @if(check_access('kas-bank-keluar.create'))
            <a href="{{ route('kas-bank-keluar.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-rose-600 to-red-700 hover:from-rose-700 hover:to-red-800 shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Catat Kas Keluar
            </a>
            @endif
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Bulan Ini -->
        <div class="bg-white p-4 rounded-xl border border-rose-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-rose-600 uppercase tracking-wider">Kas Keluar Bulan Ini</p>
                <h3 class="text-xl font-black text-gray-900 mt-1" id="stat-total-bulan-ini">
                    <span class="inline-block w-24 h-6 bg-slate-200 rounded animate-pulse"></span>
                </h3>
                <span class="text-[10px] text-gray-500" id="stat-count-bulan-ini">Memuat data...</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center">
                <i data-lucide="trending-down" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 2: Hari Ini -->
        <div class="bg-white p-4 rounded-xl border border-amber-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Kas Keluar Hari Ini</p>
                <h3 class="text-xl font-black text-gray-900 mt-1" id="stat-total-hari-ini">
                    <span class="inline-block w-24 h-6 bg-slate-200 rounded animate-pulse"></span>
                </h3>
                <span class="text-[10px] text-gray-500">Pengeluaran hari ini</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                <i data-lucide="calendar" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 3: Total Akumulasi -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-600 uppercase tracking-wider">Total Akumulasi Keluar</p>
                <h3 class="text-xl font-black text-gray-900 mt-1" id="stat-total-akumulasi">
                    <span class="inline-block w-24 h-6 bg-slate-200 rounded animate-pulse"></span>
                </h3>
                <span class="text-[10px] text-gray-500">Seluruh pengeluaran beban</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-gray-100 text-gray-600 flex items-center justify-center">
                <i data-lucide="receipt" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 4: Akun Kas Aktif -->
        <div class="bg-white p-4 rounded-xl border border-indigo-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Akun Kas & Bank</p>
                <h3 class="text-xl font-black text-gray-900 mt-1" id="stat-total-akun">{{ count($kasBankList) }} Akun</h3>
                <span class="text-[10px] text-indigo-500">Sumber dana pembayaran</span>
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
                <i data-lucide="pie-chart" class="w-4 h-4 text-rose-600"></i>
                Saldo Terkini Kas & Rekening Bank (CoA)
            </span>
            <span class="text-[11px] text-gray-400">Saldo Awal + Masuk - Keluar</span>
        </div>
        <div id="container-saldo-kas-bank" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div class="col-span-full py-4 text-center text-xs text-gray-400">
                <i data-lucide="loader-2" class="w-5 h-5 animate-spin mx-auto text-rose-600 mb-1"></i>
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
                        <option value="">Semua Akun Beban (CoA)</option>
                        @foreach($bebanList as $bb)
                            <option value="{{ $bb->id }}">{{ $bb->kode_akun }} - {{ $bb->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="button" onclick="applyFilter()" class="px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 transition">
                    Filter
                </button>
                <button type="button" onclick="resetFilter()" class="px-3 py-1.5 rounded-lg text-xs font-bold text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 transition">
                    Reset
                </button>
            </div>
        </div>

        <!-- Table -->
        <div class="p-4 overflow-x-auto">
            <table id="kas-keluar-table" class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-gray-200 text-[11px] font-bold text-gray-400 uppercase tracking-wider bg-gray-50/50">
                        <th class="py-3 px-3">No</th>
                        <th class="py-3 px-3">No. Transaksi & Tanggal</th>
                        <th class="py-3 px-3">Akun (Debet / Kredit)</th>
                        <th class="py-3 px-3">Dibayarkan Kepada</th>
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
        <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all">
            <div class="p-5 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="h-8 w-8 rounded-lg bg-rose-100 text-rose-700 flex items-center justify-center font-bold">
                        <i data-lucide="file-text" class="w-4 h-4"></i>
                    </div>
                    <h3 class="text-sm font-black text-gray-900">Rincian Transaksi Kas Keluar</h3>
                </div>
                <button type="button" onclick="closeDetailModal()" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="p-6 space-y-3.5 text-xs" id="detail-content">
                <!-- Populated by JS -->
            </div>
            <div class="p-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between">
                <a id="btn-cetak-voucher" href="#" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-rose-700 bg-rose-100 hover:bg-rose-200 transition">
                    <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                    Cetak Lembar Voucher
                </a>
                <button type="button" onclick="closeDetailModal()" class="px-3.5 py-1.5 rounded-lg border border-gray-200 text-xs font-bold text-gray-600 hover:bg-white transition">
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

        table = $('#kas-keluar-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('kas-bank-keluar.data') }}",
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
        $.get("{{ route('kas-bank-keluar.stats') }}", function(res) {
            if (res.status === 'success') {
                var d = res.data;
                $('#stat-total-bulan-ini').text('Rp ' + d.total_bulan_ini_formatted);
                $('#stat-count-bulan-ini').text(d.count_bulan_ini + ' transaksi pengeluaran');
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

    function showDetail(id) {
        $.get("{{ url('kas-bank-keluar') }}/" + id, function(res) {
            if (res.status === 'success') {
                var d = res.data;
                var html = `
                    <div class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                        <div><span class="text-gray-400">No. Transaksi:</span><p class="font-mono font-bold text-gray-900">${d.nomor_transaksi}</p></div>
                        <div><span class="text-gray-400">Tanggal:</span><p class="font-bold text-gray-900">${d.formatted_tanggal}</p></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                        <div><span class="text-gray-400">Kas / Bank (Kredit):</span><p class="font-bold text-rose-700">${d.kas_bank ? d.kas_bank.nama_akun : '-'}</p><p class="text-[11px] font-mono text-gray-500">${d.kas_bank ? d.kas_bank.kode_akun : ''}</p></div>
                        <div><span class="text-gray-400">Akun Beban (Debit):</span><p class="font-bold text-gray-900">${d.lawan_coa ? d.lawan_coa.nama_akun : '-'}</p><p class="text-[11px] font-mono text-gray-500">${d.lawan_coa ? d.lawan_coa.kode_akun : ''}</p></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                        <div><span class="text-gray-400">Nominal:</span><p class="text-base font-mono font-black text-rose-600">- Rp ${d.formatted_nominal}</p></div>
                        <div><span class="text-gray-400">Metode:</span><p class="font-bold text-gray-800">${d.metode_pembayaran || '-'}</p></div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 border-b border-gray-100 pb-2">
                        <div><span class="text-gray-400">Dibayarkan Kepada:</span><p class="font-medium text-gray-800">${d.penerima_pembayar || '-'}</p></div>
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
                            <span class="text-gray-400 block mb-1">Bukti Pengeluaran:</span>
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
            text: 'Data transaksi kas keluar ini akan dihapus dari sistem.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "{{ url('kas-bank-keluar') }}/" + id,
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
