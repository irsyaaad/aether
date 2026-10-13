@extends('layouts.app')

@section('title', 'Jurnal Umum - ' . config('app.name'))
@section('page_title', 'Jurnal Umum')

@push('styles')
<style>
    .font-mono-num {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    }

    #jurnal-table_wrapper .dataTables_length select {
        padding: 0.25rem 1.75rem 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    #jurnal-table_wrapper .dataTables_filter input {
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
        height: 38px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.75rem !important;
        padding: 4px 10px !important;
        background-color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        transition: all 0.2s ease-in-out;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
        outline: none !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        color: #1e293b !important;
        font-size: 0.75rem !important;
        line-height: 1.25rem !important;
        padding-left: 0 !important;
        font-weight: 600 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8 !important;
        font-weight: normal !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
        right: 8px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__clear {
        color: #94a3b8 !important;
        font-size: 1.1rem !important;
        margin-right: 20px !important;
        line-height: 34px !important;
    }
    .select2-dropdown {
        border: 1px solid #e2e8f0 !important;
        border-radius: 0.75rem !important;
        box-shadow: 0 15px 30px -5px rgba(0, 0, 0, 0.12), 0 10px 15px -6px rgba(0, 0, 0, 0.08) !important;
        overflow: hidden !important;
        z-index: 99999 !important;
        background-color: #ffffff !important;
    }
    .select2-search--dropdown {
        padding: 6px 8px !important;
        background-color: #f8fafc !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.5rem !important;
        padding: 6px 10px !important;
        font-size: 0.75rem !important;
        outline: none !important;
        background-color: #ffffff !important;
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15) !important;
    }
    .select2-results__option {
        padding: 6px 10px !important;
        font-size: 0.75rem !important;
        border-bottom: 1px solid #f8fafc;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #eff6ff !important;
        color: #1e40af !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #dbeafe !important;
        color: #1e40af !important;
        font-weight: 600 !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Header & Action Top Bar -->
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-xs font-bold mb-1.5 border border-blue-200">
                <i data-lucide="book-open" class="w-3.5 h-3.5"></i>
                Buku Jurnal Umum (General Journal)
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Laporan Jurnal Umum</h1>
            <p class="text-xs text-gray-500">Pencatatan pembukuan berpasangan (<em>Double-Entry Bookkeeping</em>) transaksi keuangan secara kronologis.</p>
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

    <!-- KPI Summary Cards (Loaded via AJAX Serverside) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Transaksi -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Transaksi</p>
                <h3 class="text-xl font-black text-slate-900 mt-1" id="stat-total-transaksi">
                    <span class="inline-block w-20 h-6 bg-slate-200 rounded animate-pulse"></span>
                </h3>
                <span class="text-[10px] text-slate-400" id="stat-total-baris">Memuat data...</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                <i data-lucide="layers" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 2: Total Debit -->
        <div class="bg-white p-4 rounded-xl border border-blue-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Total Debit</p>
                <h3 class="text-xl font-black text-blue-900 mt-1 font-mono-num" id="stat-total-debit">
                    <span class="inline-block w-28 h-6 bg-blue-100 rounded animate-pulse"></span>
                </h3>
                <span class="text-[10px] text-blue-500">Mutasi Masuk & Penambahan Beban</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="arrow-down-left" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 3: Total Kredit -->
        <div class="bg-white p-4 rounded-xl border border-indigo-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Total Kredit</p>
                <h3 class="text-xl font-black text-indigo-900 mt-1 font-mono-num" id="stat-total-kredit">
                    <span class="inline-block w-28 h-6 bg-indigo-100 rounded animate-pulse"></span>
                </h3>
                <span class="text-[10px] text-indigo-500">Mutasi Keluar & Penambahan Pendapatan</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i data-lucide="arrow-up-right" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 4: Status Keseimbangan (Balance) -->
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-xs flex items-center justify-between" id="card-keseimbangan">
            <div>
                <p class="text-[11px] font-bold text-slate-600 uppercase tracking-wider" id="stat-keseimbangan-title">Keseimbangan</p>
                <h3 class="text-base font-black text-slate-800 mt-1 flex items-center gap-1.5" id="stat-keseimbangan">
                    <span class="inline-block w-24 h-6 bg-slate-200 rounded animate-pulse"></span>
                </h3>
                <span class="text-[10px] text-slate-400" id="stat-selisih">Menghitung keseimbangan...</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center" id="icon-keseimbangan">
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
                    Filter Laporan Jurnal
                </div>
                <!-- Quick Date Presets -->
                <div class="flex flex-wrap items-center gap-1.5">
                    <button type="button" onclick="setPreset('today')" class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition">Hari Ini</button>
                    <button type="button" onclick="setPreset('this_month')" class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition">Bulan Ini</button>
                    <button type="button" onclick="setPreset('this_year')" class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition">Tahun Ini</button>
                    <button type="button" onclick="setPreset('all')" class="px-2.5 py-1 rounded-md text-[11px] font-medium bg-gray-100 hover:bg-gray-200 text-gray-700 transition">Semua</button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                <!-- Dari Tanggal -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Dari Tanggal</label>
                    <input type="date" name="start_date" id="filter-start-date" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <!-- Sampai Tanggal -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" id="filter-end-date" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                </div>

                <!-- Jenis Transaksi -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Jenis Transaksi</label>
                    <select name="jenis" id="filter-jenis" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
                        <option value="">Semua Jenis Transaksi</option>
                        <option value="masuk">Kas / Bank Masuk (KM)</option>
                        <option value="keluar">Kas / Bank Keluar (KK)</option>
                    </select>
                </div>

                <!-- Filter Akun CoA (Select2 AJAX) -->
                <div>
                    <label class="block text-[11px] font-bold text-gray-600 mb-1">Filter Akun (CoA)</label>
                    <select name="coa_id" id="filter-coa" class="w-full" data-placeholder="Ketik kode / nama akun..."></select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-2">
                <span class="text-xs text-gray-500" id="filter-periode-label">
                    Menampilkan data jurnal periode berjalan
                </span>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="resetFilter()" class="px-3.5 py-1.5 rounded-xl text-xs font-bold text-gray-600 bg-white border border-gray-300 hover:bg-gray-50 transition">
                        Reset Filter
                    </button>
                    <button type="button" onclick="applyFilter()" class="px-4 py-1.5 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-xs transition">
                        Terapkan Filter
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Main Journal DataTables Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50/70 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-800 uppercase tracking-wide">Daftar Entri Jurnal Umum</span>
                <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700" id="badge-total-entries">
                    Server-Side AJAX
                </span>
            </div>
            <div class="text-[11px] text-gray-500">
                Kolom <strong>Tanggal, No. Bukti, &amp; Keterangan</strong> digabung menjadi 1 kolom hemat tempat
            </div>
        </div>

        <div class="p-4 overflow-x-auto">
            <table id="jurnal-table" class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold border-b border-slate-800">
                        <th class="py-3 px-3 text-center w-12">No</th>
                        <th class="py-3 px-3">Tanggal, No. Bukti &amp; Keterangan</th>
                        <th class="py-3 px-3 w-64">Akun (CoA)</th>
                        <th class="py-3 px-3 text-center w-24">Posisi</th>
                        <th class="py-3 px-3 text-right w-36">Debit (Rp)</th>
                        <th class="py-3 px-3 text-right w-36">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-800">
                    <!-- Loaded dynamically via DataTables AJAX -->
                </tbody>
                <tfoot>
                    <tr class="bg-slate-100 border-t-2 border-slate-300 font-bold text-slate-900">
                        <td colspan="4" class="py-3 px-3 text-right uppercase tracking-wider text-xs">
                            TOTAL JURNAL UMUM:
                        </td>
                        <td class="py-3 px-3 text-right font-mono-num text-sm text-blue-900 border-l border-slate-200" id="footer-total-debit">
                            Rp 0
                        </td>
                        <td class="py-3 px-3 text-right font-mono-num text-sm text-indigo-900 border-l border-slate-200" id="footer-total-kredit">
                            Rp 0
                        </td>
                    </tr>
                    <tr class="bg-slate-50 border-t border-slate-200 text-xs font-bold" id="footer-row-status">
                        <td colspan="4" class="py-2 px-3 text-right uppercase">
                            STATUS KESEIMBANGAN:
                        </td>
                        <td colspan="2" class="py-2 px-3 text-center" id="footer-status-keseimbangan">
                            Menghitung...
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Educational & Architectural Note Card -->
    <div class="bg-gradient-to-r from-slate-50 to-blue-50/30 p-5 rounded-2xl border border-slate-200 text-xs text-slate-700">
        <div class="flex items-center gap-2 font-bold text-slate-900 mb-2">
            <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
            Penjelasan Teknis &amp; Arsitektur Server-Side (Zero N+1)
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-[11px] leading-relaxed">
            <div>
                <p class="font-bold text-slate-800 mb-1">&bull; DataTables &amp; Summary AJAX Server-Side:</p>
                <p class="text-slate-600">
                    Data jurnal dan kartu ringkasan (KPI) dimuat secara asinkron (AJAX) setelah DOM selesai di-render. Halaman utama terbuka seketika tanpa terbebani kalkulasi berat saat pertama kali diakses.
                </p>
            </div>
            <div>
                <p class="font-bold text-slate-800 mb-1">&bull; Select2 AJAX &amp; Tata Letak Hemat Tempat:</p>
                <p class="text-slate-600">
                    Filter akun CoA menggunakan Select2 AJAX dengan pencarian dinamis (tanpa me-load ribuan master akun di awal). Informasi tanggal, nomor bukti, uraian transaksi, serta pihak terkait digabung menjadi satu kolom yang kompak dan mudah dibaca.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    var table;

    $(document).ready(function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // 1. Inisialisasi Select2 AJAX untuk Filter Akun CoA
        $('#filter-coa').select2({
            placeholder: 'Semua Akun CoA (Ketik untuk mencari...)',
            allowClear: true,
            ajax: {
                url: "{{ route('jurnal.select2.coa') }}",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function(data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: data.pagination
                    };
                },
                cache: true
            }
        });

        // 2. Inisialisasi DataTables Server-Side AJAX
        table = $('#jurnal-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('jurnal.data') }}",
                data: function(d) {
                    d.start_date = $('#filter-start-date').val();
                    d.end_date   = $('#filter-end-date').val();
                    d.jenis      = $('#filter-jenis').val();
                    d.coa_id     = $('#filter-coa').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center text-gray-400 font-medium' },
                { data: 'transaksi_info', name: 'transaksi_info', orderable: false },
                { data: 'akun', name: 'akun', orderable: false },
                { data: 'posisi', name: 'posisi', orderable: false, className: 'text-center' },
                { data: 'debit', name: 'debit', orderable: false, className: 'text-right' },
                { data: 'kredit', name: 'kredit', orderable: false, className: 'text-right' }
            ],
            order: [], // Default urutan dijaga dari Raw Query (tanggal ASC, transaksi_id ASC, urutan_baris ASC)
            drawCallback: function() {
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });

        // 3. Muat KPI Stats & Total Card via AJAX Server-side
        loadStats();
    });

    /**
     * Muat Ringkasan KPI dan Keseimbangan Jurnal via AJAX Server-side
     */
    function loadStats() {
        var params = {
            start_date: $('#filter-start-date').val(),
            end_date:   $('#filter-end-date').val(),
            jenis:      $('#filter-jenis').val(),
            coa_id:     $('#filter-coa').val()
        };

        $.get("{{ route('jurnal.stats') }}", params, function(res) {
            if (res.status === 'success') {
                var d = res.data;

                // Update Card KPI
                $('#stat-total-transaksi').text(d.total_transaksi_formatted);
                $('#stat-total-baris').text(d.total_baris_formatted + ' Baris Entri Jurnal');
                $('#stat-total-debit').text('Rp ' + d.total_debit_formatted);
                $('#stat-total-kredit').text('Rp ' + d.total_kredit_formatted);

                // Update Card Keseimbangan
                if (d.is_balanced) {
                    $('#stat-keseimbangan').html('<i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-600"></i> <span class="text-emerald-700">Seimbang</span>');
                    $('#stat-selisih').text('Selisih: Rp 0 (Balance)').removeClass('text-rose-500').addClass('text-emerald-500');
                    $('#card-keseimbangan').removeClass('border-rose-200').addClass('border-emerald-200');
                    $('#icon-keseimbangan').removeClass('bg-rose-50 text-rose-600').addClass('bg-emerald-50 text-emerald-600');

                    $('#footer-row-status').removeClass('bg-rose-50 text-rose-900').addClass('bg-emerald-50 text-emerald-900');
                    $('#footer-status-keseimbangan').html('<span class="inline-flex items-center gap-1.5 text-emerald-700 font-bold"><i data-lucide="check-circle" class="w-4 h-4"></i> SEIMBANG (DEBIT = KREDIT)</span>');
                } else {
                    $('#stat-keseimbangan').html('<i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i> <span class="text-rose-700">Tidak Seimbang</span>');
                    $('#stat-selisih').text('Selisih: Rp ' + d.selisih_formatted).removeClass('text-emerald-500').addClass('text-rose-500');
                    $('#card-keseimbangan').removeClass('border-emerald-200').addClass('border-rose-200');
                    $('#icon-keseimbangan').removeClass('bg-emerald-50 text-emerald-600').addClass('bg-rose-50 text-rose-600');

                    $('#footer-row-status').removeClass('bg-emerald-50 text-emerald-900').addClass('bg-rose-50 text-rose-900');
                    $('#footer-status-keseimbangan').html('<span class="inline-flex items-center gap-1.5 text-rose-700 font-bold"><i data-lucide="alert-triangle" class="w-4 h-4"></i> SELISIH: Rp ' + d.selisih_formatted + '</span>');
                }

                // Update Footer Totals
                $('#footer-total-debit').text('Rp ' + d.total_debit_formatted);
                $('#footer-total-kredit').text('Rp ' + d.total_kredit_formatted);

                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }
            }
        });
    }

    /**
     * Terapkan Filter
     */
    function applyFilter() {
        table.ajax.reload();
        loadStats();
    }

    /**
     * Reset Seluruh Filter
     */
    function resetFilter() {
        $('#filter-start-date').val('');
        $('#filter-end-date').val('');
        $('#filter-jenis').val('');
        $('#filter-coa').val(null).trigger('change');
        table.ajax.reload();
        loadStats();
    }

    /**
     * Preset Tanggal Cepat
     */
    function setPreset(type) {
        const now = new Date();
        const startInput = document.getElementById('filter-start-date');
        const endInput = document.getElementById('filter-end-date');

        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };

        if (type === 'today') {
            startInput.value = formatDate(now);
            endInput.value = formatDate(now);
        } else if (type === 'this_month') {
            const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
            const endOfMonth = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            startInput.value = formatDate(startOfMonth);
            endInput.value = formatDate(endOfMonth);
        } else if (type === 'this_year') {
            const startOfYear = new Date(now.getFullYear(), 0, 1);
            const endOfYear = new Date(now.getFullYear(), 11, 31);
            startInput.value = formatDate(startOfYear);
            endInput.value = formatDate(endOfYear);
        } else if (type === 'all') {
            startInput.value = '';
            endInput.value = '';
        }

        applyFilter();
    }

    /**
     * Ambil Query String Filter untuk Cetak & Excel
     */
    function getFilterParams() {
        var params = new URLSearchParams();
        var startDate = $('#filter-start-date').val();
        var endDate   = $('#filter-end-date').val();
        var jenis     = $('#filter-jenis').val();
        var coaId     = $('#filter-coa').val();

        if (startDate) params.append('start_date', startDate);
        if (endDate)   params.append('end_date', endDate);
        if (jenis)     params.append('jenis', jenis);
        if (coaId)     params.append('coa_id', coaId);

        return params.toString();
    }

    function cetakHtml() {
        var qs = getFilterParams();
        var url = "{{ route('jurnal.cetak') }}" + (qs ? '?' + qs : '');
        window.open(url, '_blank');
    }

    function exportExcel() {
        var qs = getFilterParams();
        var url = "{{ route('jurnal.export-excel') }}" + (qs ? '?' + qs : '');
        window.location.href = url;
    }
</script>
@endpush
