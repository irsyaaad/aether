@extends('layouts.app')

@section('title', 'Pendaftaran & Silabus: ' . $kelas->judul . ' - ' . config('app.name'))
@section('page_title', 'Pendaftaran Workshop & Pelatihan')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(8px);
    }
    .custom-gradient-hero {
        background: linear-gradient(135deg, #0b1a3a 0%, #17366e 40%, #1e5a99 75%, #2B8BD1 100%);
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">
    <!-- Breadcrumb & Step Progress Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-gray-500 flex-wrap">
            <a href="{{ route('pelatihan.index') }}" class="inline-flex items-center gap-1 font-medium text-gray-500 hover:text-gray-700 transition hover:underline">
                <span>Katalog</span>
            </a>
            <span class="text-gray-300">/</span>
            <a href="{{ route('pelatihan.detail', $kelas->id) }}" class="inline-flex items-center gap-1.5 font-bold text-[#1768B5] hover:text-[#0f4d8a] transition hover:underline">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                <span>Detail Workshop</span>
            </a>
            <span class="text-gray-300">•</span>
            <span class="text-gray-700 font-semibold truncate max-w-xs sm:max-w-md">Checkout Pendaftaran</span>
        </div>

        <!-- 3-Step Navigation Bar -->
        <div class="flex items-center gap-2 text-[11px] font-bold">
            <span class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-[#1768B5] text-white shadow-xs">
                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                <span>1. Data Diri &amp; Silabus</span>
            </span>
            <span class="text-gray-300">→</span>
            <span class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-gray-200 text-gray-500 shadow-2xs">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                <span>2. Bayar &amp; Upload Bukti</span>
            </span>
            <span class="text-gray-300">→</span>
            <span class="flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-white border border-gray-200 text-gray-400 shadow-2xs">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                <span>3. Kelas Aktif</span>
            </span>
        </div>
    </div>

    <!-- Main Container Card -->
    <div class="rounded-3xl border border-gray-200/90 bg-white shadow-sm overflow-hidden">
        <!-- Hero Header Card with Rich Gradient & Visual Highlights -->
        <div class="custom-gradient-hero relative px-6 sm:px-9 py-8 sm:py-10 text-white overflow-hidden">
            <!-- Ambient Background Shapes -->
            <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-sky-400/10 blur-3xl pointer-events-none"></div>
            <div class="absolute right-1/3 -bottom-20 w-60 h-60 rounded-full bg-purple-500/10 blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                <div class="space-y-3 max-w-2xl">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/15 text-[#F6C75A] text-[11px] font-extrabold uppercase tracking-wider border border-white/20 backdrop-blur-md">
                            <i data-lucide="sparkles" class="w-3 h-3 text-[#F6C75A]"></i>
                            <span>{{ $kelas->kategori ?? 'Workshop Riset' }}</span>
                        </span>
                        <span class="px-3 py-1 rounded-full bg-white/10 text-sky-200 text-[11px] font-bold border border-white/15 backdrop-blur-md">
                            {{ $kelas->level ?? 'Semua Level' }}
                        </span>
                        @if(isset($kelas->badge) && $kelas->badge)
                        <span class="px-3 py-1 rounded-full bg-[#F6C75A] text-[#102654] text-[11px] font-black shadow-xs flex items-center gap-1">
                            ⭐ {{ $kelas->badge }}
                        </span>
                        @endif
                    </div>

                    <h1 class="text-2xl sm:text-3xl lg:text-[28px] font-black leading-tight tracking-tight text-white drop-shadow-xs">
                        {{ $kelas->judul }}
                    </h1>

                    <p class="text-xs sm:text-sm text-sky-100/90 leading-relaxed max-w-2xl">
                        Pelajari silabus kurikulum di bawah dan isi form pendaftaran. Akses live session Google Meet, file dataset praktik, serta sertifikat resmi akan aktif setelah pendaftaran selesai.
                    </p>
                </div>

                <!-- Right Hero Badges -->
                <div class="flex flex-col sm:flex-row lg:flex-col items-start lg:items-end gap-2.5 text-xs font-bold shrink-0">
                    <div class="rounded-2xl bg-white/15 px-4 py-2 text-white border border-white/20 backdrop-blur-md flex items-center gap-2.5 shadow-xs">
                        <div class="w-7 h-7 rounded-lg bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0">
                            <i data-lucide="award" class="w-4 h-4 text-amber-300"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-sky-200 font-medium">Sertifikasi Resmi</p>
                            <p class="text-xs font-bold">E-Sertifikat Terakreditasi</p>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-white/15 px-4 py-2 text-white border border-white/20 backdrop-blur-md flex items-center gap-2.5 shadow-xs">
                        <div class="w-7 h-7 rounded-lg bg-sky-400/20 text-sky-200 flex items-center justify-center shrink-0">
                            <i data-lucide="user" class="w-4 h-4 text-sky-200"></i>
                        </div>
                        <div>
                            <p class="text-[10px] text-sky-200 font-medium">Instruktur Workshop</p>
                            <p class="text-xs font-bold truncate max-w-[180px]">
                                {{ $kelas->mentor ? ($kelas->mentor->nama_lengkap . ($kelas->mentor->gelar ? ', ' . $kelas->mentor->gelar : '')) : ($kelas->instruktur ?? 'Instruktur Ahli') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('info'))
        <div class="m-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2.5 font-semibold shadow-2xs">
            <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0"></i>
            <span>{{ session('info') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-6 sm:p-8">
            <!-- Left Column: Silabus Curriculum & Form (Col-span 2) -->
            <div class="lg:col-span-2 space-y-7">

                <!-- 1. Silabus & Materi Workshop Card -->
                <div class="rounded-3xl border border-gray-200 bg-white p-6 sm:p-7 shadow-xs space-y-6" id="silabus-section">
                    <!-- Section Header -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-[#1768B5] to-[#2B8BD1] text-white flex items-center justify-center shrink-0 shadow-sm shadow-sky-500/20">
                                <i data-lucide="book-marked" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Materi &amp; Silabus Workshop</h2>
                                <p class="text-xs text-gray-500">Materi, modul praktik, dan silabus yang akan Anda dapatkan</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-[#1768B5] text-xs font-bold border border-sky-200">
                                <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                                <span>{{ isset($materis) && $materis->count() > 0 ? $materis->count() . ' Modul & Sesi' : 'Silabus Lengkap' }}</span>
                            </span>
                            <a href="#form-pendaftaran" class="inline-flex items-center gap-1 text-xs font-bold text-[#1768B5] hover:underline sm:hidden">
                                Ke Form <i data-lucide="arrow-down" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Highlight Curriculum Badges Bar -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-3 text-center flex flex-col items-center justify-center gap-1">
                            <div class="w-7 h-7 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center">
                                <i data-lucide="video" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-[11px] font-bold text-gray-800">Live Mentoring</span>
                            <span class="text-[10px] text-gray-400">Interactive Q&amp;A</span>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-3 text-center flex flex-col items-center justify-center gap-1">
                            <div class="w-7 h-7 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center">
                                <i data-lucide="database" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-[11px] font-bold text-gray-800">Master Dataset</span>
                            <span class="text-[10px] text-gray-400">Latihan Riil Bab 4</span>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-3 text-center flex flex-col items-center justify-center gap-1">
                            <div class="w-7 h-7 rounded-xl bg-sky-100 text-[#1768B5] flex items-center justify-center">
                                <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-[11px] font-bold text-gray-800">Template Word</span>
                            <span class="text-[10px] text-gray-400">Format Siap Sidang</span>
                        </div>
                        <div class="rounded-2xl border border-gray-100 bg-gray-50/70 p-3 text-center flex flex-col items-center justify-center gap-1">
                            <div class="w-7 h-7 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center">
                                <i data-lucide="award" class="w-3.5 h-3.5"></i>
                            </div>
                            <span class="text-[11px] font-bold text-gray-800">E-Sertifikat</span>
                            <span class="text-[10px] text-gray-400">Nomor Registrasi Sah</span>
                        </div>
                    </div>

                    <!-- Materi / Silabus Single Unified List (1 Card Terpadu) -->
                    @if(isset($materis) && $materis->count() > 0)
                    <div class="divide-y divide-gray-100 pt-1">
                        @foreach($materis as $index => $materi)
                        <div class="py-4 first:pt-0 last:pb-0 flex flex-col sm:flex-row sm:items-start justify-between gap-3 hover:bg-gray-50/50 px-2.5 -mx-2.5 rounded-xl transition">
                            <!-- Left: Sequence Number & Content Description -->
                            <div class="flex items-start gap-3.5 flex-1 min-w-0">
                                <span class="w-8 h-8 rounded-xl bg-gradient-to-br from-[#102654] to-[#1768B5] text-white font-black text-xs flex items-center justify-center shrink-0 shadow-2xs mt-0.5">
                                    {{ str_pad($materi->urutan ?? ($index + 1), 2, '0', STR_PAD_LEFT) }}
                                </span>

                                <div class="space-y-1 flex-1 min-w-0">
                                    <h3 class="text-xs sm:text-sm font-bold text-gray-900 leading-snug">
                                        {{ $materi->judul_materi }}
                                    </h3>

                                    @if(!empty($materi->deskripsi))
                                    <p class="text-xs text-gray-500 leading-relaxed">
                                        {{ $materi->deskripsi }}
                                    </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Right: Badges & Format Chips -->
                            <div class="flex items-center gap-2 shrink-0 sm:self-start pt-1 sm:pt-0">
                                @if($materi->tipe_materi === 'Video')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 text-[11px] font-bold border border-rose-100 shadow-2xs">
                                        <i data-lucide="video" class="w-3.5 h-3.5 text-rose-500"></i>
                                        <span>Video{{ $materi->durasi_menit > 0 ? ' • ' . $materi->durasi_menit . ' mnt' : '' }}</span>
                                    </span>
                                @elseif($materi->tipe_materi === 'Dokumen PDF')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-sky-50 text-sky-700 text-[11px] font-bold border border-sky-100 shadow-2xs">
                                        <i data-lucide="file-text" class="w-3.5 h-3.5 text-sky-500"></i>
                                        <span>Dokumen PDF</span>
                                    </span>
                                @elseif($materi->tipe_materi === 'Dataset Praktik')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 text-amber-700 text-[11px] font-bold border border-amber-100 shadow-2xs">
                                        <i data-lucide="database" class="w-3.5 h-3.5 text-amber-500"></i>
                                        <span>Dataset Praktik</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 text-[11px] font-bold border border-indigo-100 shadow-2xs">
                                        <i data-lucide="folder" class="w-3.5 h-3.5 text-indigo-500"></i>
                                        <span>{{ $materi->tipe_materi ?? 'Modul' }}</span>
                                    </span>
                                @endif

                                @if(!empty($materi->is_free_preview))
                                    <span class="text-[10px] text-emerald-600 font-bold flex items-center gap-1">
                                        <i data-lucide="sparkles" class="w-2.5 h-2.5"></i>
                                        Preview
                                    </span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @elseif(!empty($kelas->silabus_singkat) || !empty($kelas->deskripsi))
                    <div class="rounded-2xl border border-sky-100 bg-sky-50/50 p-5 space-y-2">
                        <p class="text-xs font-bold text-sky-900 flex items-center gap-2">
                            <i data-lucide="info" class="w-4 h-4 text-[#1768B5]"></i>
                            Silabus &amp; Ringkasan Kurikulum:
                        </p>
                        <p class="text-xs text-sky-800 leading-relaxed">
                            {{ $kelas->silabus_singkat ?: $kelas->deskripsi }}
                        </p>
                    </div>
                    @endif
                </div>

                <!-- 2. Form Card: Data Diri Peserta (Hanya Isian Data) -->
                <div class="rounded-3xl border border-gray-200 bg-white p-6 sm:p-7 shadow-xs space-y-6" id="form-pendaftaran">
                    <div class="flex items-center justify-between pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center shrink-0 border border-emerald-100 shadow-2xs">
                                <i data-lucide="user-check" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h2 class="text-base sm:text-lg font-black text-gray-900 tracking-tight">Data Diri Peserta</h2>
                                <p class="text-xs text-gray-500">Lengkapi data berikut untuk penerbitan tiket workshop &amp; sertifikat</p>
                            </div>
                        </div>
                        <span class="text-[11px] text-rose-500 font-bold bg-rose-50 px-2.5 py-1 rounded-full border border-rose-100">* Wajib diisi</span>
                    </div>

                    <!-- Guidance Info Box -->
                    <div class="flex items-start gap-3.5 rounded-2xl border border-sky-200/70 bg-gradient-to-r from-sky-50 via-indigo-50/40 to-sky-50 p-4 text-xs text-sky-900 shadow-2xs">
                        <div class="w-8 h-8 rounded-xl bg-[#1768B5] text-white flex items-center justify-center shrink-0 shadow-2xs">
                            <i data-lucide="info" class="w-4 h-4"></i>
                        </div>
                        <div class="space-y-0.5">
                            <p class="font-bold text-gray-900 text-xs">Isi Data Diri Terlebih Dahulu</p>
                            <p class="text-[11px] text-gray-600 leading-relaxed">
                                Pada tahap ini Anda cukup mengisi data diri peserta di bawah. <strong>Pilihan metode pembayaran (QRIS, Virtual Account BCA/Mandiri/BNI/BRI, atau Transfer Bank)</strong> beserta nomor rekening pembayaran akan Anda pilih pada langkah berikutnya setelah menekan tombol lanjut.
                            </p>
                        </div>
                    </div>

                    <form id="checkout-form" action="{{ route('pelatihan.payment.process', $kelas->id) }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- User Profile Inputs -->
                        <div class="space-y-4">
                            <!-- Nama Lengkap -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-bold text-gray-700">
                                        Nama Lengkap <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="text-[10px] text-gray-400 italic">(Gunakan nama asli untuk E-Sertifikat resmi)</span>
                                </div>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#1768B5]">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                    </div>
                                    <input type="text" name="nama_pemesan" required value="{{ old('nama_pemesan', $user->nm_user ?? $user->name ?? '') }}" placeholder="Masukkan Nama Lengkap Anda" class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('nama_pemesan') border-rose-500 @else border-gray-200 @enderror bg-gray-50/30 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1768B5]/20 focus:border-[#1768B5] transition">
                                </div>
                                @error('nama_pemesan')
                                    <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email Aktif & WhatsApp in 2 Columns -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Email Aktif -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                        Email Aktif <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#1768B5]">
                                            <i data-lucide="mail" class="w-4 h-4"></i>
                                        </div>
                                        <input type="email" name="email_pemesan" required value="{{ old('email_pemesan', $user->email ?? '') }}" placeholder="nama@email.com" class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('email_pemesan') border-rose-500 @else border-gray-200 @enderror bg-gray-50/30 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1768B5]/20 focus:border-[#1768B5] transition">
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1">Untuk bukti invoice &amp; file sertifikat.</p>
                                    @error('email_pemesan')
                                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- WhatsApp -->
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                        Nomor WhatsApp <span class="text-rose-500">*</span>
                                    </label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#1768B5]">
                                            <i data-lucide="phone" class="w-4 h-4"></i>
                                        </div>
                                        <input type="text" name="telepon_pemesan" required value="{{ old('telepon_pemesan', $user->telp ?? '') }}" placeholder="Contoh: 08123456789" class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('telepon_pemesan') border-rose-500 @else border-gray-200 @enderror bg-gray-50/30 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1768B5]/20 focus:border-[#1768B5] transition">
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1">Untuk link live meeting &amp; koordinasi grup.</p>
                                    @error('telepon_pemesan')
                                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Asal Universitas & Topik Skripsi -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                        Asal Universitas / Kampus <span class="text-gray-400 font-normal">(Opsional)</span>
                                    </label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#1768B5]">
                                            <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                                        </div>
                                        <input type="text" name="universitas" value="{{ old('universitas') }}" placeholder="Contoh: Universitas Indonesia / UGM" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/30 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1768B5]/20 focus:border-[#1768B5] transition">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                        Topik / Judul Skripsi <span class="text-gray-400 font-normal">(Opsional)</span>
                                    </label>
                                    <div class="relative group">
                                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#1768B5]">
                                            <i data-lucide="book-open" class="w-4 h-4"></i>
                                        </div>
                                        <input type="text" name="judul_skripsi" value="{{ old('judul_skripsi') }}" placeholder="Contoh: Analisis Pengaruh Variabel X terhadap Y" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 bg-gray-50/30 text-xs focus:bg-white focus:outline-none focus:ring-2 focus:ring-[#1768B5]/20 focus:border-[#1768B5] transition">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button Inside Form -->
                        <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="text-[11px] text-gray-500 flex items-center gap-1.5">
                                <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600 shrink-0"></i>
                                <span>Pendaftaran terjamin resmi &amp; data aman</span>
                            </div>
                            <button type="submit" class="w-full sm:w-auto py-3.5 px-7 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition flex items-center justify-center gap-2 cursor-pointer group">
                                <span>Lanjut ke Pembayaran</span>
                                <i data-lucide="arrow-right" class="w-4 h-4 transition group-hover:translate-x-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Aside Summary -->
            <aside class="flex h-fit flex-col gap-5 lg:sticky lg:top-6">
                <!-- Detail Program Card -->
                <div class="rounded-3xl border border-gray-200 bg-white p-5 sm:p-6 shadow-xs space-y-4">
                    <p class="text-[11px] uppercase tracking-[0.15em] text-gray-400 font-extrabold">Ringkasan Workshop</p>
                    
                    <div class="rounded-2xl border border-gray-100 bg-gray-50/80 p-4 space-y-3">
                        @if(!empty($kelas->thumbnail_url))
                        <div class="w-full h-36 rounded-xl overflow-hidden border border-gray-200/80 shadow-2xs">
                            <img src="{{ $kelas->thumbnail_url }}" alt="{{ $kelas->judul }}" class="w-full h-full object-cover">
                        </div>
                        @endif

                        <div>
                            <span class="inline-block px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-sky-100 text-[#1768B5] mb-1">
                                {{ $kelas->kategori ?? 'Workshop' }}
                            </span>
                            <h4 class="text-xs sm:text-sm font-bold text-gray-900 leading-snug">{{ $kelas->judul }}</h4>
                        </div>

                        <div class="flex items-center gap-2 text-[11px] text-gray-600 pt-1 border-t border-gray-200/60">
                            <i data-lucide="user-check" class="w-3.5 h-3.5 text-[#1768B5]"></i>
                            <span>Mentor: <strong>{{ $kelas->mentor ? ($kelas->mentor->nama_lengkap) : ($kelas->instruktur ?? 'Instruktur Ahli') }}</strong></span>
                        </div>
                    </div>

                    <!-- Facilities checklist -->
                    <div class="space-y-2.5 pt-1">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Fasilitas Termasuk:</p>
                        <ul class="space-y-2 text-xs text-gray-600">
                            <li class="flex items-start gap-2.5">
                                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </span>
                                <span class="leading-tight">Akses Live Session Interactive Q&amp;A</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </span>
                                <span class="leading-tight">{{ isset($materis) && $materis->count() > 0 ? $materis->count() . ' Modul & Silabus Kurikulum Lengkap' : 'Silabus Praktik Lengkap' }}</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </span>
                                <span class="leading-tight">Master Dataset Latihan &amp; Syntax Do-File</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </span>
                                <span class="leading-tight">Template Dokumen Narasi Bab 4 Siap Sidang</span>
                            </li>
                            <li class="flex items-start gap-2.5">
                                <span class="w-4 h-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                    <i data-lucide="check" class="w-2.5 h-2.5"></i>
                                </span>
                                <span class="leading-tight">E-Sertifikat Kelulusan Bernomor Resmi</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Detail Pembayaran Card -->
                <div class="rounded-3xl border border-gray-200 bg-white p-5 sm:p-6 shadow-xs space-y-4">
                    <p class="text-[11px] uppercase tracking-[0.15em] text-gray-400 font-extrabold">Detail Biaya</p>

                    <div class="rounded-2xl border border-gray-100 bg-gray-50/80 p-4 text-xs text-gray-700 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Biaya Workshop</span>
                            <span class="font-bold text-gray-800">Rp {{ number_format($kelas->harga ?? 200000, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Biaya Layanan Admin</span>
                            <span class="font-bold text-emerald-600">+ Rp {{ number_format($adminPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-2.5 border-t border-gray-200/90 flex items-center justify-between">
                            <span class="font-bold text-gray-900 text-sm">Total Pembayaran</span>
                            <span class="font-black text-gray-900 text-lg text-emerald-600">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Steps Guide -->
                    <div class="rounded-2xl bg-sky-50/60 border border-sky-100/80 p-3.5 text-[11px] text-gray-600 space-y-1">
                        <p class="font-bold text-gray-800">Alur Checkout:</p>
                        <ol class="space-y-1 text-gray-600">
                            <li>1. Isi data peserta di form.</li>
                            <li>2. Klik tombol untuk memilih metode bayar (QRIS/VA/Transfer).</li>
                            <li>3. Unggah bukti pembayaran &amp; akses langsung aktif.</li>
                        </ol>
                    </div>

                    <!-- Submit Button -->
                    <button type="button" onclick="document.getElementById('checkout-form').submit()" class="w-full py-3.5 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md hover:shadow-lg transition flex items-center justify-center gap-2 cursor-pointer group">
                        <span>Lanjut ke Pembayaran</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 transition group-hover:translate-x-1"></i>
                    </button>
                    
                    <div class="pt-1 flex items-center justify-center gap-3 text-[10px] text-gray-400">
                        <span class="flex items-center gap-1">
                            <i data-lucide="lock" class="w-3 h-3 text-emerald-600"></i>
                            <span>256-Bit SSL</span>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="zap" class="w-3 h-3 text-amber-500"></i>
                            <span>Proses Cepat</span>
                        </span>
                        <span>•</span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="check-circle" class="w-3 h-3 text-sky-500"></i>
                            <span>Garansi Akses</span>
                        </span>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
