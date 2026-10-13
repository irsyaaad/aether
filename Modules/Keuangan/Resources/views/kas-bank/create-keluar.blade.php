@extends('layouts.app')

@section('title', 'Catat Transaksi Kas/Bank Keluar - ' . config('app.name'))
@section('page_title', 'Catat Kas / Bank Keluar')

@push('styles')
<style>
    /* Select2 Custom Modern Theme */
    .select2-container {
        width: 100% !important;
    }
    .select2-container--default .select2-selection--single {
        height: 44px !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.75rem !important;
        padding: 8px 14px !important;
        background-color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        transition: all 0.2s ease-in-out;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #e11d48 !important;
        box-shadow: 0 0 0 3px rgba(225, 29, 72, 0.18) !important;
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
        height: 42px !important;
        right: 12px !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__clear {
        color: #94a3b8 !important;
        font-size: 1.125rem !important;
        margin-right: 24px !important;
        line-height: 40px !important;
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
        padding: 10px 12px !important;
        background-color: #f8fafc !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #cbd5e1 !important;
        border-radius: 0.625rem !important;
        padding: 8px 12px !important;
        font-size: 0.8125rem !important;
        outline: none !important;
        background-color: #ffffff !important;
    }
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #e11d48 !important;
        box-shadow: 0 0 0 2px rgba(225, 29, 72, 0.15) !important;
    }
    .select2-results__option {
        padding: 10px 14px !important;
        font-size: 0.8125rem !important;
        border-bottom: 1px solid #f8fafc;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #fff1f2 !important;
        color: #9f1239 !important;
    }
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #ffe4e6 !important;
        color: #9f1239 !important;
        font-weight: 600 !important;
    }
</style>
@endpush

@section('toolbar_actions')
<a href="{{ route('kas-bank-keluar.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition shadow-2xs">
    <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500"></i>
    Kembali ke Daftar
</a>
@endsection

