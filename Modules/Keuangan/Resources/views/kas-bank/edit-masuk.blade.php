@extends('layouts.app')

@section('title', 'Edit Transaksi Kas/Bank Masuk - ' . config('app.name'))
@section('page_title', 'Edit Kas / Bank Masuk')

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
        border-color: #059669 !important;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.15) !important;
    }
    .select2-results__option {
        padding: 10px 14px !important;
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

@section('toolbar_actions')
<a href="{{ route('kas-bank-masuk.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl hover:bg-slate-50 transition shadow-2xs">
    <i data-lucide="arrow-left" class="w-4 h-4 text-slate-500"></i>
    Kembali ke Daftar
</a>
@endsection

@section('content')
<div class="w-full space-y-6">

    <!-- Header Card -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="p-6 bg-gradient-to-r from-emerald-700 via-teal-700 to-emerald-800 text-white flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="h-12 w-12 rounded-2xl bg-white/15 backdrop-blur-md border border-white/20 flex items-center justify-center shadow-xs">
                    <i data-lucide="edit-3" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h1 class="text-lg sm:text-xl font-black text-white tracking-tight">Edit Transaksi Kas & Bank Masuk</h1>
                    <p class="text-xs text-emerald-100 mt-0.5">Perbarui rincian penerimaan kas / rekening bank {{ $transaksi->nomor_transaksi }}</p>
                </div>
            </div>
            <a href="{{ route('kas-bank.voucher', $transaksi->id) }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 text-white text-xs font-semibold backdrop-blur-md transition">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Cetak Voucher
            </a>
        </div>

        <!-- Penjurnalan Visual Banner -->
        <div class="bg-gradient-to-r from-emerald-50/80 via-teal-50/50 to-slate-50 p-4 border-b border-emerald-100/60">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-emerald-200/60 shadow-2xs">
                    <span class="px-2.5 py-1 rounded-md bg-emerald-600 text-white text-[10px] font-bold tracking-wide">DEBET</span>
                    <div>
                        <span class="font-bold text-emerald-950 block">Kas / Bank Penerima</span>
                        <span class="text-[11px] text-slate-500">Saldo akun aset kas/bank bertambah</span>
                    </div>
                </div>
                <div class="flex items-center gap-3 bg-white p-3 rounded-xl border border-slate-200/80 shadow-2xs">
                    <span class="px-2.5 py-1 rounded-md bg-slate-700 text-white text-[10px] font-bold tracking-wide">KREDIT</span>
                    <div>
                        <span class="font-bold text-slate-900 block">Akun Lawan (CoA)</span>
                        <span class="text-[11px] text-slate-500">Pendapatan Jasa / Pengembalian Beban</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Body -->
        <form id="form-edit-kas-masuk" action="{{ route('kas-bank-masuk.update', $transaksi->id) }}" method="POST" enctype="multipart/form-data" class="p-6 sm:p-8 space-y-6">
            @csrf
            @method('PUT')

            <!-- Baris 1: No Transaksi & Tanggal -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2 flex items-center justify-between">
                        <span>Nomor Transaksi</span>
                        <span class="text-[11px] font-normal text-slate-400">Kode Unik</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="hash" class="w-4 h-4"></i>
                        </span>
                        <input type="text" id="nomor_transaksi" name="nomor_transaksi" value="{{ $transaksi->nomor_transaksi }}" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-mono font-bold bg-slate-50 text-slate-700 tracking-wider focus:outline-none" readonly>
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
                        <input type="date" id="tanggal" name="tanggal" value="{{ \Carbon\Carbon::parse($transaksi->tanggal)->format('Y-m-d') }}" required class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none transition">
                    </div>
                </div>
            </div>

            <!-- Baris 2: Akun Kas & Akun Lawan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Akun Kas / Bank (Debit) -->
                <div>
                    <label class="block text-xs font-bold text-emerald-900 mb-2 flex items-center gap-1.5">
                        <i data-lucide="wallet" class="w-4 h-4 text-emerald-600"></i>
                        Akun Kas / Bank (Debit) <span class="text-rose-500">*</span>
                    </label>
                    <select id="kas_bank_id" name="kas_bank_id" required class="w-full">
                        @if($transaksi->kasBank)
                            <option value="{{ $transaksi->kasBank->id }}" selected>{{ $transaksi->kasBank->kode_akun }} - {{ $transaksi->kasBank->nama_akun }}</option>
                        @endif
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-emerald-600 shrink-0"></i>
                        Pilih kas tunai atau rekening bank yang menerima dana
                    </p>
                </div>

                <!-- Akun Lawan (Kredit) -->
                <div>
                    <label class="block text-xs font-bold text-slate-800 mb-2 flex items-center gap-1.5">
                        <i data-lucide="layers" class="w-4 h-4 text-slate-600"></i>
                        Akun Lawan (Kredit) <span class="text-rose-500">*</span>
                    </label>
                    <select id="lawan_coa_id" name="lawan_coa_id" required class="w-full">
                        @if($transaksi->lawanCoa)
                            <option value="{{ $transaksi->lawanCoa->id }}" selected>{{ $transaksi->lawanCoa->kode_akun }} - {{ $transaksi->lawanCoa->nama_akun }}</option>
                        @endif
                    </select>
                    <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1">
                        <i data-lucide="info" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                        Pilih sumber penerimaan (Pendapatan Jasa / Refund Beban)
                    </p>
                </div>
            </div>

            <!-- Baris 3: Nominal & Metode Pembayaran -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Nominal -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">
                        Nominal Transaksi (Rp) <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-sm font-black text-emerald-700">Rp</span>
                        <input type="text" id="nominal" name="nominal" value="{{ number_format($transaksi->nominal, 0, ',', '.') }}" required class="w-full pl-12 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-bold text-emerald-700 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none transition" placeholder="0" onkeyup="formatRupiahInput(this)">
                    </div>
                    <div id="terbilang-preview" class="mt-2 p-2.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800 italic">
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
                        <select id="metode_pembayaran" name="metode_pembayaran" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none transition">
                            <option value="Transfer Bank" {{ $transaksi->metode_pembayaran === 'Transfer Bank' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="Tunai" {{ $transaksi->metode_pembayaran === 'Tunai' ? 'selected' : '' }}>Tunai / Cash</option>
                            <option value="QRIS / Digital" {{ $transaksi->metode_pembayaran === 'QRIS / Digital' ? 'selected' : '' }}>QRIS / Digital</option>
                            <option value="Giro / Cek" {{ $transaksi->metode_pembayaran === 'Giro / Cek' ? 'selected' : '' }}>Giro / Cek</option>
                            <option value="Lainnya" {{ $transaksi->metode_pembayaran === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Baris 4: Diterima Dari & No Referensi -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Diterima Dari (Penyetor / Klien)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </span>
                        <input type="text" id="penerima_pembayar" name="penerima_pembayar" value="{{ $transaksi->penerima_pembayar }}" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none transition" placeholder="Contoh: Mahasiswa / Vendor / PT Mitra">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-2">Nomor Referensi / Bukti Transfer</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="file-check" class="w-4 h-4"></i>
                        </span>
                        <input type="text" id="nomor_referensi" name="nomor_referensi" value="{{ $transaksi->nomor_referensi }}" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none transition" placeholder="No. Kuitansi / Ref Transfer / Slip Bank">
                    </div>
                </div>
            </div>

            <!-- Baris 5: Keterangan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Keterangan / Uraian Transaksi</label>
                <textarea id="keterangan" name="keterangan" rows="3" class="w-full px-4 py-3 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 outline-none transition" placeholder="Tuliskan catatan atau rincian penerimaan kas/bank...">{{ $transaksi->keterangan }}</textarea>
            </div>

            <!-- Baris 6: Upload Bukti Transaksi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-2">Upload Bukti Transaksi Baru (Opsional)</label>
                <div class="p-4 border-2 border-dashed border-slate-200 rounded-2xl hover:border-emerald-400 transition bg-slate-50/50">
                    <input type="file" id="lampiran" name="lampiran" accept="image/*,application/pdf" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition">
                    <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1.5">
                        <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                        Mendukung format JPG, PNG, atau PDF (Ukuran maksimal 5MB)
                    </p>
                    @if($transaksi->lampiran)
                        <div class="mt-3 pt-3 border-t border-slate-200/80 flex items-center gap-2">
                            <span class="text-xs text-slate-500 font-medium">Lampiran saat ini:</span>
                            <a href="{{ asset('storage/' . $transaksi->lampiran) }}" target="_blank" class="inline-flex items-center gap-1 text-xs text-emerald-700 hover:text-emerald-800 font-bold underline">
                                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                Buka File Lampiran Tersimpan
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('kas-bank-masuk.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" id="btn-submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 shadow-sm transition">
                    <i data-lucide="check-circle" class="w-4 h-4"></i>
                    Perbarui Transaksi Kas Masuk
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
        initSelect2KasMasuk();

        var initialNominal = parseInt("{{ $transaksi->nominal }}", 10);
        if (initialNominal > 0) {
            updateTerbilangPreview(initialNominal);
        }

        $('#form-edit-kas-masuk').on('submit', function(e) {
            e.preventDefault();

            var form = this;
            var formData = new FormData(form);

            $('#btn-submit').prop('disabled', true).html('<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Memperbarui...');
            lucide.createIcons();

            $.ajax({
                url: "{{ route('kas-bank-masuk.update', $transaksi->id) }}",
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
                            window.location.href = response.redirect_url || "{{ route('kas-bank-masuk.index') }}";
                        });
                    }
                },
                error: function(xhr) {
                    $('#btn-submit').prop('disabled', false).html('<i data-lucide="check-circle" class="w-4 h-4"></i> Perbarui Transaksi Kas Masuk');
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

    function initSelect2KasMasuk() {
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
            placeholder: '-- Cari Akun Lawan (Beban / Pendapatan) --',
            allowClear: true,
            width: '100%',
            ajax: {
                url: "{{ route('kas-bank.select2.lawan') }}",
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term || '',
                        jenis: 'masuk',
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
