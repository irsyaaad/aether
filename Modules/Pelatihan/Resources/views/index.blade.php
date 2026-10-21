@extends('layouts.app')

@section('title', 'Modul Pelatihan & Workshop Olah Data - Aether')
@section('page_title', 'Katalog Pelatihan & Sertifikasi Riset')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#102654] via-[#6a4cff] to-[#2B8BD1] rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#F6C75A] text-xs font-bold mb-2 border border-white/15">
                <i data-lucide="award" class="w-3.5 h-3.5"></i>
                Aether Academic Learning Hub
            </div>
            <h1 class="text-2xl font-black">Pelatihan, Workshop &amp; Sertifikasi Data</h1>
            <p class="text-sm text-purple-100 mt-1 max-w-xl">
                Tingkatkan kompetensi analisis riset Anda dengan panduan intensif hands-on. Dapatkan dataset latihan, template sintaks/do-file, dan e-sertifikat bernomor resmi.
            </p>
        </div>
        <div class="flex gap-2.5 shrink-0">
            <a href="{{ route('pelatihan.kelas-saya') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-white/15 hover:bg-white/25 border border-white/20 transition">
                <i data-lucide="play-circle" class="w-4 h-4"></i>
                Kelas Terdaftar
            </a>
            <a href="{{ route('pelatihan.sertifikat') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-md transition">
                <i data-lucide="file-check" class="w-4 h-4"></i>
                E-Sertifikat
            </a>
        </div>
    </div>

    <!-- Course Cards Grid -->
    <div class="grid md:grid-cols-2 lg:grid-cols-2 gap-6">
        @foreach($courses as $c)
        <div class="bg-white rounded-2xl border border-gray-200/90 overflow-hidden shadow-xs hover:shadow-xl hover:border-[#1768B5]/40 transition-all duration-300 group flex flex-col justify-between">
            <div>
                <!-- Card Thumbnail Header -->
                <a href="{{ route('pelatihan.detail', $c['id']) }}" class="block relative w-full h-48 sm:h-52 bg-slate-100 overflow-hidden cursor-pointer">
                    @if(!empty($c['thumbnail_url']))
                        <img src="{{ $c['thumbnail_url'] }}" 
                             alt="{{ $c['judul'] }}" 
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                             loading="lazy">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-black/10"></div>
                    @else
                        <div class="w-full h-full bg-gradient-to-br from-[#102654] via-[#1768B5] to-[#2B8BD1] flex flex-col items-center justify-center p-6 text-center relative overflow-hidden group-hover:scale-105 transition-transform duration-500 ease-out">
                            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
                            <div class="w-14 h-14 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex items-center justify-center text-white mb-2 shadow-inner">
                                <i data-lucide="presentation" class="w-7 h-7 text-[#F6C75A]"></i>
                            </div>
                            <span class="text-xs font-bold text-white/90 tracking-wide uppercase">{{ $c['kategori'] ?? 'Workshop Riset' }}</span>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                    @endif

                    <!-- Badges Overlaid at Top -->
                    <div class="absolute top-3 left-3 right-3 flex items-center justify-between gap-2 pointer-events-none">
                        <span class="px-3 py-1 rounded-full text-xs font-bold bg-white/95 backdrop-blur-md text-[#102654] shadow-sm border border-white/40">
                            {{ $c['kategori'] }}
                        </span>
                        @if(!empty($c['badge']))
                        <span class="text-xs font-extrabold text-[#102654] bg-[#F6C75A] backdrop-blur-md px-2.5 py-1 rounded-lg shadow-sm">
                            ⭐ {{ $c['badge'] }}
                        </span>
                        @endif
                    </div>

                    <!-- Level & Price Overlaid at Bottom -->
                    <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between text-white text-xs pointer-events-none">
                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold backdrop-blur-md bg-black/50 border border-white/25 flex items-center gap-1.5">
                            <i data-lucide="layers" class="w-3 h-3 text-[#F6C75A]"></i>
                            <span>{{ $c['level'] }}</span>
                        </span>
                        @if(!empty($c['harga_formatted']))
                        <span class="font-black text-sm text-[#F6C75A] drop-shadow-md font-mono bg-black/50 backdrop-blur-md px-2.5 py-1 rounded-lg border border-white/20">
                            {{ $c['harga_formatted'] }}
                        </span>
                        @endif
                    </div>
                </a>

                <!-- Card Body -->
                <div class="p-6 space-y-4">
                    <a href="{{ route('pelatihan.detail', $c['id']) }}" class="block group-hover:text-[#1768B5]">
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 group-hover:text-[#1768B5] transition-colors leading-snug line-clamp-2">
                            {{ $c['judul'] }}
                        </h3>
                    </a>

                    <div class="space-y-2.5 text-xs text-gray-600">
                        <div class="flex items-center gap-2">
                            <i data-lucide="user-check" class="w-4 h-4 text-[#1768B5] shrink-0"></i>
                            <span class="truncate">Instruktur: <strong class="text-gray-800">{{ $c['instruktur'] }}</strong></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="video" class="w-4 h-4 text-[#1768B5] shrink-0"></i>
                            <span>Durasi &amp; Modul: <span class="font-medium text-gray-800">{{ $c['total_modul'] }}</span></span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i data-lucide="calendar" class="w-4 h-4 text-[#1768B5] shrink-0"></i>
                            <span>Jadwal Live: <span class="font-semibold text-gray-900">{{ $c['jadwal'] }}</span></span>
                        </div>
                        @if(!empty($c['deskripsi_singkat']))
                        <p class="text-gray-500 text-[11px] line-clamp-2 pt-1 border-t border-gray-100">
                            {{ $c['deskripsi_singkat'] }}
                        </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Card Footer -->
            <div class="p-6 pt-0 border-t border-gray-100 mt-2 flex items-center justify-between">
                <div class="text-xs text-gray-500 flex items-center gap-1.5">
                    <i data-lucide="users" class="w-4 h-4 text-gray-400"></i>
                    <span><strong class="text-gray-900 text-sm font-black">{{ $c['peserta'] }}</strong> Peserta</span>
                </div>
                <a href="{{ route('pelatihan.detail', $c['id']) }}" class="px-5 py-2.5 rounded-xl bg-[#102654] hover:bg-[#1768B5] text-white text-xs font-bold transition-all shadow-sm hover:shadow-md inline-flex items-center gap-1.5 cursor-pointer">
                    <i data-lucide="compass" class="w-3.5 h-3.5"></i>
                    <span>Ikuti Pelatihan</span>
                </a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
