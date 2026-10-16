@extends('layouts.app')

@section('title', 'Kelas Terdaftar Saya - ' . config('app.name'))
@section('page_title', 'Kelas Terdaftar Saya')

@section('content')
<div class="space-y-6">

    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] rounded-2xl p-6 text-white shadow-sm flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-[#F6C75A] text-xs font-bold mb-2 border border-white/15">
                <i data-lucide="award" class="w-3.5 h-3.5"></i>
                <span>Portal Pembelajaran Mahasiswa</span>
            </div>
            <h1 class="text-xl sm:text-2xl font-black">Kelas &amp; Workshop Terdaftar Saya</h1>
            <p class="text-xs sm:text-sm text-sky-100 mt-1 max-w-xl">
                Akses link pertemuan live mentoring, materi modul silabus, rekaman video, dan unduh sertifikat kelulusan pelatihan Anda.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pelatihan.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-sm transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Jelajahi Workshop Lain</span>
            </a>
        </div>
    </div>

    <!-- Active Enrolled Courses -->
    <div class="space-y-4">
        <h2 class="text-sm font-bold text-gray-900 uppercase tracking-wider flex items-center gap-2">
            <i data-lucide="play-circle" class="w-4 h-4 text-[#1768B5]"></i>
            <span>Kelas yang Sedang Diikuti ({{ $myCourses->count() }} Kelas)</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @forelse($myCourses as $course)
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col justify-between hover:border-[#1768B5]/50 transition-all group">
                @if($course->kelas && $course->kelas->thumbnail_url)
                <div class="relative w-full h-36 bg-slate-100 overflow-hidden">
                    <img src="{{ $course->kelas->thumbnail_url }}" alt="{{ $course->judul_kelas }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent"></div>
                </div>
                @endif
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-1.5">
                            <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ ($course->status ?? 'Terdaftar') === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-blue-50 text-blue-700 border border-blue-200' }}">
                                {{ $course->status ?? 'Terdaftar' }}
                            </span>
                            @if(($course->status_pembayaran ?? '') === 'Menunggu Pembayaran')
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200">
                                    Belum Dibayar
                                </span>
                            @elseif(($course->status_pembayaran ?? '') === 'Menunggu Verifikasi')
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                    Menunggu Verifikasi
                                </span>
                            @elseif(($course->status_pembayaran ?? '') === 'Lunas')
                                <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ✓ Lunas
                                </span>
                            @endif
                        </div>
                        <span class="text-xs font-mono text-gray-400">ID: {{ $course->nomor_registrasi ?? $course->kode_kelas ?? ('KLS-' . str_pad($course->id, 3, '0', STR_PAD_LEFT)) }}</span>
                    </div>

                    <h3 class="text-base font-bold text-gray-900 hover:text-[#1768B5] transition">
                        {{ $course->judul_kelas }}
                    </h3>

                    <div class="flex items-center gap-2 text-xs text-gray-600">
                        <i data-lucide="user" class="w-3.5 h-3.5 text-gray-400"></i>
                        <span>Instruktur: <strong>{{ $course->instruktur ?? 'Instruktur Ahli Aether' }}</strong></span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1.5 pt-2">
                        <div class="flex justify-between text-xs font-medium text-gray-600">
                            <span>Progres Belajar</span>
                            <span class="font-bold text-[#1768B5]">{{ $course->progress ?? 0 }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#1768B5] h-2 rounded-full" style="width: {{ min(100, max(0, (int)($course->progress ?? 0))) }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="pt-5 mt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                    @if(($course->status_pembayaran ?? '') === 'Menunggu Pembayaran')
                        <a href="{{ route('pelatihan.payment.instruction', $course->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-amber-500 hover:bg-amber-600 transition shadow-xs">
                            <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                            <span>Bayar Sekarang</span>
                        </a>
                    @elseif(($course->status_pembayaran ?? '') === 'Menunggu Verifikasi')
                        <a href="{{ route('pelatihan.payment.invoice', $course->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-sky-700 bg-sky-50 border border-sky-200">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                            <span>Verifikasi Admin</span>
                        </a>
                    @elseif(!empty($course->link_zoom))
                        <a href="{{ $course->link_zoom }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] transition">
                            <i data-lucide="video" class="w-3.5 h-3.5"></i>
                            <span>Buka Live Meet</span>
                        </a>
                    @else
                        <span class="text-xs font-semibold text-amber-700 bg-amber-50 px-2.5 py-1.5 rounded-lg border border-amber-200 flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                            <span>{{ $course->jadwal_mulai ? \Carbon\Carbon::parse($course->jadwal_mulai)->format('d M Y') : 'Jadwal Segera' }}</span>
                        </span>
                    @endif

                    <div class="flex items-center gap-2">
                        @if(($course->status_pembayaran ?? '') === 'Lunas')
                        <a href="{{ route('pelatihan.payment.invoice', $course->id) }}" class="inline-flex items-center gap-1 px-2.5 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition" title="Lihat Invoice">
                            <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                        </a>
                        @endif
                        <a href="{{ route('pelatihan.show', $course->kelas_id ?: $course->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                            <span>Akses Materi</span>
                            <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-white rounded-2xl border border-dashed border-gray-300 p-8 text-center">
                <div class="w-12 h-12 bg-sky-50 text-[#1768B5] rounded-xl flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="graduation-cap" class="w-6 h-6"></i>
                </div>
                <h3 class="text-base font-bold text-gray-900">Belum Ada Kelas atau Workshop yang Diikuti</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-md mx-auto">
                    Anda belum mendaftar kelas mentoring atau workshop riset apa pun. Pilih program pelatihan yang sesuai dengan kebutuhan penelitian Anda.
                </p>
                <div class="mt-4">
                    <a href="{{ route('pelatihan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#135392] transition shadow-sm">
                        <i data-lucide="compass" class="w-4 h-4"></i>
                        <span>Cari &amp; Daftar Workshop</span>
                    </a>
                </div>
            </div>
            @endforelse
        </div>
    </div>

</div>
@endsection
