@extends('layouts.app')

@section('title', 'Tambah Kelas & Workshop - ' . config('app.name'))
@section('page_title', 'Tambah Kelas & Workshop Baru')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Card -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold mb-1.5 border border-purple-100">
                <i data-lucide="plus-circle" class="w-3.5 h-3.5"></i>
                Halaman Tambah Kelas
            </div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight">Formulir Kelas &amp; Workshop Riset Baru</h1>
            <p class="text-xs text-gray-500">Kelola informasi bootcamp riset, mentor pengampu, foto thumbnail promosi, kuota, dan harga pendaftaran.</p>
        </div>
        <a href="{{ route('master.kelas.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Kembali ke Daftar
        </a>
    </div>

    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-xs space-y-1">
            <div class="font-bold flex items-center gap-1.5">
                <i data-lucide="alert-circle" class="w-4 h-4"></i>
                <span>Terdapat kesalahan pengisian formulir:</span>
            </div>
            <ul class="list-disc list-inside pl-1 text-[11px] space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Main Form Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 sm:p-8">
        <form action="{{ route('master.kelas.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            <input type="hidden" name="_form_page" value="1">

            <!-- Section 1: Thumbnail & Identitas Utama -->
            <div class="space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                    <i data-lucide="image" class="w-4 h-4 text-[#1768B5]"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">1. Foto Thumbnail &amp; Identitas Kelas</h3>
                </div>

                <!-- Upload Thumbnail Preview -->
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">
                        Foto Thumbnail Kelas &amp; Workshop
                    </label>
                    <div class="mt-1 flex flex-col sm:flex-row items-start sm:items-center gap-5">
                        <!-- Preview Box -->
                        <div id="thumbnail-preview-wrapper" class="relative w-full sm:w-56 h-36 rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center overflow-hidden shrink-0 group transition hover:border-[#1768B5]">
                            <img id="thumbnail-preview-img" src="" alt="Preview Thumbnail" class="w-full h-full object-cover hidden">
                            <div id="thumbnail-placeholder" class="flex flex-col items-center justify-center p-3 text-center text-gray-400">
                                <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-1.5">
                                    <i data-lucide="image-plus" class="w-5 h-5"></i>
                                </div>
                                <span class="text-[11px] font-bold text-gray-600">Pilih Foto Thumbnail</span>
                                <span class="text-[10px] text-gray-400">Rasio 16:9 disarankan</span>
                            </div>
                        </div>

                        <!-- Upload Controls -->
                        <div class="flex-1 space-y-2">
                            <input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/jpg,image/webp,image/svg+xml" onchange="previewThumbnailImage(event)" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-sky-50 file:text-[#1768B5] hover:file:bg-sky-100 cursor-pointer">
                            <p class="text-[11px] text-gray-400 leading-relaxed">
                                Format didukung: <strong>JPG, PNG, WEBP, SVG</strong> (Maksimal 3 MB). Thumbnail ini akan ditampilkan di kartu etalase workshop agar kelas terlihat lebih menarik, cantik, dan profesional bagi mahasiswa &amp; calon peserta.
                            </p>
                            <button type="button" id="remove-thumbnail-btn" onclick="clearThumbnailPreview()" class="text-[11px] font-bold text-rose-600 hover:underline hidden flex items-center gap-1">
                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                <span>Batalkan Gambar Terpilih</span>
                            </button>
                        </div>
                    </div>
                    @error('thumbnail')
                        <p class="text-[11px] text-rose-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Kode Kelas <span class="text-rose-500">*</span></label>
                        <input type="text" name="kode_kelas" value="{{ old('kode_kelas') }}" required placeholder="Contoh: KLS-PLS-01" class="w-full px-3.5 py-2.5 rounded-xl border @error('kode_kelas') border-rose-500 @else border-gray-300 @enderror text-xs font-mono uppercase focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                        @error('kode_kelas')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Badge Promosi / Tag Khusus</label>
                        <input type="text" name="badge" value="{{ old('badge') }}" placeholder="Contoh: Terpopuler, Best Seller, Baru, Intensif" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                        <p class="text-[10px] text-gray-400 mt-1">Label mencolok di pojok kartu kelas (opsional).</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Judul Kelas / Nama Pelatihan <span class="text-rose-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required placeholder="Contoh: Mastering SEM-PLS 4: Dari Kerangka Konseptual Hingga Interpretasi Bab 4" class="w-full px-3.5 py-2.5 rounded-xl border @error('judul') border-rose-500 @else border-gray-300 @enderror text-xs focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                    @error('judul')
                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Instruktur / Mentor Pengampu</label>
                        <select name="mentor_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                            <option value="">-- Pilih Mentor Pengampu --</option>
                            @foreach($mentors as $mentor)
                                <option value="{{ $mentor->id }}" {{ old('mentor_id') == $mentor->id ? 'selected' : '' }}>
                                    {{ $mentor->nama_lengkap }}{{ $mentor->gelar ? ', ' . $mentor->gelar : '' }} ({{ $mentor->spesialisasi }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Program Terkait (Opsional)</label>
                        <select name="program_id" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                            <option value="">-- Hubungkan dengan Program Bimbingan --</option>
                            @foreach($programs as $prog)
                                <option value="{{ $prog->id }}" {{ old('program_id') == $prog->id ? 'selected' : '' }}>
                                    {{ $prog->nama_program }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Tingkat (Level) <span class="text-rose-500">*</span></label>
                        <select name="level" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                            <option value="Pemula" {{ old('level') == 'Pemula' ? 'selected' : '' }}>Pemula</option>
                            <option value="Intermediate" {{ old('level') == 'Intermediate' ? 'selected' : '' }}>Intermediate / Menengah</option>
                            <option value="Advanced" {{ old('level') == 'Advanced' ? 'selected' : '' }}>Advanced / Mahir</option>
                            <option value="Semua Level" {{ old('level', 'Semua Level') == 'Semua Level' ? 'selected' : '' }}>Semua Level</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Kategori Bidang</label>
                        <input type="text" name="kategori" value="{{ old('kategori') }}" placeholder="Contoh: Kuantitatif & SEM, Ekonometrika" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Tipe Pelaksanaan <span class="text-rose-500">*</span></label>
                        <select name="tipe_kelas" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs bg-white focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                            <option value="Bootcamp Intensif" {{ old('tipe_kelas') == 'Bootcamp Intensif' ? 'selected' : '' }}>Bootcamp Intensif</option>
                            <option value="Online Webinar" {{ old('tipe_kelas', 'Online Webinar') == 'Online Webinar' ? 'selected' : '' }}>Online Webinar</option>
                            <option value="Self-Paced Video" {{ old('tipe_kelas') == 'Self-Paced Video' ? 'selected' : '' }}>Self-Paced Video</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Section 2: Biaya, Kuota & Jadwal -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                    <i data-lucide="calendar" class="w-4 h-4 text-[#1768B5]"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">2. Biaya Pendaftaran, Kuota &amp; Jadwal</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Biaya Pendaftaran (Rp) <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-xs font-bold text-gray-400">Rp</span>
                            <input type="number" name="harga" value="{{ old('harga') }}" required min="0" step="1000" placeholder="250000" class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border @error('harga') border-rose-500 @else border-gray-300 @enderror text-xs font-black focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                        </div>
                        @error('harga')
                            <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Kuota Maksimal Peserta <span class="text-rose-500">*</span></label>
                        <div class="relative">
                            <input type="number" name="kuota_maksimal" value="{{ old('kuota_maksimal', 50) }}" required min="1" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                            <span class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-xs text-gray-400 font-semibold pointer-events-none">Peserta</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Status Pendaftaran <span class="text-rose-500">*</span></label>
                        <select name="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs bg-white font-semibold focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                            <option value="Dibuka" {{ old('status', 'Dibuka') == 'Dibuka' ? 'selected' : '' }}>Dibuka (Pendaftaran Aktif)</option>
                            <option value="Berlangsung" {{ old('status') == 'Berlangsung' ? 'selected' : '' }}>Berlangsung</option>
                            <option value="Selesai" {{ old('status') == 'Selesai' ? 'selected' : '' }}>Selesai</option>
                            <option value="Ditutup" {{ old('status') == 'Ditutup' ? 'selected' : '' }}>Ditutup (Kunci Pendaftaran)</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" value="{{ old('tanggal_mulai') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" value="{{ old('tanggal_selesai') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Link Pertemuan Webinar / LMS (Zoom / GMeet)</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400 pointer-events-none">
                            <i data-lucide="video" class="w-4 h-4"></i>
                        </span>
                        <input type="url" name="link_meeting" value="{{ old('link_meeting') }}" placeholder="https://meet.google.com/... atau https://zoom.us/j/..." class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                    </div>
                    <p class="text-[10px] text-gray-400 mt-1">Dapat diisi nanti sebelum jadwal pelaksanaan dimulai.</p>
                </div>
            </div>

            <!-- Section 3: Deskripsi & Silabus Singkat -->
            <div class="space-y-4 pt-2">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-100">
                    <i data-lucide="file-text" class="w-4 h-4 text-[#1768B5]"></i>
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">3. Silabus Singkat &amp; Deskripsi Materi</h3>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Silabus Singkat / Highlight Fasilitas</label>
                    <input type="text" name="silabus_singkat" value="{{ old('silabus_singkat') }}" placeholder="Contoh: 8 Sesi Video Praktik + Template Word Bab 4 + Live Q&A" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#1768B5]">
                    <p class="text-[10px] text-gray-400 mt-1">Ringkasan singkat yang ditampilkan pada kartu katalog kelas.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-gray-700 mb-1.5">Deskripsi Lengkap &amp; Cakupan Materi Pelatihan</label>
                    <textarea name="deskripsi" rows="4" placeholder="Jelaskan gambaran umum materi, software yang dipelajari, prasyarat, serta output praktis yang akan dikuasai peserta setelah mengikuti kelas ini..." class="w-full px-3.5 py-2.5 rounded-xl border border-gray-300 text-xs focus:outline-none focus:ring-2 focus:ring-[#1768B5] leading-relaxed">{{ old('deskripsi') }}</textarea>
                </div>
            </div>

            <!-- Section 4: Pengaturan Tambahan -->
            <div class="pt-2 border-t border-gray-100 flex flex-wrap items-center gap-6">
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }} class="h-4 w-4 rounded border-gray-300 text-[#1768B5] focus:ring-[#1768B5]">
                    <span class="text-xs font-bold text-gray-800">Status Aktif &amp; Ditampilkan di Katalog Mahasiswa</span>
                </label>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('master.kelas.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 transition">
                    Batal
                </a>
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-[#1768B5] hover:bg-[#135392] text-white text-xs font-bold transition shadow-sm">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    Simpan Kelas &amp; Workshop
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewThumbnailImage(event) {
        const input = event.target;
        const previewImg = document.getElementById('thumbnail-preview-img');
        const placeholder = document.getElementById('thumbnail-placeholder');
        const removeBtn = document.getElementById('remove-thumbnail-btn');

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

    function clearThumbnailPreview() {
        const input = document.getElementById('thumbnail');
        const previewImg = document.getElementById('thumbnail-preview-img');
        const placeholder = document.getElementById('thumbnail-placeholder');
        const removeBtn = document.getElementById('remove-thumbnail-btn');

        input.value = '';
        previewImg.src = '';
        previewImg.classList.add('hidden');
        placeholder.classList.remove('hidden');
        removeBtn.classList.add('hidden');
    }
</script>
@endpush
