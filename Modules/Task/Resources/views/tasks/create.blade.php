@extends('layouts.app')

@section('title', 'Buat Tugas Baru - ' . config('app.name'))
@section('page_title', 'Buat Tugas Baru')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border-color: #d1d5db !important;
        border-radius: 0.5rem !important;
        padding: 0.25rem 0.4rem !important;
        min-height: 38px !important;
        font-size: 0.75rem !important;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #e11d48 !important;
        outline: none !important;
        box-shadow: 0 0 0 1px #e11d48 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #fee2e2 !important;
        border: 1px solid #fecaca !important;
        border-radius: 0.375rem !important;
        padding: 2px 8px !important;
        font-size: 0.75rem !important;
        color: #991b1b !important;
        font-weight: 500 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #b91c1c !important;
        margin-right: 4px !important;
    }
    .select2-container--default .select2-search--inline .select2-search__field {
        font-size: 0.75rem !important;
        margin-top: 4px !important;
    }
    .select2-dropdown {
        border-color: #e5e7eb !important;
        border-radius: 0.5rem !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1) !important;
        font-size: 0.75rem !important;
        z-index: 9999 !important;
    }
    .select2-results__option--highlighted[aria-selected] {
        background-color: #e11d48 !important;
        color: #ffffff !important;
    }
</style>
@endpush

@section('toolbar_actions')
<a href="{{ route('tasks.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors shadow-sm">
    <i data-lucide="arrow-left" class="w-4 h-4 text-gray-500"></i>
    Kembali ke Daftar
