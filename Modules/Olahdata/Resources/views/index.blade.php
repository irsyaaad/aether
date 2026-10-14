@extends('layouts.app')

@section('title', 'Modul Olah Data Statistik & Riset - Aether')
@section('page_title', 'Layanan Pengolahan Data Riset')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#1768B5] via-[#2B8BD1] to-[#58B7EA] rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-bold mb-2 border border-white/15">
                <i data-lucide="bar-chart-3" class="w-3.5 h-3.5"></i>
                Aether Data Processing Engine
            </div>
            <h1 class="text-2xl font-black">Project Olah Data &amp; Ekonometrika</h1>
            <p class="text-sm text-sky-100 mt-1 max-w-xl">
                Pantau proses running data, uji asumsi klasik, eliminasi outlier, dan hasil interpretasi tabel Bab 4 secara transparan dengan jaminan garansi re-run.
            </p>
        </div>
        <div class="flex gap-2.5 shrink-0">
            <a href="{{ route('olahdata.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-md transition">
                <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                Upload Dataset Baru
            </a>
        </div>
    </div>

    <!-- Project List -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="p-5 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-gray-900">Project Olah Data Aktif</h3>
                <p class="text-xs text-gray-500">Daftar analisis data yang sedang diproses oleh tim analis Aether.</p>
            </div>
            <a href="{{ route('olahdata.create') }}" class="text-xs font-bold text-[#1768B5] hover:underline flex items-center gap-1">
                <span>+ Ajukan Olah Data</span>
            </a>
        </div>

        <div class="divide-y divide-gray-100">
            @forelse($projects as $p)
            <div class="p-5 hover:bg-gray-50/80 transition flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                <div class="space-y-2 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold 
                            {{ $p['status'] === 'Selesai & ACC' ? 'bg-emerald-100 text-emerald-800' : 'bg-sky-100 text-[#1768B5]' }}">
                            {{ $p['status'] }}
                        </span>
                        
                        @if(isset($p['status_pembayaran']))
                            @if($p['status_pembayaran'] === 'Lunas')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    ✓ Lunas
                                </span>
                            @elseif($p['status_pembayaran'] === 'Menunggu Verifikasi')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                                    ⏳ Menunggu Verifikasi
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700 border border-rose-200">
                                    Belum Dibayar
                                </span>
                            @endif
                        @endif

                        <span class="text-xs font-bold text-gray-400">•</span>
                        <span class="text-xs font-extrabold text-[#B36A12] bg-[#FFF5D8] px-2 py-0.5 rounded-md">{{ $p['software'] }}</span>
                    </div>

                    <h4 class="text-sm font-bold text-gray-900 hover:text-[#2B8BD1] transition">
                        {{ $p['judul'] }}
                    </h4>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-500">
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="file-spreadsheet" class="w-3.5 h-3.5 text-gray-400"></i>
                            File: <code class="text-gray-700 bg-gray-100 px-1 py-0.5 rounded">{{ $p['dataset'] }}</code>
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="user-cog" class="w-3.5 h-3.5 text-gray-400"></i>
                            Analis: <strong class="text-gray-700">{{ $p['analis'] }}</strong>
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-gray-400"></i>
                            Update: {{ $p['updated_at'] }}
                        </span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="pt-1 max-w-md">
                        <div class="flex justify-between text-[11px] font-bold text-gray-500 mb-1">
                            <span>Tahapan Pengerjaan</span>
                            <span>{{ $p['progress'] }}%</span>
                        </div>
                        <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                            <div class="bg-gradient-to-r from-[#2B8BD1] to-[#102654] h-full rounded-full transition-all duration-500" style="width: {{ $p['progress'] }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    @if(isset($p['status_pembayaran']) && $p['status_pembayaran'] === 'Menunggu Pembayaran')
                        <a href="{{ route('olahdata.payment.instruction', $p['id']) }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold transition shadow-xs inline-flex items-center gap-1.5">
                            <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                            Bayar Sekarang
                        </a>
                    @elseif(isset($p['status_pembayaran']) && $p['status_pembayaran'] === 'Menunggu Verifikasi')
                        <a href="{{ route('olahdata.payment.invoice', $p['id']) }}" class="px-4 py-2 rounded-xl bg-sky-50 text-[#1768B5] border border-sky-200 text-xs font-bold transition inline-flex items-center gap-1.5">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                            Lihat Invoice
                        </a>
                    @elseif(isset($p['status_pembayaran']) && $p['status_pembayaran'] === 'Lunas')
                        <a href="{{ route('olahdata.payment.invoice', $p['id']) }}" class="px-3.5 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100 transition inline-flex items-center gap-1.5">
                            <i data-lucide="receipt" class="w-3.5 h-3.5 text-emerald-600"></i>
                            Invoice
                        </a>
                    @endif

                    <button class="px-4 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-100 transition inline-flex items-center gap-1.5">
                        <i data-lucide="download" class="w-3.5 h-3.5"></i>
                        Unduh Output
                    </button>
                    <a href="https://wa.me/6285745143123?text=Halo%20Analis%20Aether,%20saya%20ingin%20tanya%20progres%20project%20olah%20data%20saya" target="_blank" class="px-4 py-2 rounded-xl bg-[#1768B5] text-white text-xs font-bold hover:bg-[#102654] transition inline-flex items-center gap-1.5">
                        <i data-lucide="message-square" class="w-3.5 h-3.5"></i>
                        Diskusi Analis
                    </a>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-3">
                    <i data-lucide="bar-chart-2" class="w-6 h-6"></i>
                </div>
                <h4 class="text-sm font-bold text-gray-900 mb-1">Belum Ada Project Olah Data</h4>
                <p class="text-xs text-gray-500 max-w-sm mx-auto mb-4">
                    Anda belum mengajukan pengolahan dataset riset. Unggah berkas data Anda untuk mulai diolah oleh tim analis statistik kami.
                </p>
                <a href="{{ route('olahdata.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-[#1768B5] hover:bg-[#102654] shadow-sm transition">
                    <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                    <span>Upload Dataset Baru</span>
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