@section('content')
<div class="w-full space-y-6">

    <!-- Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-rose-600 via-red-600 to-rose-700 text-white flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center shadow-xs">
                    <i data-lucide="arrow-up-right" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-black text-white tracking-tight">Catat Transaksi Kas & Bank Keluar</h1>
                    <p class="text-xs text-rose-100 mt-0.5">Formulir pengeluaran kas / transfer rekening bank untuk beban operasional</p>
                </div>
            </div>
            <a href="{{ route('kas-bank-keluar.index') }}" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-semibold backdrop-blur-md transition">
                <i data-lucide="list" class="w-4 h-4"></i>
                Lihat Daftar Transaksi
            </a>
        </div>

        <!-- Penjurnalan Visual Banner -->
        <div class="bg-gradient-to-r from-rose-50/80 via-amber-50/50 to-slate-50 p-4 border-b border-rose-100/60">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-rose-200/60 shadow-2xs">
                    <span class="px-2.5 py-1 rounded-md bg-rose-600 text-white text-[10px] font-bold tracking-wide">DEBET</span>
                    <div>
                        <span class="font-bold text-rose-950 block">Akun Lawan / Beban (CoA)</span>
                        <span class="text-[11px] text-slate-500">Alokasi beban operasional / pembayaran</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-slate-200/80 shadow-2xs">
                    <span class="px-2.5 py-1 rounded-md bg-slate-700 text-white text-[10px] font-bold tracking-wide">KREDIT</span>
                    <div>
                        <span class="font-bold text-slate-900 block">Kas / Bank Sumber Dana</span>
                        <span class="text-[11px] text-slate-500">Saldo aset kas / rekening bank berkurang</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Body -->
        <form id="form-kas-keluar" action="{{ route('kas-bank-keluar.store') }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf

            <!-- Baris 1: No Transaksi & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center justify-between">
                        <span>Nomor Transaksi</span>
                        <span class="text-[11px] font-normal text-slate-400">Auto-generated</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="hash" class="w-4 h-4"></i>
                        </span>
                        <input type="text" id="nomor_transaksi" name="nomor_transaksi" value="{{ $defaultNoTransaksi }}" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold bg-slate-50 text-slate-700 tracking-wider focus:outline-none" readonly>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Tanggal Transaksi <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="calendar" class="w-4 h-4"></i>
                        </span>
                        <input type="date" id="tanggal" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600 outline-none transition">
                    </div>
                </div>
            </div>

            <!-- Baris 2: Akun Kas & Akun Lawan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Akun Kas / Bank (Kredit) -->
                <div>
                    <label class="block text-xs font-bold text-slate-900 mb-2 flex items-center gap-1.5">
                        <i data-lucide="wallet" class="w-4 h-4 text-slate-600"></i>
                        Akun Kas / Bank Sumber (Kredit) <span class="text-rose-500">*</span>
                    </label>
                    <select id="kas_bank_id" name="kas_bank_id" required class="w-full">
                        <option value="">-- Cari & Pilih Kas / Rekening Sumber --</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                        Rekening atau kas tunai yang dananya dikeluarkan
                    </p>
                </div>

                <!-- Akun Lawan Beban (Debit) -->
                <div>
                    <label class="block text-xs font-bold text-rose-900 mb-2 flex items-center gap-1.5">
                        <i data-lucide="layers" class="w-4 h-4 text-rose-600"></i>
                        Akun Beban / Keperluan (Debit) <span class="text-rose-500">*</span>
                    </label>
                    <select id="lawan_coa_id" name="lawan_coa_id" required class="w-full">
                        <option value="">-- Cari Akun Beban (Operasional/dll) --</option>
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-rose-600 shrink-0"></i>
                        Pos akun beban yang dibiayai transaksi ini
                    </p>
                </div>
            </div>

            <!-- Baris 3: Nominal & Metode Pembayaran -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Nominal -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Nominal Pengeluaran (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-sm font-black text-rose-700">Rp</span>
                        <input type="text" id="nominal" name="nominal" required class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-bold text-rose-700 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600 outline-none transition" placeholder="0" onkeyup="formatRupiahInput(this)">
                    </div>
                    <div id="terbilang-preview" class="hidden mt-2 p-2.5 rounded-xl bg-rose-50 border border-rose-200 text-xs font-semibold text-rose-800 italic">
                        -
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Metode Pembayaran</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="credit-card" class="w-4 h-4"></i>
                        </span>
                        <select id="metode_pembayaran" name="metode_pembayaran" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600 outline-none transition">
                            <option value="Transfer Bank">Transfer Bank</option>
                            <option value="Tunai">Tunai / Cash</option>
                            <option value="QRIS / Digital">QRIS / Digital</option>
                            <option value="Giro / Cek">Giro / Cek</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Baris 4: Dibayarkan Kepada & No Referensi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Dibayarkan Kepada (Penerima Dana)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user-check" class="w-4 h-4"></i>
                        </span>
                        <input type="text" id="penerima_pembayar" name="penerima_pembayar" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600 outline-none transition" placeholder="Nama vendor / karyawan / pihak penerima">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Nomor Referensi / No. Nota / No. Invoice</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="file-check" class="w-4 h-4"></i>
                        </span>
                        <input type="text" id="nomor_referensi" name="nomor_referensi" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600 outline-none transition" placeholder="No. Invoice / Nota / Slip Pengeluaran">
                    </div>
                </div>
            </div>

            <!-- Baris 5: Keterangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Keterangan / Keperluan Pengeluaran</label>
                <textarea id="keterangan" name="keterangan" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-600 outline-none transition" placeholder="Uraian pengeluaran beban kas/bank..."></textarea>
            </div>

            <!-- Baris 6: Upload Bukti Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Upload Bukti Transaksi (Struk / Nota / PDF)</label>
                <div class="p-4 border-2 border-dashed border-slate-200 rounded-2xl hover:border-rose-400 transition bg-slate-50/50">
                    <input type="file" id="lampiran" name="lampiran" accept="image/*,application/pdf" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 transition">
                    <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5">
                        <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                        Mendukung format JPG, PNG, atau PDF (Ukuran maksimal 5MB)
                    </p>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('kas-bank-keluar.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" id="btn-submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-rose-600 to-red-700 hover:from-rose-700 hover:to-red-800 shadow-sm transition">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Simpan Transaksi Kas Keluar
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        lucide.createIcons();
        initSelect2KasKeluar();

        $('#tanggal').on('change', function() {
            var selectedDate = $(this).val();
            $.get("{{ route('kas-bank.next-nomor') }}", { jenis: 'keluar', tanggal: selectedDate }, function(res) {
                if (res.status === 'success') {
                    $('#nomor_transaksi').val(res.nomor_transaksi);
                }
            });
        });

        $('#form-kas-keluar').on('submit', function(e) {
            e.preventDefault();

            var form = this;
            var formData = new FormData(form);

            $('#btn-submit').prop('disabled', true).html('<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Menyimpan...');
            lucide.createIcons();

            $.ajax({
                url: "{{ route('kas-bank-keluar.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(function() {
                            window.location.href = response.redirect_url || "{{ route('kas-bank-keluar.index') }}";
                        });
                    }
                },
                error: function(xhr) {
                    $('#btn-submit').prop('disabled', false).html('<i data-lucide="check-circle" class="w-4 h-4"></i> Simpan Transaksi Kas Keluar');
                    lucide.createIcons();
                    var msg = 'Terjadi kesalahan saat menyimpan data.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        msg = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal Menyimpan',
                        text: msg
                    });
                }
            });
        });
    });

    function initSelect2KasKeluar() {
        $('#kas_bank_id').select2({
            placeholder: '-- Cari & Pilih Akun Kas / Rekening Bank --',
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ route('kas-bank.select2.kas-bank') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || '',
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                },
                cache: true
            },
            templateResult: formatCoaOption,
            templateSelection: formatCoaSelection
        });

        $('#lawan_coa_id').select2({
            placeholder: '-- Cari Akun Beban (Operasional/dll) --',
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ route('kas-bank.select2.lawan') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || '',
                        jenis: 'keluar',
                        page: params.page || 1
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data.results,
                        pagination: {
                            more: data.pagination ? data.pagination.more : false
                        }
                    };
                },
                cache: true
            },
            templateResult: formatCoaOption,
            templateSelection: formatCoaSelection
        });
    }

    function formatCoaOption(item) {
        if (!item.id) return item.text;
        if (item.kode_akun && item.nama_akun) {
            return item.kode_akun + ' - ' + item.nama_akun;
        }
        return item.text || item.nama_akun;
    }

    function formatCoaSelection(item) {
        if (!item.id) return item.text;
        if (item.kode_akun && item.nama_akun) {
            return item.kode_akun + ' - ' + item.nama_akun;
        }
        return item.text || item.nama_akun;
    }

    function formatRupiahInput(input) {
        var raw = input.value.replace(/[^0-9]/g, '');
        if (raw) {
            input.value = new Intl.NumberFormat('id-ID').format(raw);
            updateTerbilangPreview(parseInt(raw, 10));
        } else {
            input.value = '';
            $('#terbilang-preview').text('-').addClass('hidden');
        }
    }

    function updateTerbilangPreview(nominal) {
        if (!nominal || isNaN(nominal) || nominal <= 0) {
            $('#terbilang-preview').text('-').addClass('hidden');
            return;
        }
        var words = 'Terbilang: ' + angkaKeTerbilang(nominal) + ' Rupiah';
        $('#terbilang-preview').text(words).removeClass('hidden');
    }

    function angkaKeTerbilang(n) {
        var angka = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        n = Math.floor(n);
        if (n < 12) {
            return angka[n];
        } else if (n < 20) {
            return angkaKeTerbilang(n - 10) + " Belas";
        } else if (n < 100) {
            return angkaKeTerbilang(Math.floor(n / 10)) + " Puluh " + angkaKeTerbilang(n % 10);
        } else if (n < 200) {
            return "Seratus " + angkaKeTerbilang(n - 100);
        } else if (n < 1000) {
            return angkaKeTerbilang(Math.floor(n / 100)) + " Ratus " + angkaKeTerbilang(n % 100);
        } else if (n < 2000) {
            return "Seribu " + angkaKeTerbilang(n - 1000);
        } else if (n < 1000000) {
            return angkaKeTerbilang(Math.floor(n / 1000)) + " Ribu " + angkaKeTerbilang(n % 1000);
        } else if (n < 1000000000) {
            return angkaKeTerbilang(Math.floor(n / 1000000)) + " Juta " + angkaKeTerbilang(n % 1000000);
        } else if (n < 1000000000000) {
            return angkaKeTerbilang(Math.floor(n / 1000000000)) + " Miliar " + angkaKeTerbilang(n % 1000000000);
        } else {
            return angkaKeTerbilang(Math.floor(n / 1000000000000)) + " Triliun " + angkaKeTerbilang(n % 1000000000000);
        }
    }
</script>
@endpush
