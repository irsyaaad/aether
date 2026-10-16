@extends('layouts.app')

@section('title', 'E-Sertifikat Kelulusan - ' . config('app.name'))
@section('page_title', 'E-Sertifikat Kelulusan')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-gray-200 shadow-xs">
        <div class="flex items-center gap-3">
            <span class="p-2.5 bg-amber-50 text-amber-700 rounded-2xl border border-amber-200 shrink-0">
                <i data-lucide="award" class="w-6 h-6"></i>
            </span>
            <div>
                <h2 class="text-lg font-bold text-gray-900">E-Sertifikat Pelatihan &amp; Workshop Riset</h2>
                <p class="text-xs text-gray-500">Daftar sertifikat kelulusan yang telah diterbitkan lengkap dengan nomor verifikasi kredensial resmi Aether.</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('pelatihan.kelas-saya') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                <i data-lucide="book-open" class="w-4 h-4"></i>
                <span>Kelas Saya</span>
            </a>
            <a href="{{ route('pelatihan.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#1768B5] hover:bg-[#135392] text-white text-xs font-bold transition shadow-xs">
                <i data-lucide="compass" class="w-4 h-4"></i>
                <span>Katalog Workshop</span>
            </a>
        </div>
    </div>

    <!-- Certificate List -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse($sertifikats as $sertifikat)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-xs p-6 space-y-4 hover:border-amber-300 hover:shadow-md transition">
                <div class="flex items-start justify-between gap-3">
                    <div class="space-y-1">
                        <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 inline-block">
                            E-Sertifikat Kelulusan
                        </span>
                        <h3 class="text-base font-bold text-gray-900 leading-snug pt-0.5">
                            {{ $sertifikat->judul_kelas }}
                        </h3>
                        <p class="text-xs text-slate-500 font-mono font-semibold">
                            No. Sertifikat: <span class="text-[#1768B5]">{{ $sertifikat->nomor_sertifikat }}</span>
                        </p>
                    </div>
                    <div class="p-2.5 bg-emerald-50 text-emerald-600 rounded-2xl shrink-0 border border-emerald-100">
                        <i data-lucide="check-circle" class="w-6 h-6"></i>
                    </div>
                </div>

                <div class="text-xs text-gray-600 border-t border-b border-gray-100 py-3.5 space-y-2">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Penerima Sertifikat:</span>
                        <strong class="text-gray-800 text-right">{{ $sertifikat->nama_pemesan ?: ($sertifikat->user->nm_user ?? 'Peserta Aether') }}</strong>
                    </div>
                    @if(!empty($sertifikat->universitas))
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Institusi / Afiliasi:</span>
                        <span class="text-gray-700 font-medium text-right">{{ $sertifikat->universitas }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Instruktur Utama:</span>
                        <span class="text-gray-700 font-medium text-right">{{ $sertifikat->instruktur ?: 'Instruktur Ahli Aether' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Tanggal Terbit:</span>
                        <span class="text-gray-700 font-medium text-right">{{ ($sertifikat->updated_at ?? $sertifikat->created_at ?? now())->translatedFormat('d F Y') }}</span>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-1">
                    <span class="text-xs text-emerald-700 font-semibold flex items-center gap-1.5">
                        <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                        <span>Terverifikasi Resmi</span>
                    </span>

                    <div class="flex items-center gap-2">
                        <!-- Tombol Lihat Sertifikat -->
                        <a href="{{ route('pelatihan.sertifikat.preview', $sertifikat->id) }}" 
                           target="_blank" 
                           class="inline-flex items-center justify-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-200 transition">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            <span>Lihat Sertifikat</span>
                        </a>

                        <!-- Tombol Unduh PDF -->
                        <a href="{{ route('pelatihan.sertifikat.download', $sertifikat->id) }}" 
                           class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] transition shadow-xs">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span>Unduh PDF</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white rounded-2xl border border-gray-200 p-12 text-center space-y-4">
                <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-200 text-amber-600 flex items-center justify-center mx-auto">
                    <i data-lucide="award" class="w-8 h-8"></i>
                </div>
                <div class="max-w-md mx-auto space-y-1">
                    <h3 class="text-base font-bold text-gray-900">Belum Ada E-Sertifikat Kelulusan</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Sertifikat resmi berlisensi Aether akan otomatis terbit setelah Anda menyelesaikan 100% materi silabus pada kelas workshop yang Anda ikuti.
                    </p>
                </div>
                <div class="pt-2">
                    <a href="{{ route('pelatihan.kelas-saya') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#1768B5] hover:bg-[#135392] text-white text-xs font-bold transition shadow-xs">
                        <i data-lucide="play-circle" class="w-4 h-4"></i>
                        <span>Buka &amp; Pelajari Kelas Saya</span>
                    </a>
                </div>
            </div>
        @endforelse
    </div>

</div>
@endsection