</a>
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('tasks.store') }}" method="POST" id="task-form">
        @csrf

        <div class="space-y-6">
            <!-- Main Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-5">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-800 pb-2 border-b border-gray-100 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4 text-primary"></i>
                    Informasi Tugas
                </h3>

                <!-- Company Selector (if super admin) -->
                @if(empty($userCompanyId))
                <div>
                    <label for="company_id" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Perusahaan <span class="text-red-500">*</span>
                    </label>
                    <select name="company_id" id="company_id" required class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary @error('company_id') border-red-500 @enderror">
                        <option value="">-- Pilih Perusahaan --</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ (string)old('company_id', $defaultCompanyId) === (string)$company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                @else
                <input type="hidden" name="company_id" id="company_id" value="{{ $userCompanyId }}">
                @endif

                <!-- Title -->
                <div>
                    <label for="title" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Judul Tugas <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="title" required value="{{ old('title') }}" placeholder="Contoh: Rekapitulasi Laporan Kinerja Tugas Pegawai Mingguan" class="w-full text-xs py-2.5 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary @error('title') border-red-500 @enderror">
                    @error('title')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Deskripsi / Rincian Instruksi
                    </label>
                    <textarea name="description" id="description" rows="4" placeholder="Tuliskan instruksi detail mengenai tugas ini..." class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary @error('description') border-red-500 @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Category -->
                    <div>
                        <label for="category_id" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Kategori Tugas
                        </label>
                        <select name="category_id" id="category_id" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                            <option value="">-- Tanpa Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }} ({{ $cat->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priority -->
                    <div>
                        <label for="priority_id" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Tingkat Prioritas <span class="text-red-500">*</span>
                        </label>
                        <select name="priority_id" id="priority_id" required class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary @error('priority_id') border-red-500 @enderror">
                            <option value="">-- Pilih Prioritas --</option>
                            @foreach($priorities as $p)
                                <option value="{{ $p->id }}" {{ old('priority_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->name }} (Level {{ $p->level }})
                                </option>
                            @endforeach
                        </select>
                        @error('priority_id')
                            <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Task Type (Harian, Bulanan, Insidentil) -->
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Sifat / Tipe Tugas <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <label class="flex items-start gap-2.5 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="task_type" value="harian" {{ old('task_type') === 'harian' ? 'checked' : '' }} class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900 flex items-center gap-1">
                                        <i data-lucide="repeat" class="w-3.5 h-3.5 text-cyan-600"></i> Harian
                                    </span>
                                    <span class="text-[10px] text-gray-500 leading-tight block mt-0.5">Tiap hari otomatis muncul ke karyawan terploting</span>
                                </div>
                            </label>
                            <label class="flex items-start gap-2.5 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="task_type" value="bulanan" {{ old('task_type') === 'bulanan' ? 'checked' : '' }} class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900 flex items-center gap-1">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-purple-600"></i> Bulanan
                                    </span>
                                    <span class="text-[10px] text-gray-500 leading-tight block mt-0.5">Tiap bulan otomatis tergenerate ke karyawan</span>
                                </div>
                            </label>
                            <label class="flex items-start gap-2.5 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="task_type" value="insidentil" {{ old('task_type', 'insidentil') === 'insidentil' ? 'checked' : '' }} class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900 flex items-center gap-1">
                                        <i data-lucide="zap" class="w-3.5 h-3.5 text-amber-600"></i> Insidentil
                                    </span>
                                    <span class="text-[10px] text-gray-500 leading-tight block mt-0.5">Tugas satu kali jalan / non-recurring</span>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Preset Template Tugas Perusahaan -->
                    <div class="sm:col-span-2 p-3.5 bg-gradient-to-r from-blue-50/80 via-indigo-50/70 to-purple-50/80 rounded-xl border border-blue-200/90 shadow-xs mb-1">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-xs font-bold text-blue-950 flex items-center gap-1.5">
                                    <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                                    Template Cepat Tugas Operasional Perusahaan
                                </span>
                                <p class="text-[11px] text-gray-600 mt-0.5">Pilih jenis tugas operasional untuk mengisi otomatis tipe pengerjaan dan kolom data yang dibutuhkan:</p>
                            </div>
                            <select id="company-preset-select" onchange="applyCompanyPreset(this.value)" class="sm:w-72 text-xs py-2 px-3 border border-blue-300 rounded-lg bg-white text-gray-800 font-semibold focus:ring-1 focus:ring-primary focus:border-primary shadow-xs">
                                <option value="">-- Pilih Template Tugas --</option>
                                <option value="stock_update">1. Update Stock Kosong (Pilih Produk & Toko)</option>
                                <option value="pesanan_batal">2. Pesanan Batal</option>
                                <option value="pesanan_terkendala">3. Pesanan Terkendala</option>
                                <option value="customer_baru">4. Kenaikan Customer Baru</option>
                                <option value="daftar_pesanan">5. Daftar Pesanan Perhari</option>
                                <option value="chat_customer">6. Chat Customer Per Hari</option>
                                <option value="posting_facebook">7. Posting di Facebook</option>
                                <option value="scraping_wa">8. Scraping WA</option>
                                <option value="artikel_website">9. Artikel Website</option>
                                <option value="posting_wa_ig">10. Posting WA & IG</option>
                                <option value="flash_sale">11. Pengaturan Flash Sale</option>
                                <option value="campaign">12. Daftar Campaign yang Diikuti</option>
                                <option value="voucher">13. Pengaturan Voucher</option>
                                <option value="broadcast">14. Pengaturan Broadcast</option>
                            </select>
                        </div>
                    </div>

                    <!-- Output / Execution Type (Checklist, Target Angka, List Jawaban, Update Stock) -->
                    <div class="sm:col-span-2 pt-3 border-t border-gray-100">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Bentuk Pengerjaan / Tipe Output Tugas <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-3">
                            <label class="flex items-start gap-2.5 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="output_type" value="checklist" {{ old('output_type', 'checklist') === 'checklist' ? 'checked' : '' }} onchange="toggleOutputType(this.value)" class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900 flex items-center gap-1">
                                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i> Selesai / Belum
                                    </span>
                                    <span class="text-[10px] text-gray-500 leading-tight block mt-0.5">Tugas checklist biasa, ditandai selesai jika sudah dikerjakan</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="output_type" value="target" {{ old('output_type') === 'target' ? 'checked' : '' }} onchange="toggleOutputType(this.value)" class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900 flex items-center gap-1">
                                        <i data-lucide="target" class="w-3.5 h-3.5 text-amber-600"></i> Target Angka / Nilai
                                    </span>
                                    <span class="text-[10px] text-gray-500 leading-tight block mt-0.5">Mempunyai target (contoh: Target omset toko A, penjualan)</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="output_type" value="data_list" {{ old('output_type') === 'data_list' ? 'checked' : '' }} onchange="toggleOutputType(this.value)" class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900 flex items-center gap-1">
                                        <i data-lucide="table" class="w-3.5 h-3.5 text-blue-600"></i> List Jawaban / Data
                                    </span>
                                    <span class="text-[10px] text-gray-500 leading-tight block mt-0.5">Tugas mengumpulkan data (contoh: list nama & kontak)</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="output_type" value="stock_update" {{ old('output_type') === 'stock_update' ? 'checked' : '' }} onchange="toggleOutputType(this.value)" class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900 flex items-center gap-1">
                                        <i data-lucide="package-x" class="w-3.5 h-3.5 text-rose-600"></i> Update Stock Kosong
                                    </span>
                                    <span class="text-[10px] text-gray-500 leading-tight block mt-0.5">Pilih produk & toko untuk dicek dan diupdate stoknya</span>
                                </div>
                            </label>

                            <label class="flex items-start gap-2.5 p-3 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors has-[:checked]:border-primary has-[:checked]:bg-primary/5">
                                <input type="radio" name="output_type" value="full_custom" {{ old('output_type') === 'full_custom' ? 'checked' : '' }} onchange="toggleOutputType(this.value)" class="mt-0.5 text-primary focus:ring-primary">
                                <div>
                                    <span class="block text-xs font-bold text-gray-900 flex items-center gap-1">
                                        <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-purple-600"></i> Full Custom
                                    </span>
                                    <span class="text-[10px] text-gray-500 leading-tight block mt-0.5">Atur sendiri nama, tipe field (angka, teks, textarea, file) & status wajib</span>
                                </div>
                            </label>
                        </div>

                        <!-- Settings for Full Custom (Visible if output_type == full_custom) -->
                        <div id="custom-settings-box" class="{{ old('output_type') === 'full_custom' ? '' : 'hidden' }} mt-3 p-4 bg-purple-50/60 rounded-xl border border-purple-200/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-purple-900 flex items-center gap-1.5">
                                    <i data-lucide="sliders-horizontal" class="w-3.5 h-3.5 text-purple-700"></i>
                                    Konfigurasi Field Input Kustom
                                </h4>
                                <button type="button" onclick="addCustomFieldRow()" class="px-2.5 py-1 text-[10px] font-semibold text-purple-700 bg-purple-100 hover:bg-purple-200 rounded transition-colors flex items-center gap-1 shadow-2xs">
                                    <i data-lucide="plus" class="w-3 h-3"></i> Tambah Field
                                </button>
                            </div>
                            <p class="text-[11px] text-gray-600">
                                Tentukan input yang harus diisi atau diunggah karyawan saat mengerjakan tugas ini (contoh: teks, angka/nominal, textarea, atau unggah file/gambar).
                            </p>

                            <!-- Column header for desktop -->
                            <div class="hidden sm:grid sm:grid-cols-12 gap-2 px-1 text-[10px] font-bold text-purple-900 uppercase tracking-wider">
                                <div class="col-span-5">Nama Field / Label <span class="text-red-500">*</span></div>
                                <div class="col-span-3">Jenis Field <span class="text-red-500">*</span></div>
                                <div class="col-span-3">Status Pengisian</div>
                                <div class="col-span-1 text-center">Hapus</div>
                            </div>

                            <div id="custom-fields-container" class="space-y-2">
                                @php
                                    $oldCustomFields = old('custom_fields', []);
                                    if (!is_array($oldCustomFields)) {
                                        $oldCustomFields = [];
                                    }
                                @endphp

                                @foreach($oldCustomFields as $idx => $cField)
                                    <div class="bg-white p-2.5 rounded-lg border border-purple-200/80 shadow-2xs grid grid-cols-1 sm:grid-cols-12 gap-2 items-center custom-field-row">
                                        <div class="sm:col-span-5">
                                            <label class="block sm:hidden text-[10px] font-semibold text-gray-700 mb-0.5">Nama Field:</label>
                                            <input type="text" name="custom_fields[{{ $idx }}][label]" value="{{ $cField['label'] ?? '' }}" placeholder="Contoh: Nomor Resi, Link Drive, Foto..." class="w-full text-xs py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" required>
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label class="block sm:hidden text-[10px] font-semibold text-gray-700 mb-0.5">Jenis Field:</label>
                                            <select name="custom_fields[{{ $idx }}][type]" class="w-full text-xs py-1.5 px-2.5 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                                                <option value="text" {{ ($cField['type'] ?? '') === 'text' ? 'selected' : '' }}>Teks Singkat (Text)</option>
                                                <option value="number" {{ ($cField['type'] ?? '') === 'number' ? 'selected' : '' }}>Angka / Nominal (Number)</option>
                                                <option value="date" {{ ($cField['type'] ?? '') === 'date' ? 'selected' : '' }}>Tanggal (Date)</option>
                                                <option value="time" {{ ($cField['type'] ?? '') === 'time' ? 'selected' : '' }}>Waktu / Jam (Time)</option>
                                                <option value="datetime" {{ ($cField['type'] ?? '') === 'datetime' ? 'selected' : '' }}>Tanggal & Waktu (DateTime)</option>
                                                <option value="textarea" {{ ($cField['type'] ?? '') === 'textarea' ? 'selected' : '' }}>Teks Panjang (Textarea)</option>
                                                <option value="file" {{ ($cField['type'] ?? '') === 'file' ? 'selected' : '' }}>File / Gambar / Dokumen (File)</option>
                                                <option value="url" {{ ($cField['type'] ?? '') === 'url' ? 'selected' : '' }}>Link / Tautan Web (URL)</option>
                                                <option value="email" {{ ($cField['type'] ?? '') === 'email' ? 'selected' : '' }}>Alamat Email (Email)</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-3">
                                            <label class="block sm:hidden text-[10px] font-semibold text-gray-700 mb-0.5">Status:</label>
                                            <select name="custom_fields[{{ $idx }}][required]" class="w-full text-xs py-1.5 px-2.5 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                                                <option value="1" {{ (!isset($cField['required']) || $cField['required'] == '1' || $cField['required'] === true) ? 'selected' : '' }}>Wajib Diisi (Required)</option>
                                                <option value="0" {{ (isset($cField['required']) && ($cField['required'] == '0' || $cField['required'] === false)) ? 'selected' : '' }}>Opsional</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-1 flex items-center justify-end sm:justify-center">
                                            <button type="button" onclick="removeCustomFieldRow(this)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded transition-colors" title="Hapus Field">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Settings for Target Angka (Visible if output_type == target) -->
                        <div id="target-settings-box" class="{{ old('output_type') === 'target' ? '' : 'hidden' }} mt-3 p-4 bg-amber-50/60 rounded-xl border border-amber-200/80 space-y-3">
                            <h4 class="text-xs font-bold text-amber-900 flex items-center gap-1.5">
                                <i data-lucide="sliders" class="w-3.5 h-3.5"></i>
                                Konfigurasi Target Nilai
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Bentuk Nilai</label>
                                    <select name="target_type" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                                        <option value="currency" {{ old('target_type') === 'currency' ? 'selected' : '' }}>Mata Uang (Rupiah Rp)</option>
                                        <option value="number" {{ old('target_type') === 'number' ? 'selected' : '' }}>Angka / Kuantitas Biasa</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Nilai Target</label>
                                    <input type="number" step="any" name="target_amount" value="{{ old('target_amount') }}" placeholder="Contoh: 5000000 atau 50" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-700 mb-1">Satuan Target (Opsional)</label>
                                    <input type="text" name="target_unit" value="{{ old('target_unit', 'Rupiah') }}" placeholder="Contoh: Rupiah, Pcs, Klien" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                                </div>
                            </div>
                        </div>

                        <!-- Settings for Data List (Visible if output_type == data_list) -->
                        <div id="datalist-settings-box" class="{{ old('output_type') === 'data_list' ? '' : 'hidden' }} mt-3 p-4 bg-blue-50/60 rounded-xl border border-blue-200/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-blue-900 flex items-center gap-1.5">
                                    <i data-lucide="columns" class="w-3.5 h-3.5"></i>
                                    Kolom Data yang Harus Diinput Karyawan
                                </h4>
                                <button type="button" onclick="addColumnRow()" class="px-2 py-1 text-[10px] font-semibold text-blue-700 bg-blue-100 hover:bg-blue-200 rounded transition-colors flex items-center gap-1">
                                    <i data-lucide="plus" class="w-3 h-3"></i> Tambah Kolom
                                </button>
                            </div>
                            <p class="text-[11px] text-gray-600">
                                Tentukan kolom-kolom identitas/jawaban yang wajib diisi karyawan (contoh: Nama Afiliator, No WhatsApp, Link Profil Shopee, Catatan).
                            </p>
                            <div id="columns-container" class="space-y-2">
                                <div class="flex items-center gap-2 column-input-row">
                                    <input type="text" name="list_columns[]" value="Nama Afiliator / Entitas" placeholder="Nama Kolom 1..." class="flex-1 text-xs py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                                    <button type="button" onclick="removeColumnRow(this)" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors"><i data-lucide="x" class="w-4 h-4"></i></button>
                                </div>
                                <div class="flex items-center gap-2 column-input-row">
                                    <input type="text" name="list_columns[]" value="No. WhatsApp / Kontak" placeholder="Nama Kolom 2..." class="flex-1 text-xs py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                                    <button type="button" onclick="removeColumnRow(this)" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors"><i data-lucide="x" class="w-4 h-4"></i></button>
                                </div>
                                <div class="flex items-center gap-2 column-input-row">
                                    <input type="text" name="list_columns[]" value="Akun Medsos / Link Profil" placeholder="Nama Kolom 3..." class="flex-1 text-xs py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                                    <button type="button" onclick="removeColumnRow(this)" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors"><i data-lucide="x" class="w-4 h-4"></i></button>
                                </div>
                                <div class="flex items-center gap-2 column-input-row">
                                    <input type="text" name="list_columns[]" value="Catatan / Status Afiliasi" placeholder="Nama Kolom 4..." class="flex-1 text-xs py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                                    <button type="button" onclick="removeColumnRow(this)" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors"><i data-lucide="x" class="w-4 h-4"></i></button>
                                </div>
                            </div>
                            <div class="pt-2 border-t border-blue-200/60 flex items-center gap-3">
                                <label class="text-[11px] font-semibold text-gray-700 shrink-0">Target Minimal Jumlah Baris Data (Opsional):</label>
                                <input type="number" name="target_count" value="{{ old('target_count') }}" placeholder="Contoh: 10" class="w-32 text-xs py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                            </div>
                        </div>

                        <!-- Settings for Stock Update (Visible if output_type == stock_update) -->
                        <div id="stock-settings-box" class="{{ old('output_type') === 'stock_update' ? '' : 'hidden' }} mt-3 p-4 bg-rose-50/60 rounded-xl border border-rose-200/80 space-y-4">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-rose-900 flex items-center gap-1.5">
                                    <i data-lucide="store" class="w-3.5 h-3.5"></i>
                                    Pilih Toko & Produk yang Akan Di-update
                                </h4>
                                <span class="text-[10px] text-gray-500 italic">Select multiple toko dan produk</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Select Multiple Toko -->
                                <div class="bg-white p-3.5 rounded-lg border border-gray-200 space-y-2">
                                    <div class="flex items-center justify-between pb-1.5 border-b border-gray-100">
                                        <label for="selected_toko_ids" class="text-xs font-semibold text-gray-800 flex items-center gap-1">
                                            <i data-lucide="building-2" class="w-3.5 h-3.5 text-primary"></i>
                                            Pilih Toko / Cabang (Multiple)
                                        </label>
                                        <div class="flex items-center gap-1.5 text-[10px]">
                                            <button type="button" onclick="selectAllSelect2('selected_toko_ids', true)" class="text-primary hover:underline font-semibold">Pilih Semua</button>
                                            <span class="text-gray-300">|</span>
                                            <button type="button" onclick="selectAllSelect2('selected_toko_ids', false)" class="text-gray-500 hover:underline">Hapus Semua</button>
                                        </div>
                                    </div>
                                    <select name="selected_toko_ids[]" id="selected_toko_ids" multiple="multiple" class="w-full text-xs select2-multiple" data-placeholder="-- Pilih Toko (bisa lebih dari satu) --">
                                        @foreach($tokos as $toko)
                                            <option value="{{ $toko->id }}" {{ in_array($toko->id, (array)old('selected_toko_ids', [])) ? 'selected' : '' }}>
                                                {{ $toko->name }} ({{ $toko->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-gray-400">Pilih satu atau beberapa toko yang akan dicek stoknya.</p>
                                </div>

                                <!-- Select Multiple Produk -->
                                <div class="bg-white p-3.5 rounded-lg border border-gray-200 space-y-2">
                                    <div class="flex items-center justify-between pb-1.5 border-b border-gray-100">
                                        <label for="selected_produk_ids" class="text-xs font-semibold text-gray-800 flex items-center gap-1">
                                            <i data-lucide="package" class="w-3.5 h-3.5 text-primary"></i>
                                            Pilih Produk (Multiple)
                                        </label>
                                        <div class="flex items-center gap-1.5 text-[10px]">
                                            <button type="button" onclick="selectAllSelect2('selected_produk_ids', true)" class="text-primary hover:underline font-semibold">Pilih Semua</button>
                                            <span class="text-gray-300">|</span>
                                            <button type="button" onclick="selectAllSelect2('selected_produk_ids', false)" class="text-gray-500 hover:underline">Hapus Semua</button>
                                        </div>
                                    </div>
                                    <select name="selected_produk_ids[]" id="selected_produk_ids" multiple="multiple" class="w-full text-xs select2-multiple" data-placeholder="-- Cari dan Pilih Produk (bisa lebih dari satu) --">
                                        @foreach($produks as $produk)
                                            <option value="{{ $produk->id }}" {{ in_array($produk->id, (array)old('selected_produk_ids', [])) ? 'selected' : '' }}>
                                                {{ $produk->name }} ({{ $produk->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="text-[10px] text-gray-400">Ketik untuk mencari produk. Bisa memilih lebih dari 1 produk.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-3 border-t border-gray-100">
                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Status Awal <span class="text-red-500">*</span>
                        </label>
                        <select name="status" id="status" required class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ old('status', 'draft') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label for="start_date" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Tanggal Mulai
                        </label>
                        <input type="date" name="start_date" id="start_date" value="{{ old('start_date', date('Y-m-d')) }}" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                    </div>

                    <!-- Due Date -->
                    <div>
                        <label for="due_date" class="block text-xs font-semibold text-gray-700 mb-1.5">
                            Tenggat Waktu (Due Date)
                        </label>
                        <input type="date" name="due_date" id="due_date" value="{{ old('due_date') }}" class="w-full text-xs py-2 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary @error('due_date') border-red-500 @enderror">
                        @error('due_date')
                            <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('tasks.index') }}" class="px-4 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2 text-xs font-medium text-white bg-primary rounded-lg hover:bg-primary/90 transition-colors shadow-sm flex items-center gap-1.5">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Simpan Tugas
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    function initStockSelect2() {
        if ($.fn.select2) {
            $('#selected_toko_ids, #selected_produk_ids').each(function() {
                if (!$(this).data('select2')) {
                    $(this).select2({
                        width: '100%',
                        placeholder: $(this).data('placeholder')
                    });
                }
            });
        }
    }

    function selectAllSelect2(id, selectAll) {
        initStockSelect2();
        const el = $('#' + id);
        if (selectAll) {
            el.find('option').prop('selected', true);
        } else {
            el.find('option').prop('selected', false);
        }
        el.trigger('change');
    }

    function toggleOutputType(type) {
        $('#target-settings-box').addClass('hidden');
        $('#datalist-settings-box').addClass('hidden');
        $('#stock-settings-box').addClass('hidden');
        $('#custom-settings-box').addClass('hidden');

        if (type === 'target') {
            $('#target-settings-box').removeClass('hidden');
        } else if (type === 'data_list') {
            $('#datalist-settings-box').removeClass('hidden');
        } else if (type === 'stock_update') {
            $('#stock-settings-box').removeClass('hidden');
            setTimeout(initStockSelect2, 50);
        } else if (type === 'full_custom') {
            $('#custom-settings-box').removeClass('hidden');
            if ($('#custom-fields-container .custom-field-row').length === 0) {
                addCustomFieldRow('Field 1', 'text', '1');
            }
        }
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function applyCompanyPreset(key) {
        if (!key) return;

        const presets = {
            'stock_update': {
                title: 'Update Stock Kosong Toko & Marketplace',
                type: 'stock_update'
            },
            'pesanan_batal': {
                title: 'Rekap Pesanan Batal Harian',
                type: 'data_list',
                columns: ['No. Pesanan', 'Toko / Channel', 'Nama Produk', 'Alasan Batal', 'Tindak Lanjut Customer']
            },
            'pesanan_terkendala': {
                title: 'Daftar Pesanan Terkendala & Komplain',
                type: 'data_list',
                columns: ['No. Pesanan', 'Toko', 'Kurir / No. Resi', 'Kendala (Hilang/Rusak/Retur)', 'Status Penanganan']
            },
            'customer_baru': {
                title: 'Target Pertumbuhan Customer Baru',
                type: 'target',
                target_type: 'number',
                target_unit: 'Customer Baru',
                amount: 50
            },
            'daftar_pesanan': {
                title: 'Rekap Daftar Pesanan Perhari',
                type: 'data_list',
                columns: ['Toko / Cabang', 'Total Pesanan Masuk', 'Total Omset (Rp)', 'Pesanan Siap Kirim', 'Catatan']
            },
            'chat_customer': {
                title: 'Layanan Chat Customer Per Hari',
                type: 'target',
                target_type: 'number',
                target_unit: 'Chat Terlayani',
                amount: 100
            },
            'posting_facebook': {
                title: 'Jadwal & Laporan Posting Facebook',
                type: 'data_list',
                columns: ['Akun / Halaman Facebook', 'Topik / Judul Konten', 'Link Postingan', 'Jam Tayang', 'Status']
            },
            'scraping_wa': {
                title: 'Pengumpulan Data Scraping Nomor WhatsApp',
                type: 'data_list',
                columns: ['Sumber / Nama Grup', 'Nama Kontak', 'Nomor WhatsApp', 'Status Validitas Kontak']
            },
            'artikel_website': {
                title: 'Penulisan & Publikasi Artikel Website',
                type: 'data_list',
                columns: ['Judul Artikel', 'Target Keyword (SEO)', 'Link Artikel Website', 'Jumlah Kata', 'Status Indeks']
            },
            'posting_wa_ig': {
                title: 'Posting Konten WhatsApp Story & Instagram',
                type: 'data_list',
                columns: ['Platform (WA Story / Feed IG / Reels)', 'Judul Konten / Promo', 'Link / Bukti Postingan', 'Jam Posting']
            },
            'flash_sale': {
                title: 'Pengaturan Event Flash Sale Marketplace',
                type: 'data_list',
                columns: ['Toko / Marketplace', 'Nama Event Flash Sale', 'Nama Produk', 'Harga Flash Sale', 'Kuota Stok']
            },
            'campaign': {
                title: 'Daftar Campaign Marketplace yang Diikuti',
                type: 'data_list',
                columns: ['Platform / Toko', 'Nama Campaign Promo', 'Periode Tanggal', 'Jumlah Produk Terdaftar', 'Status Approval']
            },
            'voucher': {
                title: 'Pengaturan & Monitoring Voucher Toko',
                type: 'data_list',
                columns: ['Toko / Channel', 'Kode / Nama Voucher', 'Diskon (Nominal / %)', 'Minimal Belanja', 'Kuota Voucher']
            },
            'broadcast': {
                title: 'Pengaturan Pesan Broadcast Promosi',
                type: 'data_list',
                columns: ['Channel (WA / Shopee Chat)', 'Judul & Tema Pesan', 'Target Segmen Penerima', 'Waktu Kirim', 'Respon / Hasil']
            }
        };

        const config = presets[key];
        if (!config) return;

        // Auto-fill title
        if (!$('#title').val() || $('#title').data('preset-filled')) {
            $('#title').val(config.title).data('preset-filled', true);
        }

        // Check output_type radio
        $(`input[name="output_type"][value="${config.type}"]`).prop('checked', true);
        toggleOutputType(config.type);

        if (config.type === 'data_list' && config.columns) {
            let html = '';
            config.columns.forEach((col, idx) => {
                html += `
                    <div class="flex items-center gap-2 column-input-row">
                        <input type="text" name="list_columns[]" value="${col}" placeholder="Nama Kolom ${idx+1}..." class="flex-1 text-xs py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                        <button type="button" onclick="removeColumnRow(this)" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors"><i data-lucide="x" class="w-4 h-4"></i></button>
                    </div>
                `;
            });
            $('#columns-container').html(html);
        }

        if (config.type === 'target') {
            $('select[name="target_type"]').val(config.target_type || 'number');
            $('input[name="target_unit"]').val(config.target_unit || '');
            if (config.amount) $('input[name="target_amount"]').val(config.amount);
        }

        if (config.type === 'stock_update') {
            setTimeout(function() {
                selectAllSelect2('selected_toko_ids', true);
                selectAllSelect2('selected_produk_ids', false);
            }, 60);
        }

        if (typeof lucide !== 'undefined') lucide.createIcons();

        Swal.fire({
            icon: 'success',
            title: 'Template Diterapkan!',
            text: `Template "${config.title}" berhasil dimuat. Anda dapat menyesuaikan Toko, Produk, dan kolom sesuai kebutuhan.`,
            timer: 1800,
            showConfirmButton: false
        });
    }

    function addColumnRow() {
        const count = $('#columns-container .column-input-row').length + 1;
        const html = `
            <div class="flex items-center gap-2 column-input-row">
                <input type="text" name="list_columns[]" placeholder="Nama Kolom ${count}..." class="flex-1 text-xs py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                <button type="button" onclick="removeColumnRow(this)" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors"><i data-lucide="x" class="w-4 h-4"></i></button>
            </div>
        `;
        $('#columns-container').append(html);
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function removeColumnRow(btn) {
        if ($('#columns-container .column-input-row').length > 1) {
            $(btn).closest('.column-input-row').remove();
        } else {
            Swal.fire('Perhatian', 'Minimal harus ada 1 kolom data.', 'warning');
        }
    }

    function addCustomFieldRow(label = '', type = 'text', required = '1') {
        const idx = $('#custom-fields-container .custom-field-row').length;
        const html = `
            <div class="bg-white p-2.5 rounded-lg border border-purple-200/80 shadow-2xs grid grid-cols-1 sm:grid-cols-12 gap-2 items-center custom-field-row">
                <div class="sm:col-span-5">
                    <label class="block sm:hidden text-[10px] font-semibold text-gray-700 mb-0.5">Nama Field:</label>
                    <input type="text" name="custom_fields[${idx}][label]" value="${escapeHtml(label)}" placeholder="Contoh: Nomor Resi, Link Drive, Foto..." class="w-full text-xs py-1.5 px-3 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary" required>
                </div>
                <div class="sm:col-span-3">
                    <label class="block sm:hidden text-[10px] font-semibold text-gray-700 mb-0.5">Jenis Field:</label>
                    <select name="custom_fields[${idx}][type]" class="w-full text-xs py-1.5 px-2.5 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                        <option value="text" ${type === 'text' ? 'selected' : ''}>Teks Singkat (Text)</option>
                        <option value="number" ${type === 'number' ? 'selected' : ''}>Angka / Nominal (Number)</option>
                        <option value="date" ${type === 'date' ? 'selected' : ''}>Tanggal (Date)</option>
                        <option value="time" ${type === 'time' ? 'selected' : ''}>Waktu / Jam (Time)</option>
                        <option value="datetime" ${type === 'datetime' ? 'selected' : ''}>Tanggal & Waktu (DateTime)</option>
                        <option value="textarea" ${type === 'textarea' ? 'selected' : ''}>Teks Panjang (Textarea)</option>
                        <option value="file" ${type === 'file' ? 'selected' : ''}>File / Gambar / Dokumen (File)</option>
                        <option value="url" ${type === 'url' ? 'selected' : ''}>Link / Tautan Web (URL)</option>
                        <option value="email" ${type === 'email' ? 'selected' : ''}>Alamat Email (Email)</option>
                    </select>
                </div>
                <div class="sm:col-span-3">
                    <label class="block sm:hidden text-[10px] font-semibold text-gray-700 mb-0.5">Status:</label>
                    <select name="custom_fields[${idx}][required]" class="w-full text-xs py-1.5 px-2.5 border border-gray-300 rounded-lg focus:ring-1 focus:ring-primary focus:border-primary">
                        <option value="1" ${required === '1' || required === 1 || required === true ? 'selected' : ''}>Wajib Diisi (Required)</option>
                        <option value="0" ${required === '0' || required === 0 || required === false ? 'selected' : ''}>Opsional</option>
                    </select>
                </div>
                <div class="sm:col-span-1 flex items-center justify-end sm:justify-center">
                    <button type="button" onclick="removeCustomFieldRow(this)" class="p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded transition-colors" title="Hapus Field">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
        `;
        $('#custom-fields-container').append(html);
        reindexCustomFields();
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    function removeCustomFieldRow(btn) {
        if ($('#custom-fields-container .custom-field-row').length > 1) {
            $(btn).closest('.custom-field-row').remove();
            reindexCustomFields();
        } else {
            Swal.fire('Perhatian', 'Minimal harus ada 1 field untuk tipe Full Custom.', 'warning');
        }
    }

    function reindexCustomFields() {
        $('#custom-fields-container .custom-field-row').each(function(idx) {
            $(this).find('input[name*="[label]"]').attr('name', `custom_fields[${idx}][label]`);
            $(this).find('select[name*="[type]"]').attr('name', `custom_fields[${idx}][type]`);
            $(this).find('select[name*="[required]"]').attr('name', `custom_fields[${idx}][required]`);
        });
    }

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    $(document).ready(function() {
        if ($('input[name="output_type"]:checked').val() === 'stock_update') {
            initStockSelect2();
        }
        if ($('input[name="output_type"]:checked').val() === 'full_custom') {
            if ($('#custom-fields-container .custom-field-row').length === 0) {
                addCustomFieldRow('Field 1', 'text', '1');
            }
        }

        $('#company_id').on('change', function() {
            const companyId = $(this).val();
            if (!companyId) return;

            // Load categories
            $.get(`{{ url('task-categories/by-company') }}/${companyId}`, function(categories) {
                let html = '<option value="">-- Tanpa Kategori --</option>';
                categories.forEach(c => {
                    html += `<option value="${c.id}">${c.name} (${c.code})</option>`;
                });
                $('#category_id').html(html);
            });

            // Load priorities
            $.get(`{{ url('task-priorities/by-company') }}/${companyId}`, function(priorities) {
                let html = '<option value="">-- Pilih Prioritas --</option>';
                priorities.forEach(p => {
                    html += `<option value="${p.id}">${p.name} (Level ${p.level})</option>`;
                });
                $('#priority_id').html(html);
            });
        });

        // Validate on submit & Prevent double submit
        $('#task-form').on('submit', function(e) {
            const outputType = $('input[name="output_type"]:checked').val();
            if (outputType === 'full_custom') {
                let hasValidField = false;
                $('#custom-fields-container .custom-field-row').each(function() {
                    if ($.trim($(this).find('input[name*="[label]"]').val()) !== '') {
                        hasValidField = true;
                    }
                });
                if (!hasValidField) {
                    e.preventDefault();
                    Swal.fire('Perhatian', 'Mohon tentukan minimal 1 nama field untuk tipe Full Custom.', 'warning');
                    return false;
                }
            }

            const btn = $(this).find('button[type="submit"]');
            btn.prop('disabled', true).addClass('opacity-60 cursor-not-allowed').html('<i data-lucide="loader" class="w-4 h-4 animate-spin inline mr-1"></i> Menyimpan...');
        });
    });
</script>
@endpush
