@extends('layouts.app')

@section('title', 'Setting Perusahaan - ' . config('app.name'))

@section('content')
<div class="flex flex-wrap items-center lg:items-end justify-between gap-5 pb-7.5">
    <div class="flex flex-col justify-center gap-2">
        <h1 class="text-xl font-medium leading-none text-mono">Setting Perusahaan</h1>
        <div class="flex items-center gap-2 text-sm font-normal text-secondary-foreground">
            Konfigurasi informasi umum dan menyeluruh untuk perusahaan Anda
        </div>
    </div>
</div>

@if(Session::has('success'))
    <div class="kt-alert kt-alert-success mb-5">
        <div class="kt-alert-content">
            <p class="kt-alert-description">{{ Session::get('success') }}</p>
        </div>
    </div>
@endif

@if (count($errors) > 0)
    <div class="kt-alert kt-alert-destructive mb-5">
        <div class="kt-alert-content">
            <div class="kt-alert-description">
                <ul class="list-disc ps-4">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif

<form action="{{ route('setting.perusahaan.update') }}" method="POST" enctype="multipart/form-data" class="w-full">
    @csrf
    @method('PUT')

    <div class="kt-container-fixed">
        <div class="grid gap-5 lg:gap-7.5 xl:w-[38.75rem] mx-auto">
            <!-- Card 1: Informasi Perusahaan -->
            <div class="kt-card pb-2.5">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">
                        Detail Perusahaan
                    </h3>
                </div>
                
                <div class="kt-card-content grid gap-5">
                    <!-- Logo Perusahaan (Photo) -->
                    <div class="flex items-center flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Logo Perusahaan</label>
                        <div class="flex items-center justify-between flex-wrap grow gap-2.5">
                            <span class="text-2sm text-muted-foreground">Format: JPG, JPEG, PNG, SVG (Maks. 2MB)</span>
                            
                            <!-- Image Input Widget -->
                            <div class="relative size-16 rounded-xl border border-border bg-background p-1.5 shadow-sm group overflow-hidden cursor-pointer">
                                <input accept="image/*" name="logo" type="file" id="logo-input" class="absolute inset-0 opacity-0 z-10 cursor-pointer"/>
                                
                                <div class="h-full w-full flex items-center justify-center relative rounded-lg overflow-hidden bg-muted/20">
                                    @if (isset($setting->logo) && $setting->logo)
                                        <img id="logo-preview" src="{{ Storage::url($setting->logo) }}" alt="Logo Perusahaan" class="h-full w-full object-contain"/>
                                        <div id="logo-placeholder" class="text-center flex flex-col items-center justify-center text-muted-foreground hidden">
                                            <i class="ki-filled ki-picture text-xl mb-0.5"></i>
                                            <span class="text-[9px] font-medium leading-none">Pilih Logo</span>
                                        </div>
                                    @else
                                        <div id="logo-placeholder" class="text-center flex flex-col items-center justify-center text-muted-foreground">
                                            <i class="ki-filled ki-picture text-xl mb-0.5"></i>
                                            <span class="text-[9px] font-medium leading-none">Pilih Logo</span>
                                        </div>
                                        <img id="logo-preview" src="" alt="Logo Preview" class="h-full w-full object-contain hidden"/>
                                    @endif
                                    
                                    <!-- Hover Overlay -->
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none">
                                        <i class="ki-filled ki-camera text-white text-base"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nama Perusahaan -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Nama Perusahaan *</label>
                        <input class="kt-input" type="text" name="nama_perusahaan" value="{{ old('nama_perusahaan', $setting->nama_perusahaan ?? '') }}" required placeholder="Masukkan nama perusahaan"/>
                    </div>

                    <!-- Pemilik -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Pemilik *</label>
                        <input class="kt-input" type="text" name="pemilik" value="{{ old('pemilik', $setting->pemilik ?? '') }}" required placeholder="Masukkan nama pemilik"/>
                    </div>

                    <!-- NPWP -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">NPWP</label>
                        <input class="kt-input" type="text" name="npwp" value="{{ old('npwp', $setting->npwp ?? '') }}" placeholder="Masukkan NPWP perusahaan"/>
                    </div>

                    <!-- Telepon -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Telepon</label>
                        <input class="kt-input" type="text" name="telepon" value="{{ old('telepon', $setting->telepon ?? '') }}" placeholder="Masukkan nomor telepon"/>
                    </div>

                    <!-- Email -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Email</label>
                        <input class="kt-input" type="email" name="email" value="{{ old('email', $setting->email ?? '') }}" placeholder="Masukkan email perusahaan"/>
                    </div>

                    <!-- Website -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Website</label>
                        <input class="kt-input" type="text" name="website" value="{{ old('website', $setting->website ?? '') }}" placeholder="Masukkan website perusahaan"/>
                    </div>

                    <!-- Kota -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Kota</label>
                        <input class="kt-input" type="text" name="kota" value="{{ old('kota', $setting->kota ?? '') }}" placeholder="Masukkan kota"/>
                    </div>

                    <!-- Alamat -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Alamat</label>
                        <div class="grow w-full">
                            <textarea class="kt-input min-h-[80px] resize-y" name="alamat" placeholder="Masukkan alamat perusahaan">{{ old('alamat', $setting->alamat ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Visi & Misi -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Visi & Misi</label>
                        <div class="grow w-full">
                            <textarea class="kt-input min-h-[80px] resize-y" name="visi_misi" placeholder="Masukkan visi dan misi perusahaan">{{ old('visi_misi', $setting->visi_misi ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Keterangan</label>
                        <div class="grow w-full">
                            <textarea class="kt-input min-h-[80px] resize-y" name="keterangan" placeholder="Keterangan tambahan">{{ old('keterangan', $setting->keterangan ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="flex justify-end pt-2.5">
                        <button class="kt-btn kt-btn-primary" type="submit">
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>

            <!-- Card 2: Detail Pembaruan -->
            <div class="kt-card pb-2.5">
                <div class="kt-card-header">
                    <h3 class="kt-card-title">
                        Detail Pembaruan
                    </h3>
                </div>
                <div class="kt-card-content grid gap-5">
                    <!-- Pembaruan Terakhir -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Pembaruan Terakhir</label>
                        <div class="grow text-sm text-foreground font-semibold">
                            {{ isset($setting->updated_at) ? $setting->updated_at->diffForHumans() : '-' }}
                        </div>
                    </div>

                    <!-- Diperbarui Oleh -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Diperbarui Oleh</label>
                        <div class="grow text-sm text-foreground font-semibold">
                            {{ $setting->updater->name ?? 'Sistem' }}
                        </div>
                    </div>

                    <!-- Status Konfigurasi -->
                    <div class="flex items-baseline flex-wrap lg:flex-nowrap gap-2.5">
                        <label class="kt-form-label max-w-56 w-full lg:w-1/3 shrink-0">Status Konfigurasi</label>
                        <div class="grow">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/30">
                                Terkonfigurasi
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Logo upload instant preview logic
        const logoInput = document.getElementById('logo-input');
        const logoPreview = document.getElementById('logo-preview');
        const logoPlaceholder = document.getElementById('logo-placeholder');

        if (logoInput && logoPreview) {
            logoInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        logoPreview.src = e.target.result;
                        logoPreview.classList.remove('hidden');
                        if (logoPlaceholder) {
                            logoPlaceholder.classList.add('hidden');
                        }
                    }
                    reader.readAsDataURL(file);
                }
            });
        }
    });
</script>
@endpush
@endsection
