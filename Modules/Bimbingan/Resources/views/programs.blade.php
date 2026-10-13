@extends('layouts.app')

@section('title', 'Pilihan Program & Tarif Bimbingan Skripsi - Aether')
@section('page_title', 'Katalog Program Bimbingan Skripsi')

@section('content')
<div class="space-y-8">
    <!-- Header Hero Banner -->
    <div class="bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-3xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#F6C75A] text-xs font-bold mb-3 border border-white/15 backdrop-blur-xs">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i>
                Program Pendampingan Skripsi Terpercaya
            </div>
            <h1 class="text-3xl font-black tracking-tight leading-tight">
                Pilih Program Bimbingan Sesuai Kebutuhan Anda
            </h1>
            <p class="text-sm text-sky-100 mt-2.5 leading-relaxed">
                Kami menawarkan beragam pilihan paket bimbingan skripsi dan tesis dengan tarif transparan. Didampingi secara intensif 1-on-1 oleh mentor spesialis dari perumusan judul, proposal Bab 1-3, olah data Bab 4, hingga simulasi ujian sidang.
            </p>
        </div>
        <div class="absolute -right-10 -bottom-10 opacity-10 pointer-events-none">
            <i data-lucide="graduation-cap" class="w-80 h-80 text-white"></i>
        </div>
    </div>

    <!-- Filter Category Pills -->
    <div class="flex flex-wrap items-center gap-2 pb-2">
        <a href="{{ route('bimbingan.programs') }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ !request('kategori') ? 'bg-[#1768B5] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            Semua Program
        </a>
        <a href="{{ route('bimbingan.programs', ['kategori' => 'Bimbingan Skripsi']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('kategori') == 'Bimbingan Skripsi' ? 'bg-[#1768B5] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            Skripsi Full Bab 1-5
        </a>
        <a href="{{ route('bimbingan.programs', ['kategori' => 'Bimbingan Proposal']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('kategori') == 'Bimbingan Proposal' ? 'bg-[#1768B5] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            Proposal (Bab 1-3)
        </a>
        <a href="{{ route('bimbingan.programs', ['kategori' => 'Bimbingan Hasil']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('kategori') == 'Bimbingan Hasil' ? 'bg-[#1768B5] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            Hasil &amp; Bab 4-5
        </a>
        <a href="{{ route('bimbingan.programs', ['kategori' => 'Simulasi Sidang']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('kategori') == 'Simulasi Sidang' ? 'bg-[#1768B5] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            Simulasi Sidang
        </a>
        <a href="{{ route('bimbingan.programs', ['kategori' => 'Olah Data']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('kategori') == 'Olah Data' ? 'bg-[#1768B5] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            Olah Data Riset
        </a>
        <a href="{{ route('bimbingan.programs', ['kategori' => 'Bimbingan Tesis']) }}" class="px-4 py-2 rounded-xl text-xs font-bold transition {{ request('kategori') == 'Bimbingan Tesis' ? 'bg-[#1768B5] text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:bg-gray-50' }}">
            Tesis S2
        </a>
    </div>

    <!-- Program Offerings Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($programs as $program)
        <div class="bg-white rounded-3xl border {{ $program->is_populer ? 'border-amber-400 ring-2 ring-amber-400/30 shadow-xl' : 'border-gray-200 shadow-sm' }} p-6 flex flex-col justify-between transition hover:-translate-y-1 hover:shadow-xl relative overflow-hidden group">
            @if($program->is_populer)
            <div class="absolute top-0 right-0">
                <div class="bg-gradient-to-l from-amber-500 to-amber-400 text-white text-[11px] font-black uppercase tracking-wider px-3.5 py-1.5 rounded-bl-2xl shadow-sm flex items-center gap-1">
                    <span>⭐</span>
                    <span>{{ $program->badge ?: 'Paling Populer' }}</span>
                </div>
            </div>
            @elseif($program->badge)
            <div class="absolute top-0 right-0">
                <div class="bg-gradient-to-l from-[#2B8BD1] to-[#1768B5] text-white text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-bl-2xl shadow-xs">
                    {{ $program->badge }}
                </div>
            </div>
            @endif

            <div>
                <!-- Badge Tag & Level -->
                <div class="flex items-center gap-2 mb-3">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-sky-50 text-[#1768B5] border border-sky-100">
                        {{ $program->kategori }}
                    </span>
                    <span class="text-xs font-bold text-gray-400">{{ $program->jenjang }}</span>
                </div>

                <!-- Program Title -->
                <h3 class="text-lg font-black text-gray-900 group-hover:text-[#1768B5] transition leading-snug mb-1">
                    {{ $program->nama_program }}
                </h3>

                <!-- Target Bab / Cakupan -->
                @if($program->target_bab)
                <div class="inline-flex items-center gap-1.5 text-xs text-sky-700 font-semibold mb-3">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5 text-sky-600"></i>
                    <span>Cakupan: {{ $program->target_bab }}</span>
                </div>
                @endif

                <!-- Description -->
                <p class="text-xs text-gray-500 leading-relaxed mb-5">
                    {{ $program->deskripsi }}
                </p>

                <!-- Pricing Box -->
                <div class="bg-gradient-to-br from-gray-50 to-sky-50/30 rounded-2xl p-4 border border-gray-100 mb-5">
                    <span class="text-[11px] text-gray-500 font-bold uppercase tracking-wider">Tarif Paket</span>
                    <div class="flex items-baseline gap-1 mt-0.5">
                        <span class="text-2xl font-black text-gray-900">{{ $program->formatted_biaya }}</span>
                        <span class="text-xs text-gray-500 font-semibold">/ paket</span>
                    </div>
                    <div class="flex items-center gap-3 mt-2.5 text-xs text-gray-600 font-medium pt-2 border-t border-gray-200/60">
                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="calendar" class="w-4 h-4 text-[#1768B5]"></i>
                            <strong>{{ $program->jumlah_sesi ?: 1 }}x</strong> Pertemuan
                        </span>
                        <span class="text-gray-300">•</span>
                        <span class="inline-flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-4 h-4 text-[#1768B5]"></i>
                            <strong>@ {{ $program->durasi_sesi ?: 60 }}</strong> Menit/Sesi
                        </span>
                    </div>
                </div>

                <!-- Facilities Checklist -->
                <div class="space-y-2.5 mb-6">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400">Keuntungan &amp; Fasilitas:</h4>
                    <ul class="space-y-2 text-xs text-gray-700">
                        @foreach($program->fasilitas_array as $fasilitas)
                        <li class="flex items-start gap-2.5">
                            <div class="h-4 w-4 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">
                                <i data-lucide="check" class="w-3 h-3"></i>
                            </div>
                            <span class="leading-tight">{{ $fasilitas }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <!-- CTA Action -->
            <div class="pt-4 border-t border-gray-100">
                <a href="{{ route('bimbingan.create', ['program_id' => $program->id]) }}" class="w-full py-3 px-4 rounded-xl {{ $program->is_populer ? 'bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white shadow-md' : 'bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] text-white shadow-xs' }} font-bold text-xs flex items-center justify-center gap-2 transition">
                    <span>Pilih Program Ini</span>
                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                </a>
            </div>
        </div>
        @empty
        <div class="col-span-3 text-center py-16 bg-white rounded-3xl border border-gray-200">
            <i data-lucide="folder-x" class="w-12 h-12 text-gray-300 mx-auto mb-3"></i>
            <h4 class="text-base font-bold text-gray-700">Tidak ada program untuk kategori ini</h4>
            <p class="text-xs text-gray-400 mt-1">Silakan pilih kategori lain atau tampilkan semua program.</p>
            <a href="{{ route('bimbingan.programs') }}" class="inline-block mt-4 text-xs font-bold text-[#1768B5] hover:underline">
                ← Kembali ke Semua Program
            </a>
        </div>
        @endforelse
    </div>

    <!-- Assurance & FAQ Banner -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-start gap-4">
            <div class="p-3 bg-sky-50 text-[#1768B5] rounded-2xl shrink-0">
                <i data-lucide="help-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <h4 class="text-base font-black text-gray-900">Butuh Konsultasi Kustom atau Bingung Memilih?</h4>
                <p class="text-xs text-gray-500 mt-1 max-w-xl">
                    Tim konsultan kami siap membantu memetakan bab dan metode penelitian Anda untuk menentukan paket yang paling efektif dan hemat biaya.
                </p>
            </div>
        </div>
        <div class="flex items-center gap-3 shrink-0">
            <a href="{{ route('bimbingan.create') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100 transition">
                Formulir Pengajuan Bebas
            </a>
            <a href="https://wa.me/6281234567891?text=Halo%20Aether,%20saya%20ingin%20konsultasi%20program%20bimbingan%20skripsi" target="_blank" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition flex items-center gap-2 shadow-xs">
                <i data-lucide="message-circle" class="w-4 h-4"></i>
                Tanya Tim via WhatsApp
            </a>
        </div>
    </div>
</div>
@endsection
