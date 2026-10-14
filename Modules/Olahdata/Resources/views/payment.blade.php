@extends('layouts.app')

@section('title', 'Pembayaran Layanan Olah Data - ' . config('app.name'))
@section('page_title', 'Checkout Layanan Olah Data Riset')

@push('styles')
<style>
    .payment-option-input:checked + .payment-option-card {
        border-color: #1768B5;
        background-color: #F0F9FF;
        box-shadow: 0 0 0 2px rgba(23, 104, 181, 0.2);
    }
</style>
@endpush

@section('content')
<div class="max-w-6xl mx-auto space-y-6 pb-12">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-gray-500">
        <a href="{{ route('olahdata.index') }}" class="inline-flex items-center gap-1.5 font-bold text-[#1768B5] hover:underline">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
            Kembali ke Daftar Project Olah Data
        </a>
        <span class="text-gray-300">•</span>
        <span class="text-gray-700 font-semibold truncate">Checkout: {{ $project->nomor_order }}</span>
    </div>

    <!-- Main Container Card with Header Banner -->
    <div class="rounded-3xl border border-gray-200 bg-white shadow-xs overflow-hidden">
        <!-- Hero Header Card with Signature Gradient -->
        <div class="bg-gradient-to-r from-[#1768B5] via-[#2B346F] to-[#102654] px-6 sm:px-8 py-8 text-white flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-white text-[11px] font-bold uppercase tracking-widest border border-white/20 backdrop-blur-xs">
                    <span>Layanan Olah Data</span>
                    <span>•</span>
                    <span>{{ $project->software_digunakan }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black leading-tight tracking-tight text-white">
                    {{ $project->judul_penelitian }}
                </h1>
                <p class="max-w-3xl text-xs sm:text-sm text-white/90 leading-relaxed">
                    Selesaikan pembayaran pemrosesan dataset Anda. Tim analis data profesional kami siap mengerjakan uji statistik, asumsi klasik, dan interpretasi Bab 4.
                </p>
            </div>
            <div class="flex flex-wrap md:flex-col items-start md:items-end gap-2.5 text-xs font-bold shrink-0">
                <span class="rounded-xl bg-white/15 px-3.5 py-1.5 text-white border border-white/20 backdrop-blur-xs flex items-center gap-1.5">
                    <i data-lucide="cpu" class="w-3.5 h-3.5 text-sky-200"></i>
                    {{ $project->software_digunakan }}
                </span>
                <span class="rounded-xl bg-white/15 px-3.5 py-1.5 text-white border border-white/20 backdrop-blur-xs flex items-center gap-1.5">
                    <i data-lucide="git-commit" class="w-3.5 h-3.5 text-sky-200"></i>
                    {{ $project->metode_analisis }}
                </span>
                <span class="rounded-xl bg-white/15 px-3.5 py-1.5 text-white border border-white/20 backdrop-blur-xs flex items-center gap-1.5">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-300"></i>
                    Garansi Re-Run
                </span>
            </div>
        </div>

        @if(session('info'))
        <div class="m-6 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-center gap-2 font-semibold">
            <i data-lucide="info" class="w-4 h-4 text-amber-600 shrink-0"></i>
            <span>{{ session('info') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-6 sm:p-8">
            <!-- Left Column: Step Guides & Order Form (Col-span 2) -->
            <div class="lg:col-span-2 space-y-4" id="payment-form-section">
                <!-- Step 1 Notice -->
                <div class="flex items-center gap-3.5 rounded-2xl bg-indigo-50/80 border border-indigo-100/80 px-4 sm:px-5 py-3.5 text-indigo-900">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white font-black text-indigo-700 shadow-xs text-xs">
                        1
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm font-bold">Periksa identitas pemesan &amp; kontak WhatsApp</p>
                        <p class="text-[11px] text-indigo-700/80">Pastikan nomor WhatsApp aktif untuk koordinasi draft &amp; hasil olah data bersama analis.</p>
                    </div>
                </div>

                <!-- Step 2 Notice -->
                <div class="flex items-center gap-3.5 rounded-2xl bg-indigo-50/80 border border-indigo-100/80 px-4 sm:px-5 py-3.5 text-indigo-900">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white font-black text-indigo-700 shadow-xs text-xs">
                        2
                    </div>
                    <div>
                        <p class="text-xs sm:text-sm font-bold">Pilih metode pembayaran (VA / QRIS / Transfer Bank)</p>
                        <p class="text-[11px] text-indigo-700/80">Dapatkan kode pembayaran instan atau scan barcode QRIS dan upload bukti transfer.</p>
                    </div>
                </div>

                <!-- Form Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-7 shadow-xs space-y-6">
                    <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50/70 p-3.5 text-xs text-amber-900">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                        <div>
                            <p class="font-bold">Lengkapi Data Diri Pemesan</p>
                            <p class="text-[11px] text-amber-800/80 mt-0.5">Pastikan Nama dan Email Anda benar untuk pengiriman berkas hasil running, do-file/sintaks, serta faktur resmi.</p>
                        </div>
                    </div>

                    <form id="checkout-form" action="{{ route('olahdata.payment.process', $project->id) }}" method="POST" class="space-y-5">
                        @csrf

                        <!-- User Profile Inputs -->
                        <div class="grid grid-cols-1 gap-4">
                            <!-- Nama Lengkap -->
                            <div>
                                <div class="flex items-center justify-between mb-1.5">
                                    <label class="block text-xs font-bold text-gray-700">
                                        Nama Lengkap Mahasiswa / Peneliti <span class="text-rose-500">*</span>
                                    </label>
                                    <span class="text-[10px] text-gray-400 italic">(Digunakan pada lembar sertifikasi hasil analisis)</span>
                                </div>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#1768B5]">
                                        <i data-lucide="user" class="w-4 h-4"></i>
                                    </div>
                                    <input type="text" name="nama_pemesan" required value="{{ old('nama_pemesan', $project->nama_mahasiswa ?? $user->nm_user ?? $user->name ?? '') }}" placeholder="Masukkan Nama Lengkap Anda" class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('nama_pemesan') border-rose-500 @else border-gray-200 @enderror text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
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
                                        <input type="email" name="email_pemesan" required value="{{ old('email_pemesan', $project->email ?? $user->email ?? '') }}" placeholder="nama@email.com" class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('email_pemesan') border-rose-500 @else border-gray-200 @enderror text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                                    </div>
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
                                        <input type="text" name="telepon_pemesan" required value="{{ old('telepon_pemesan', $project->no_whatsapp ?? $user->telp ?? '') }}" placeholder="Contoh: 08123456789" class="w-full pl-10 pr-4 py-2.5 rounded-xl border @error('telepon_pemesan') border-rose-500 @else border-gray-200 @enderror text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                                    </div>
                                    <p class="text-[10px] text-gray-400 mt-1">Digunakan analis untuk konfirmasi hipotesis dan hasil running data.</p>
                                    @error('telepon_pemesan')
                                        <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Asal Universitas -->
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                    Asal Universitas / Institusi
                                </label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400 group-focus-within:text-[#1768B5]">
                                        <i data-lucide="graduation-cap" class="w-4 h-4"></i>
                                    </div>
                                    <input type="text" name="universitas" value="{{ old('universitas', $project->universitas ?? '') }}" placeholder="Contoh: Universitas Indonesia / UGM" class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-transparent transition">
                                </div>
                            </div>
                        </div>

                        <!-- Payment Methods Section -->
                        <div class="pt-4 border-t border-gray-100 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold uppercase tracking-wider text-gray-700">
                                    Pilih Metode Pembayaran <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-emerald-600 font-bold flex items-center gap-1">
                                    <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                    Keamanan Terverifikasi
                                </span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- BCA Virtual Account -->
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="metode_pembayaran" value="BCA Virtual Account" class="payment-option-input peer sr-only" {{ old('metode_pembayaran', 'BCA Virtual Account') == 'BCA Virtual Account' ? 'checked' : '' }}>
                                    <div class="payment-option-card p-3.5 rounded-xl border border-gray-200 bg-white hover:border-sky-300 transition flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-700 font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">
                                                BCA
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-800">BCA Virtual Account</p>
                                                <p class="text-[10px] text-gray-400">Verifikasi instan 24 jam</p>
                                            </div>
                                        </div>
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5] flex items-center justify-center"></div>
                                    </div>
                                </label>

                                <!-- Mandiri Virtual Account -->
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="metode_pembayaran" value="Mandiri Virtual Account" class="payment-option-input peer sr-only" {{ old('metode_pembayaran') == 'Mandiri Virtual Account' ? 'checked' : '' }}>
                                    <div class="payment-option-card p-3.5 rounded-xl border border-gray-200 bg-white hover:border-sky-300 transition flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-700 font-black text-xs flex items-center justify-center shrink-0 border border-amber-100">
                                                MDR
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-800">Mandiri VA</p>
                                                <p class="text-[10px] text-gray-400">Verifikasi otomatis</p>
                                            </div>
                                        </div>
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5] flex items-center justify-center"></div>
                                    </div>
                                </label>

                                <!-- BNI Virtual Account -->
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="metode_pembayaran" value="BNI Virtual Account" class="payment-option-input peer sr-only" {{ old('metode_pembayaran') == 'BNI Virtual Account' ? 'checked' : '' }}>
                                    <div class="payment-option-card p-3.5 rounded-xl border border-gray-200 bg-white hover:border-sky-300 transition flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-teal-50 text-teal-700 font-black text-xs flex items-center justify-center shrink-0 border border-teal-100">
                                                BNI
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-800">BNI Virtual Account</p>
                                                <p class="text-[10px] text-gray-400">Verifikasi instan ATM / Mobile</p>
                                            </div>
                                        </div>
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5] flex items-center justify-center"></div>
                                    </div>
                                </label>

                                <!-- BRI Virtual Account -->
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="metode_pembayaran" value="BRI Virtual Account" class="payment-option-input peer sr-only" {{ old('metode_pembayaran') == 'BRI Virtual Account' ? 'checked' : '' }}>
                                    <div class="payment-option-card p-3.5 rounded-xl border border-gray-200 bg-white hover:border-sky-300 transition flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-800 font-black text-xs flex items-center justify-center shrink-0 border border-blue-100">
                                                BRI
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-800">BRI BRIVA</p>
                                                <p class="text-[10px] text-gray-400">Verifikasi BRIMO otomatis</p>
                                            </div>
                                        </div>
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5] flex items-center justify-center"></div>
                                    </div>
                                </label>

                                <!-- QRIS Instant -->
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="metode_pembayaran" value="QRIS Instant" class="payment-option-input peer sr-only" {{ old('metode_pembayaran') == 'QRIS Instant' ? 'checked' : '' }}>
                                    <div class="payment-option-card p-3.5 rounded-xl border border-gray-200 bg-white hover:border-sky-300 transition flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-rose-50 text-rose-600 font-black text-[11px] flex items-center justify-center shrink-0 border border-rose-100">
                                                QRIS
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-800">QRIS (Gopay / OVO / Dana / BCA)</p>
                                                <p class="text-[10px] text-emerald-600 font-semibold">Scan langsung bayar</p>
                                            </div>
                                        </div>
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5] flex items-center justify-center"></div>
                                    </div>
                                </label>

                                <!-- Transfer Bank BCA -->
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="metode_pembayaran" value="Transfer Bank BCA" class="payment-option-input peer sr-only" {{ old('metode_pembayaran') == 'Transfer Bank BCA' ? 'checked' : '' }}>
                                    <div class="payment-option-card p-3.5 rounded-xl border border-gray-200 bg-white hover:border-sky-300 transition flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-emerald-50 text-emerald-700 font-black text-xs flex items-center justify-center shrink-0 border border-emerald-100">
                                                TF
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-800">Transfer Rekening BCA</p>
                                                <p class="text-[10px] text-gray-400">Manual Transfer &amp; Upload Bukti</p>
                                            </div>
                                        </div>
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5] flex items-center justify-center"></div>
                                    </div>
                                </label>

                                <!-- Transfer Bank Mandiri -->
                                <label class="relative cursor-pointer">
                                    <input type="radio" name="metode_pembayaran" value="Transfer Bank Mandiri" class="payment-option-input peer sr-only" {{ old('metode_pembayaran') == 'Transfer Bank Mandiri' ? 'checked' : '' }}>
                                    <div class="payment-option-card p-3.5 rounded-xl border border-gray-200 bg-white hover:border-sky-300 transition flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-amber-50 text-amber-800 font-black text-xs flex items-center justify-center shrink-0 border border-amber-100">
                                                TF
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-gray-800">Transfer Rekening Mandiri</p>
                                                <p class="text-[10px] text-gray-400">Manual Transfer &amp; Upload Bukti</p>
                                            </div>
                                        </div>
                                        <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5] flex items-center justify-center"></div>
                                    </div>
                                </label>
                            </div>
                            @error('metode_pembayaran')
                                <p class="text-[11px] text-rose-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Action Submit Form -->
                        <div class="pt-4 flex items-center justify-end">
                            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-gradient-to-r from-[#1768B5] to-[#102654] hover:from-[#135392] hover:to-[#0b1c40] text-white font-bold text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                                <span>Lanjutkan ke Instruksi Pembayaran</span>
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Aside Summary -->
            <aside class="flex h-fit flex-col gap-5">
                <!-- Detail Project Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-xs">
                    <p class="text-[11px] uppercase tracking-[0.15em] text-gray-400 font-bold mb-3">Rincian Order Olah Data</p>

                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4 space-y-2">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500">Order ID:</span>
                            <span class="font-mono font-bold text-gray-800">{{ $project->nomor_order }}</span>
                        </div>
                        <h4 class="text-xs font-bold text-gray-900 leading-snug">{{ $project->judul_penelitian }}</h4>
                        <div class="flex flex-wrap items-center gap-2 text-[11px] text-gray-500 pt-1">
                            <span class="inline-flex items-center gap-1 text-sky-700 font-semibold bg-sky-50 px-2 py-0.5 rounded">
                                <i data-lucide="cpu" class="w-3 h-3"></i>
                                {{ $project->software_digunakan }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-indigo-700 font-semibold bg-indigo-50 px-2 py-0.5 rounded">
                                <i data-lucide="git-commit" class="w-3 h-3"></i>
                                {{ $project->metode_analisis }}
                            </span>
                        </div>
                    </div>

                    <!-- Facilities checklist -->
                    <div class="mt-4 space-y-2">
                        <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Termasuk Dalam Layanan:</p>
                        <ul class="space-y-1.5 text-xs text-gray-600">
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5"></i>
                                <span class="leading-tight">Cleaning dataset &amp; eliminasi outlier</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5"></i>
                                <span class="leading-tight">Uji validitas, reliabilitas &amp; asumsi klasik</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5"></i>
                                <span class="leading-tight">Output running (.sav, .dta, sintaks, output tabel)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5"></i>
                                <span class="leading-tight">Draft interpretasi hasil Bab 4 (.docx)</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <i data-lucide="check" class="w-3.5 h-3.5 text-emerald-500 shrink-0 mt-0.5"></i>
                                <span class="leading-tight">Garansi re-run jika ada revisi dosen penguji</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Detail Pembayaran Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-xs space-y-4">
                    <div>
                        <p class="text-[11px] uppercase tracking-[0.15em] text-gray-400 font-bold">Detail Pembayaran</p>
                    </div>

                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4 text-xs text-gray-700 space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Biaya Olah Data</span>
                            <span class="font-bold text-gray-800">Rp {{ number_format($project->total_biaya ?: 450000, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-gray-500">Biaya Layanan Admin</span>
                            <span class="font-bold text-emerald-600">+ Rp {{ number_format($adminPrice, 0, ',', '.') }}</span>
                        </div>
                        <div class="pt-2 border-t border-gray-200/80 flex items-center justify-between">
                            <span class="font-bold text-gray-900 text-sm">Total Pembayaran</span>
                            <span class="font-black text-gray-900 text-base">Rp {{ number_format($totalPrice, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Steps Guide -->
                    <div class="rounded-xl bg-gray-50/80 border border-gray-100 p-4 text-[11px] text-gray-600 space-y-1.5">
                        <p class="font-bold text-gray-800">Alur Pemrosesan:</p>
                        <ol class="space-y-1 text-gray-500">
                            <li>1. Pilih metode pembayaran &amp; kirim data.</li>
                            <li>2. Lakukan transfer sesuai nominal hingga 3 digit unik.</li>
                            <li>3. Unggah bukti transfer pada halaman instruksi.</li>
                            <li>4. Admin memverifikasi &amp; analis langsung running data.</li>
                        </ol>
                    </div>

                    <!-- Submit Button -->
                    <button type="button" onclick="document.getElementById('checkout-form').submit()" class="w-full py-3.5 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer group">
                        <span>Lanjut Pembayaran</span>
                        <i data-lucide="arrow-right" class="w-4 h-4 transition group-hover:translate-x-0.5"></i>
                    </button>
                    
                    <p class="text-[10px] text-center text-gray-400 flex items-center justify-center gap-1">
                        <i data-lucide="lock" class="w-3 h-3 text-emerald-500"></i>
                        <span>Pembayaran Aman &amp; Terenkripsi 256-Bit SSL</span>
                    </p>
                </div>
            </aside>
        </div>
    </div>
</div>
@endsection
