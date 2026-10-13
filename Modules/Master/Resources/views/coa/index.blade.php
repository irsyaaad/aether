@extends('layouts.app')

@section('title', 'Master Chart of Accounts (CoA) - ' . config('app.name'))
@section('page_title', 'Master Chart of Accounts (CoA)')

@push('styles')
<style>
    #coa-table_wrapper .dataTables_length select {
        padding: 0.25rem 1.75rem 0.25rem 0.5rem;
        font-size: 0.75rem;
        border-radius: 0.375rem;
        border: 1px solid #d1d5db;
    }
    #coa-table_wrapper .dataTables_filter input {
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
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 text-[#1768B5] text-xs font-bold mb-1.5 border border-sky-100">
                <i data-lucide="book-marked" class="w-3.5 h-3.5"></i>
                Master Bagan Akun Terpadu
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Master Chart of Accounts (CoA)</h1>
            <p class="text-xs text-gray-500">Kelola akun <strong>Neraca</strong> (Aset, Kewajiban, Ekuitas) dan <strong>Laba Rugi</strong> (Pendapatan Jasa, Beban Operasional) untuk manajemen bimbingan skripsi.</p>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="openCreateModal()" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                Tambah Akun CoA
            </button>
        </div>
    </div>

    <!-- Quick Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Akun -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs flex items-center justify-between cursor-pointer hover:border-gray-300 transition" onclick="quickFilterLaporan('')">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Akun CoA</p>
                <h3 class="text-2xl font-black text-gray-900 mt-1" id="stat-total">{{ $stats['total'] ?? 0 }}</h3>
                <span class="text-[10px] text-gray-500">Neraca + Laba Rugi</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i data-lucide="layers" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 2: Akun Neraca -->
        <div class="bg-white p-4 rounded-xl border border-indigo-100 shadow-xs flex items-center justify-between cursor-pointer hover:border-indigo-300 transition" onclick="quickFilterLaporan('Neraca')">
            <div>
                <p class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider">Akun Neraca</p>
                <h3 class="text-2xl font-black text-indigo-700 mt-1" id="stat-neraca">{{ $stats['neraca'] ?? 0 }}</h3>
                <span class="text-[10px] text-indigo-500 font-medium">Aset ({{ $stats['aset'] ?? 0 }}), Utang ({{ $stats['kewajiban'] ?? 0 }}), Modal ({{ $stats['ekuitas'] ?? 0 }})</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i data-lucide="scale" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 3: Akun Laba Rugi -->
        <div class="bg-white p-4 rounded-xl border border-teal-100 shadow-xs flex items-center justify-between cursor-pointer hover:border-teal-300 transition" onclick="quickFilterLaporan('Laba Rugi')">
            <div>
                <p class="text-[11px] font-bold text-teal-600 uppercase tracking-wider">Akun Laba Rugi</p>
                <h3 class="text-2xl font-black text-teal-700 mt-1" id="stat-labarugi">{{ $stats['labarugi'] ?? 0 }}</h3>
                <span class="text-[10px] text-teal-600 font-medium">Pendapatan ({{ $stats['pendapatan'] ?? 0 }}), Beban ({{ $stats['beban'] ?? 0 }})</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center">
                <i data-lucide="line-chart" class="h-5 w-5"></i>
            </div>
        </div>

        <!-- Card 4: Akun Aktif -->
        <div class="bg-white p-4 rounded-xl border border-emerald-100 shadow-xs flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider">Akun Aktif</p>
                <h3 class="text-2xl font-black text-emerald-700 mt-1" id="stat-aktif">{{ $stats['aktif'] ?? 0 }}</h3>
                <span class="text-[10px] text-emerald-600">Siap untuk penjurnalan</span>
            </div>
            <div class="h-11 w-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                <i data-lucide="check-circle-2" class="h-5 w-5"></i>
            </div>
        </div>
    </div>

    <!-- Quick Navigation Tab Pills -->
    <div class="flex items-center gap-2 border-b border-gray-200 pb-2">
        <button type="button" id="tab-all" onclick="setTabLaporan('')" class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-[#1768B5] text-white shadow-xs">
            Semua Bagan Akun ({{ $stats['total'] ?? 0 }})
        </button>
        <button type="button" id="tab-neraca" onclick="setTabLaporan('Neraca')" class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-gray-100 text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">
            <i data-lucide="scale" class="w-3.5 h-3.5 inline mr-1"></i> Akun Neraca ({{ $stats['neraca'] ?? 0 }})
        </button>
        <button type="button" id="tab-labarugi" onclick="setTabLaporan('Laba Rugi')" class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition bg-gray-100 text-gray-700 hover:bg-teal-50 hover:text-teal-700">
            <i data-lucide="trending-up" class="w-3.5 h-3.5 inline mr-1"></i> Akun Laba Rugi ({{ $stats['labarugi'] ?? 0 }})
        </button>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-xs">
        <div class="grid grid-cols-1 sm:grid-cols-5 gap-3 items-center">
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Kelompok Laporan</label>
                <select id="filter-laporan" onchange="syncLaporanTab()" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">Semua Laporan</option>
                    <option value="Neraca">Neraca (Balance Sheet)</option>
                    <option value="Laba Rugi">Laba Rugi (P&amp;L)</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Filter Kategori</label>
                <select id="filter-kategori" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">Semua Kategori</option>
                    <optgroup label="Akun Neraca">
                        <option value="ASET LANCAR">ASET LANCAR</option>
                        <option value="ASET TETAP">ASET TETAP</option>
                        <option value="KEWAJIBAN LANCAR">KEWAJIBAN LANCAR</option>
                        <option value="KEWAJIBAN JANGKA PANJANG">KEWAJIBAN JANGKA PANJANG</option>
                        <option value="EKUITAS">EKUITAS</option>
                    </optgroup>
                    <optgroup label="Akun Laba Rugi">
                        <option value="PENDAPATAN JASA">PENDAPATAN JASA</option>
                        <option value="BEBAN OPERASIONAL">BEBAN OPERASIONAL</option>
                    </optgroup>
                    @foreach($kategoriList as $kat)
                        @if(!in_array(strtoupper($kat), ['ASET LANCAR', 'ASET TETAP', 'KEWAJIBAN LANCAR', 'KEWAJIBAN JANGKA PANJANG', 'EKUITAS', 'PENDAPATAN JASA', 'BEBAN OPERASIONAL']))
                            <option value="{{ $kat }}">{{ $kat }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Posisi Normal</label>
                <select id="filter-posisi" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">Semua Posisi</option>
                    <option value="Debit">Debit</option>
                    <option value="Kredit">Kredit</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold uppercase tracking-wider text-gray-500 mb-1">Status Akun</label>
                <select id="filter-status" class="w-full px-3 py-2 text-xs rounded-lg border border-gray-200 bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <option value="">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Nonaktif</option>
                </select>
            </div>
            <div class="flex items-end gap-2 pt-5 sm:pt-0">
                <button type="button" onclick="applyFilter()" class="flex-1 px-3 py-2 bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold rounded-lg transition text-center shadow-xs">
                    Terapkan
                </button>
                <button type="button" onclick="resetFilter()" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Data Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-5">
        <div class="overflow-x-auto">
            <table id="coa-table" class="w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-gray-200 text-xs font-bold text-gray-400 uppercase tracking-wider">
                        <th class="py-3 px-4">No</th>
                        <th class="py-3 px-4">Kode Akun</th>
                        <th class="py-3 px-4">Nama Akun &amp; Keterangan</th>
                        <th class="py-3 px-4">Laporan</th>
                        <th class="py-3 px-4">Kategori CoA</th>
                        <th class="py-3 px-4">Tipe</th>
                        <th class="py-3 px-4">Posisi Normal</th>
                        <th class="py-3 px-4">Saldo Awal</th>
                        <th class="py-3 px-4">Status</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700 text-xs">
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Create / Edit -->
<div id="coa-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-gray-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl border border-gray-200 overflow-hidden transform transition-all">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
            <h3 id="modal-title" class="text-base font-black text-gray-900">Tambah Akun CoA Baru</h3>
            <button type="button" onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="coa-form" onsubmit="saveCoa(event)" class="p-6 space-y-4">
            <input type="hidden" id="coa-id" name="id">

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kode Akun *</label>
                    <input type="text" id="kode_akun" name="kode_akun" required placeholder="Contoh: 1101, 2101, 4101" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kategori CoA *</label>
                    <select id="kategori" name="kategori" required onchange="handleKategoriChange(this.value)" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <optgroup label="Neraca (Balance Sheet)">
                            <option value="ASET LANCAR">ASET LANCAR (11xx)</option>
                            <option value="ASET TETAP">ASET TETAP (12xx)</option>
                            <option value="KEWAJIBAN LANCAR">KEWAJIBAN LANCAR (21xx)</option>
                            <option value="KEWAJIBAN JANGKA PANJANG">KEWAJIBAN JANGKA PANJANG (22xx)</option>
                            <option value="EKUITAS">EKUITAS / MODAL (31xx/32xx)</option>
                        </optgroup>
                        <optgroup label="Laba Rugi (Income Statement)">
                            <option value="PENDAPATAN JASA">PENDAPATAN JASA (41xx)</option>
                            <option value="BEBAN OPERASIONAL">BEBAN OPERASIONAL (51xx)</option>
                        </optgroup>
                        <option value="custom">+ Kategori Lainnya (Custom)</option>
                    </select>
                </div>
            </div>

            <!-- Custom Kategori Input (hidden by default) -->
            <div id="custom-kategori-container" class="hidden">
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nama Kategori Custom *</label>
                <input type="text" id="custom_kategori" placeholder="Ketik nama kategori..." class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Nama Akun *</label>
                <input type="text" id="nama_akun" name="nama_akun" required placeholder="Contoh: Kas Operasional, Piutang Klien..." class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Kelompok Laporan</label>
                    <select id="laporan" name="laporan" required class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="Neraca">Neraca</option>
                        <option value="Laba Rugi">Laba Rugi</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Tipe Akun *</label>
                    <select id="tipe" name="tipe" required class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="Aset">Aset</option>
                        <option value="Kewajiban">Kewajiban</option>
                        <option value="Ekuitas">Ekuitas</option>
                        <option value="Pendapatan">Pendapatan</option>
                        <option value="Beban">Beban</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Posisi Normal *</label>
                    <select id="posisi_normal" name="posisi_normal" required class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                        <option value="Debit">Debit</option>
                        <option value="Kredit">Kredit</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Saldo Awal (Rp)</label>
                <input type="number" id="saldo_awal" name="saldo_awal" value="0" min="0" step="1000" class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-sky-500">
            </div>

            <div>
                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1">Deskripsi / Peruntukan</label>
                <textarea id="deskripsi" name="deskripsi" rows="2" placeholder="Keterangan peruntukan akun atau jenis transaksi..." class="w-full px-3 py-2 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500"></textarea>
            </div>

            <div class="flex items-center gap-2 pt-1">
                <input type="checkbox" id="is_active" name="is_active" value="1" checked class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                <label for="is_active" class="text-xs font-medium text-gray-700 cursor-pointer">Akun Aktif (Dapat digunakan dalam jurnal/transaksi)</label>
            </div>

            <div class="pt-4 border-t border-gray-100 flex justify-end gap-2">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100 transition">
                    Batal
                </button>
                <button type="submit" id="save-btn" class="px-5 py-2 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold transition shadow-xs">
                    Simpan Akun
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let coaTable;

    $(document).ready(function() {
        coaTable = $('#coa-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('master.coa.get-data') }}",
                data: function(d) {
                    d.laporan = $('#filter-laporan').val();
                    d.kategori = $('#filter-kategori').val();
                    d.posisi = $('#filter-posisi').val();
                    d.status = $('#filter-status').val();
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'kode_akun', name: 'kode_akun' },
                { data: 'nama_akun', name: 'nama_akun' },
                { data: 'laporan', name: 'laporan' },
                { data: 'kategori', name: 'kategori' },
                { data: 'tipe', name: 'tipe' },
                { data: 'posisi_normal', name: 'posisi_normal' },
                { data: 'saldo_awal', name: 'saldo_awal' },
                { data: 'is_active', name: 'is_active' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-right' }
            ],
            drawCallback: function() {
                if (window.lucide) {
                    lucide.createIcons();
                }
            }
        });
    });

    function setTabLaporan(val) {
        $('#filter-laporan').val(val);
        updateTabStyles(val);
        coaTable.ajax.reload();
    }

    function quickFilterLaporan(val) {
        setTabLaporan(val);
    }

    function syncLaporanTab() {
        const val = $('#filter-laporan').val();
        updateTabStyles(val);
        coaTable.ajax.reload();
    }

    function updateTabStyles(val) {
        $('.tab-btn').removeClass('bg-[#1768B5] text-white shadow-xs').addClass('bg-gray-100 text-gray-700');
        if (val === 'Neraca') {
            $('#tab-neraca').removeClass('bg-gray-100 text-gray-700').addClass('bg-[#1768B5] text-white shadow-xs');
        } else if (val === 'Laba Rugi') {
            $('#tab-labarugi').removeClass('bg-gray-100 text-gray-700').addClass('bg-[#1768B5] text-white shadow-xs');
        } else {
            $('#tab-all').removeClass('bg-gray-100 text-gray-700').addClass('bg-[#1768B5] text-white shadow-xs');
        }
    }

    function applyFilter() {
        syncLaporanTab();
    }

    function resetFilter() {
        $('#filter-laporan').val('');
        $('#filter-kategori').val('');
        $('#filter-posisi').val('');
        $('#filter-status').val('');
        updateTabStyles('');
        coaTable.ajax.reload();
    }

    function handleKategoriChange(val) {
        if (val === 'custom') {
            $('#custom-kategori-container').removeClass('hidden');
            $('#custom_kategori').prop('required', true);
        } else {
            $('#custom-kategori-container').addClass('hidden');
            $('#custom_kategori').prop('required', false);
            
            // Smart auto-fill logic for standard accounting categories
            if (val === 'ASET LANCAR' || val === 'ASET TETAP') {
                $('#laporan').val('Neraca');
                $('#tipe').val('Aset');
                $('#posisi_normal').val('Debit');
            } else if (val === 'KEWAJIBAN LANCAR' || val === 'KEWAJIBAN JANGKA PANJANG') {
                $('#laporan').val('Neraca');
                $('#tipe').val('Kewajiban');
                $('#posisi_normal').val('Kredit');
            } else if (val === 'EKUITAS') {
                $('#laporan').val('Neraca');
                $('#tipe').val('Ekuitas');
                $('#posisi_normal').val('Kredit');
            } else if (val === 'PENDAPATAN JASA') {
                $('#laporan').val('Laba Rugi');
                $('#tipe').val('Pendapatan');
                $('#posisi_normal').val('Kredit');
            } else if (val === 'BEBAN OPERASIONAL') {
                $('#laporan').val('Laba Rugi');
                $('#tipe').val('Beban');
                $('#posisi_normal').val('Debit');
            }
        }
    }

    function openCreateModal() {
        $('#coa-form')[0].reset();
        $('#coa-id').val('');
        $('#modal-title').text('Tambah Akun CoA Baru');
        $('#kategori').val('ASET LANCAR');
        $('#laporan').val('Neraca');
        $('#tipe').val('Aset');
        $('#posisi_normal').val('Debit');
        $('#saldo_awal').val('0');
        $('#custom-kategori-container').addClass('hidden');
        $('#is_active').prop('checked', true);
        $('#coa-modal').removeClass('hidden');
        if (window.lucide) lucide.createIcons();
    }

    function closeModal() {
        $('#coa-modal').addClass('hidden');
    }

    function editCoa(id) {
        $.get("{{ url('master/coa') }}/" + id, function(data) {
            $('#coa-id').val(data.id);
            $('#kode_akun').val(data.kode_akun);
            $('#nama_akun').val(data.nama_akun);
            
            const standardKategori = [
                'ASET LANCAR', 'ASET TETAP', 
                'KEWAJIBAN LANCAR', 'KEWAJIBAN JANGKA PANJANG', 
                'EKUITAS', 'PENDAPATAN JASA', 'BEBAN OPERASIONAL'
            ];
            if (standardKategori.includes(data.kategori)) {
                $('#kategori').val(data.kategori);
                $('#custom-kategori-container').addClass('hidden');
            } else {
                $('#kategori').val('custom');
                $('#custom_kategori').val(data.kategori);
                $('#custom-kategori-container').removeClass('hidden');
            }

            $('#laporan').val(data.laporan || (['Pendapatan', 'Beban'].includes(data.tipe) ? 'Laba Rugi' : 'Neraca'));
            $('#tipe').val(data.tipe);
            $('#posisi_normal').val(data.posisi_normal);
            $('#saldo_awal').val(Math.round(data.saldo_awal || 0));
            $('#deskripsi').val(data.deskripsi);
            $('#is_active').prop('checked', data.is_active == 1);

            $('#modal-title').text('Edit Akun CoA: ' + data.nama_akun);
            $('#coa-modal').removeClass('hidden');
            if (window.lucide) lucide.createIcons();
        }).fail(function() {
            alert('Gagal mengambil data akun CoA.');
        });
    }

    function saveCoa(e) {
        e.preventDefault();
        const id = $('#coa-id').val();
        const url = id ? "{{ url('master/coa') }}/" + id : "{{ route('master.coa.store') }}";
        const method = id ? 'PUT' : 'POST';

        let kategoriVal = $('#kategori').val();
        if (kategoriVal === 'custom') {
            kategoriVal = $('#custom_kategori').val().trim();
            if (!kategoriVal) {
                alert('Nama kategori custom wajib diisi.');
                return;
            }
        }

        const payload = {
            id: id,
            kode_akun: $('#kode_akun').val().trim(),
            nama_akun: $('#nama_akun').val().trim(),
            kategori: kategoriVal,
            laporan: $('#laporan').val(),
            tipe: $('#tipe').val(),
            posisi_normal: $('#posisi_normal').val(),
            saldo_awal: $('#saldo_awal').val() || 0,
            deskripsi: $('#deskripsi').val(),
            is_active: $('#is_active').is(':checked') ? 1 : 0
        };

        $.ajax({
            url: url,
            type: method,
            data: payload,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                closeModal();
                coaTable.ajax.reload();
                alert(res.message);
            },
            error: function(xhr) {
                const errors = xhr.responseJSON ? xhr.responseJSON.errors : null;
                if (errors) {
                    let msg = '';
                    for (let k in errors) msg += errors[k].join('\n') + '\n';
                    alert(msg);
                } else {
                    alert('Gagal menyimpan akun CoA: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan sistem.'));
                }
            }
        });
    }

    function deleteCoa(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus akun CoA ini?')) return;

        $.ajax({
            url: "{{ url('master/coa') }}/" + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                coaTable.ajax.reload();
                alert(res.message);
            },
            error: function(xhr) {
                alert('Gagal menghapus akun CoA: ' + (xhr.responseJSON?.message || 'Terjadi kesalahan sistem.'));
            }
        });
    }
</script>
@endpush
