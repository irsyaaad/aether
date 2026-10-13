@extends('layouts.app')

@section('title', 'Tambah Program Bimbingan Skripsi - ' . config('app.name'))
@section('page_title', 'Tambah Program Bimbingan Baru')

@push('styles')
<style>
    .ck-editor__editable_inline {
        min-height: 180px;
        border-bottom-left-radius: 0.75rem !important;
        border-bottom-right-radius: 0.75rem !important;
        font-size: 0.8125rem !important;
    }
    .ck-toolbar {
        border-top-left-radius: 0.75rem !important;
        border-top-right-radius: 0.75rem !important;
        border-color: #E5E7EB !important;
        background: #F9FAFB !important;
    }
    .ck.ck-editor__main>.ck-editor__editable {
        border-color: #E5E7EB !important;
        background-color: #FFFFFF !important;
    }
    .ck.ck-editor__main>.ck-editor__editable:focus {
        border-color: #38BDF8 !important;
        box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2) !important;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header Card -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 text-[#1768B5] text-xs font-bold mb-1.5 border border-sky-100">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                Halaman Tambah Program
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Formulir Program Bimbingan Baru</h1>
            <p class="text-xs text-gray-500">Konfigurasikan paket penawaran bimbingan skripsi, foto/banner promosi, tarif, dan fasilitas yang didapatkan mahasiswa.</p>
        </div>
        <a href="{{ route('master.programs.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    <!-- Main Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 sm:p-8">
        <form action="{{ route('master.programs.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Identitas Program & Media Banner -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                    <i data-lucide="image" class="w-4 h-4 text-[#1768B5]"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">1. Foto Banner &amp; Identitas Program</h3>
                </div>

                <!-- Input Upload Banner / Foto Promosi -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Foto / Banner Program Bimbingan</label>
                    <div class="mt-1 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                        <!-- Preview Box -->
                        <div id="banner-preview-wrapper" class="relative w-full sm:w-48 h-32 rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center overflow-hidden shrink-0 group transition hover:border-sky-400">
                            <img id="banner-preview-img" src="" alt="Preview Banner" class="w-full h-full object-cover hidden">
                            <div id="banner-placeholder" class="flex flex-col items-center justify-center p-3 text-center text-gray-400">
                                <i data-lucide="image-plus" class="w-8 h-8 text-sky-500 mb-1.5"></i>
                                <span class="text-[11px] font-bold text-gray-500">Preview Banner</span>
                                <span class="text-[10px] text-gray-400">Rasio 16:9 disarankan</span>
                            </div>
                        </div>

                        <!-- Upload Controls -->
                        <div class="flex-1 space-y-2">
                            <input type="file" id="banner" name="banner" accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml" onchange="previewBannerImage(event)" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-50 file:text-[#1768B5] hover:file:bg-sky-100 cursor-pointer">
                            <p class="text-[11px] text-gray-400 leading-relaxed">
                                Format didukung: <strong>JPG, PNG, WEBP, SVG</strong> (Maks. 3 MB). Banner akan ditampilkan di kartu penawaran katalog agar program terlihat lebih menarik dan profesional bagi calon mahasiswa.
                            </p>
                            <button type="button" id="remove-banner-btn" onclick="clearBannerPreview()" class="text-[11px] font-bold text-rose-600 hover:underline hidden flex items-center gap-1">
                                <i data-lucide="trash-2" class="w-3 h-3"></i>
                                <span>Batalkan Gambar Terpilih</span>
                            </button>
                        </div>
                    </div>
                    @error('banner')
                        <p class="text-[11px] text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Kode Program *</label>
                        <input type="text" name="kode_program" value="{{ old('kode_program') }}" required placeholder="Contoh: PRG-SKR-FULL" class="w-full px-3.5 py-2.5 rounded-xl border @error('kode_program') border-rose-500 @else border-gray-300 @enderror text-xs font-mono uppercase focus:outline-none focus:ring-2 focus:ring-sky-500">
                        @error('kode_program')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Kategori Program *</label>
                        <select name="kategori" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="Bimbingan Skripsi" {{ old('kategori') == 'Bimbingan Skripsi' ? 'selected' : '' }}>Bimbingan Skripsi Full</option>
                            <option value="Bimbingan Proposal" {{ old('kategori') == 'Bimbingan Proposal' ? 'selected' : '' }}>Bimbingan Proposal (Bab 1-3)</option>
                            <option value="Bimbingan Hasil" {{ old('kategori') == 'Bimbingan Hasil' ? 'selected' : '' }}>Bimbingan Hasil &amp; Bab 4-5</option>
                            <option value="Simulasi Sidang" {{ old('kategori') == 'Simulasi Sidang' ? 'selected' : '' }}>Simulasi &amp; Defense Sidang</option>
                            <option value="Bimbingan Tesis" {{ old('kategori') == 'Bimbingan Tesis' ? 'selected' : '' }}>Bimbingan Tesis (S2)</option>
                            <option value="Olah Data" {{ old('kategori') == 'Olah Data' ? 'selected' : '' }}>Olah Data Riset</option>
                            <option value="Pelatihan" {{ old('kategori') == 'Pelatihan' ? 'selected' : '' }}>Pelatihan &amp; Workshop</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Nama Paket Program *</label>
                    <input type="text" name="nama_program" value="{{ old('nama_program') }}" required placeholder="Contoh: Paket Bimbingan Skripsi Komprehensif (Full Bab 1 s/d Bab 5)" class="w-full px-3.5 py-2.5 rounded-xl border @error('nama_program') border-rose-500 @else border-gray-300 @enderror text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                    @error('nama_program')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Jenjang Pendidikan</label>
                        <select name="jenjang" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="S1 (Sarjana)" {{ old('jenjang') == 'S1 (Sarjana)' ? 'selected' : '' }}>S1 (Sarjana)</option>
                            <option value="S2 (Magister)" {{ old('jenjang') == 'S2 (Magister)' ? 'selected' : '' }}>S2 (Magister)</option>
                            <option value="S3 (Doktor)" {{ old('jenjang') == 'S3 (Doktor)' ? 'selected' : '' }}>S3 (Doktor)</option>
                            <option value="S1 / S2" {{ old('jenjang') == 'S1 / S2' ? 'selected' : '' }}>S1 / S2</option>
                            <option value="Semua Jenjang" {{ old('jenjang') == 'Semua Jenjang' ? 'selected' : '' }}>Semua Jenjang</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Target Bab / Cakupan Materi</label>
                        <input type="text" name="target_bab" value="{{ old('target_bab') }}" placeholder="Contoh: Full Bab 1 s/d Bab 5 &amp; Sidang" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                    </div>
                </div>
            </div>

            <!-- Section 2: Biaya & Durasi Pertemuan -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                    <i data-lucide="credit-card" class="w-4 h-4 text-[#1768B5]"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">2. Tarif &amp; Durasi Sesi</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Biaya / Tarif Paket (Rp) *</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-gray-400">Rp</span>
                            <input type="number" name="biaya_standar" value="{{ old('biaya_standar') }}" required min="0" step="10000" placeholder="1500000" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border @error('biaya_standar') border-rose-500 @else border-gray-300 @enderror text-xs font-black focus:outline-none focus:ring-2 focus:ring-sky-500">
                        </div>
                        @error('biaya_standar')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Jumlah Sesi Pertemuan</label>
                        <div class="relative">
                            <input type="number" name="jumlah_sesi" value="{{ old('jumlah_sesi', 4) }}" min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-gray-400 font-semibold pointer-events-none">Sesi</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Durasi per Sesi (Menit)</label>
                        <div class="relative">
                            <input type="number" name="durasi_sesi" value="{{ old('durasi_sesi', 90) }}" min="15" step="15" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-gray-400 font-semibold pointer-events-none">Menit</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Badge Penawaran / Label Promo</label>
                    <input type="text" name="badge" value="{{ old('badge') }}" placeholder="Contoh: Paling Populer, Best Value, Rekomendasi, Hemat 25%" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500">
                    <p class="text-[11px] text-gray-400 mt-1">Label ini akan ditampilkan mencolok di pojok kartu penawaran pada etalase program.</p>
                </div>
            </div>

            <!-- Section 3: Fasilitas & Benefit Penawaran -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">3. Fasilitas &amp; Rincian Deskripsi</h3>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">Fasilitas yang Didapat Mahasiswa</label>
                        <span class="text-[11px] text-[#1768B5] font-semibold flex items-center gap-1">
                            <i data-lucide="list" class="w-3.5 h-3.5"></i>
                            Gunakan icon Bulleted List (•) pada editor
                        </span>
                    </div>
                    <textarea id="fasilitas" name="fasilitas" class="ckeditor w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 leading-relaxed">{{ old('fasilitas') }}</textarea>
                    <p class="text-[11px] text-gray-400 mt-1.5">
                        Tuliskan daftar benefit menggunakan bullet points (daftar poin). Sistem akan otomatis merendernya menjadi daftar checklist hijau pada etalase program.
                    </p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Deskripsi Lengkap / Rincian Program</label>
                    <textarea id="deskripsi" name="deskripsi" class="ckeditor w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 leading-relaxed">{{ old('deskripsi') }}</textarea>
                    <p class="text-[11px] text-gray-400 mt-1.5">
                        Jelaskan latar belakang, capaian lulusan bimbingan, maupun panduan alur konsultasi yang ditawarkan kepada mahasiswa.
                    </p>
                </div>
            </div>

            <!-- Section 4: Pengaturan Status -->
            <div class="pt-2 border-t border-gray-100 flex flex-wrap items-center gap-6">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_populer" value="1" {{ old('is_populer') ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-amber-500 focus:ring-amber-500">
                    <span class="text-xs font-bold text-gray-800">Tandai sebagai Paket Populer (Highlight Emas)</span>
                </label>

                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-sky-600 focus:ring-sky-500">
                    <span class="text-xs font-bold text-gray-800">Status Aktif &amp; Ditampilkan di Katalog Klien</span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('master.programs.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] text-white text-xs font-bold transition shadow-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Program Bimbingan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script>
    const editors = {};

    function initCKEditor(selector, placeholderText) {
        const el = document.querySelector(selector);
        if (!el) return;

        ClassicEditor
            .create(el, {
                placeholder: placeholderText || '',
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', '|',
                    'bulletedList', 'numberedList', '|',
                    'outdent', 'indent', '|',
                    'link', 'blockQuote', '|',
                    'undo', 'redo'
                ],
                heading: {
                    options: [
                        { model: 'paragraph', title: 'Paragraf', class: 'ck-heading_paragraph' },
                        { model: 'heading2', view: 'h2', title: 'Subjudul (H2)', class: 'ck-heading_heading2' },
                        { model: 'heading3', view: 'h3', title: 'Poin Penting (H3)', class: 'ck-heading_heading3' }
                    ]
                }
            })
            .then(editor => {
                editors[selector] = editor;
                editor.model.document.on('change:data', () => {
                    el.value = editor.getData();
                });
            })
            .catch(error => {
                console.error('CKEditor init error on ' + selector, error);
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        initCKEditor('#fasilitas', 'Contoh: • 8x Sesi Bimbingan Intensif (gunakan bullet list toolbar)');
        initCKEditor('#deskripsi', 'Tuliskan deskripsi lengkap, tujuan, serta keunggulan program bimbingan ini...');

        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function() {
                for (const selector in editors) {
                    const el = document.querySelector(selector);
                    if (el && editors[selector]) {
                        el.value = editors[selector].getData();
                    }
                }
            });
        }
    });

    function previewBannerImage(event) {
        const input = event.target;
        const previewImg = document.getElementById('banner-preview-img');
        const placeholder = document.getElementById('banner-placeholder');
        const removeBtn = document.getElementById('remove-banner-btn');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                placeholder.classList.add('hidden');
                removeBtn.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearBannerPreview() {
        const input = document.getElementById('banner');
        const previewImg = document.getElementById('banner-preview-img');
        const placeholder = document.getElementById('banner-placeholder');
        const removeBtn = document.getElementById('remove-banner-btn');

        input.value = '';
        previewImg.src = '';
        previewImg.classList.add('hidden');
        placeholder.classList.remove('hidden');
        removeBtn.classList.add('hidden');
    }
</script>
@endpush
