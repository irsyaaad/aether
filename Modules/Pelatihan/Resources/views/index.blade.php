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
        <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-xs hover:shadow-lg transition group flex flex-col justify-between">
            <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
                        {{ $c['kategori'] }}
                    </span>
                    <span class="text-xs font-extrabold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-md border border-amber-200">
                        ⭐ {{ $c['badge'] }}
                    </span>
                </div>

                <h3 class="text-lg font-bold text-gray-900 group-hover:text-[#2B8BD1] transition leading-snug">
                    {{ $c['judul'] }}
                </h3>

                <div class="space-y-2 text-xs text-gray-600">
                    <div class="flex items-center gap-2">
                        <i data-lucide="user-check" class="w-4 h-4 text-purple-600"></i>
                        <span>Instruktur: <strong class="text-gray-800">{{ $c['instruktur'] }}</strong></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="layers" class="w-4 h-4 text-purple-600"></i>
                        <span>Tingkat: <span class="font-semibold">{{ $c['level'] }}</span></span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="video" class="w-4 h-4 text-purple-600"></i>
                        <span>Durasi: {{ $c['total_modul'] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-purple-600"></i>
                        <span>Jadwal Live: <span class="font-semibold text-gray-900">{{ $c['jadwal'] }}</span></span>
                    </div>
                </div>
            </div>

            <div class="p-6 pt-0 border-t border-gray-100 mt-4 flex items-center justify-between">
                <div class="text-xs text-gray-500">
                    <strong class="text-gray-900 text-sm font-black">{{ $c['peserta'] }}</strong> Mahasiswa Terdaftar
                </div>
                <form action="{{ route('pelatihan.daftar', $c['id']) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#102654] hover:bg-[#1768B5] text-white text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5">
                        <i data-lucide="user-plus" class="w-3.5 h-3.5"></i>
                        Ikuti Pelatihan
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
