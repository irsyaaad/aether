@extends('layouts.app')

@section('title', 'Detail Kelas & Daftar Pembeli - ' . config('app.name'))
@section('page_title', 'Detail Kelas & Workshop')

@section('content')
<div class="space-y-6">

    <!-- Top Action Bar & Breadcrumbs -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-xs font-bold mb-1.5 border border-purple-100">
                <i data-lucide="award" class="w-3.5 h-3.5"></i>
                Detail Master Kelas &amp; Workshop Riset
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">{{ $kelas->judul }}</h1>
                @php
                    $statusColors = [
                        'Dibuka' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        'Berlangsung' => 'bg-blue-50 text-blue-700 border-blue-200',
                        'Selesai' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                        'Ditutup' => 'bg-rose-50 text-rose-700 border-rose-200',
                    ];
                    $statusColor = $statusColors[$kelas->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                @endphp
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold border {{ $statusColor }}">
                    @if($kelas->status === 'Dibuka')
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Pendaftaran Dibuka
                    @elseif($kelas->status === 'Berlangsung')
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span> Sedang Berlangsung
                    @elseif($kelas->status === 'Selesai')
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-indigo-600"></i> Kelas Selesai
                    @else
                        <i data-lucide="ban" class="w-3.5 h-3.5 text-rose-600"></i> Kelas Ditutup
                    @endif
                </span>
                @if($kelas->badge)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        ⭐ {{ $kelas->badge }}
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-500 font-mono mt-1">
                Kode Kelas: <span class="font-bold text-[#1768B5]">{{ $kelas->kode_kelas }}</span> &bull; 
                Level: {{ $kelas->level ?? 'Semua Level' }} &bull; 
                Tipe: {{ $kelas->tipe_kelas }} &bull;
                Kategori: {{ $kelas->kategori ?? 'Riset & Metodologi' }}
            </p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <!-- Action Tutup Kelas -->
            @if($kelas->status !== 'Ditutup')
            <button onclick="ubahStatusKelas({{ $kelas->id }}, 'Ditutup', '{{ e(addslashes($kelas->judul)) }}')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 text-xs font-bold hover:bg-rose-100 transition shadow-xs" title="Tutup Pendaftaran Kelas">
                <i data-lucide="ban" class="w-4 h-4"></i>
                Tutup Kelas
            </button>
            @endif

            <!-- Action Selesaikan Kelas -->
            @if($kelas->status !== 'Selesai')
            <button onclick="ubahStatusKelas({{ $kelas->id }}, 'Selesai', '{{ e(addslashes($kelas->judul)) }}')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-indigo-200 bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100 transition shadow-xs" title="Tandai Kelas Selesai">
                <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                Selesaikan Kelas
            </button>
            @endif

            <!-- Action Buka Kelas Kembali -->
            @if($kelas->status === 'Ditutup' || $kelas->status === 'Selesai')
            <button onclick="ubahStatusKelas({{ $kelas->id }}, 'Dibuka', '{{ e(addslashes($kelas->judul)) }}')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-emerald-200 bg-emerald-50 text-emerald-700 text-xs font-bold hover:bg-emerald-100 transition shadow-xs" title="Buka Kembali Pendaftaran Kelas">
                <i data-lucide="play-circle" class="w-4 h-4"></i>
                Buka Pendaftaran
            </button>
            @endif

            <a href="{{ route('master.materi.index', ['kelas_id' => $kelas->id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-purple-200 bg-purple-50 text-purple-700 text-xs font-bold hover:bg-purple-100 transition" title="Kelola Materi & Silabus">
                <i data-lucide="book-open" class="w-4 h-4"></i>
                Materi &amp; Silabus
            </a>

            <a href="{{ route('master.kelas.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-black transition shadow-xs">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Metric Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <!-- Total Pembeli -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-100">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Total Pembeli</span>
                <span class="text-xl font-black text-gray-900">{{ number_format($stats['total_pembeli']) }}</span>
                <span class="text-[10px] text-gray-500 block">Peserta terdaftar</span>
            </div>
        </div>

        <!-- Transaksi Lunas -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                <i data-lucide="badge-check" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Transaksi Lunas</span>
                <span class="text-xl font-black text-emerald-600">{{ number_format($stats['total_lunas']) }}</span>
                <span class="text-[10px] text-gray-500 block">Pembayaran dikonfirmasi</span>
            </div>
        </div>

        <!-- Total Omset -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0 border border-purple-100">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Total Pendapatan</span>
                <span class="text-lg font-black text-gray-900">Rp {{ number_format($stats['total_omset'], 0, ',', '.') }}</span>
                <span class="text-[10px] text-gray-500 block">Omset transaksi lunas</span>
            </div>
        </div>

        <!-- Sisa Kuota -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                <i data-lucide="user-check" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Sisa Kuota Kursi</span>
                <span class="text-xl font-black text-gray-900">{{ $stats['sisa_kuota'] }} <span class="text-xs font-normal text-gray-400">/ {{ $stats['kuota_maksimal'] }}</span></span>
                <span class="text-[10px] text-gray-500 block">{{ $stats['menunggu_bayar'] }} antrian menunggu bayar</span>
            </div>
        </div>
    </div>

    <!-- Specs & Class Info Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Quick Info Card -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                <div class="h-28 w-full bg-gradient-to-r from-[#172554] via-[#1e3a8a] to-[#1768B5] relative flex items-center justify-between p-6 overflow-hidden">
                    <i data-lucide="presentation" class="w-24 h-24 text-white/10 absolute -right-3 -bottom-3"></i>
                    <div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white/20 text-white uppercase tracking-wider">
                            {{ $kelas->tipe_kelas }}
                        </span>
                        <h4 class="text-white font-black text-base mt-1 line-clamp-1">{{ $kelas->judul }}</h4>
                    </div>
                </div>

                <div class="p-6 space-y-3.5">
                    <div class="flex items-center justify-between pb-2.5 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Biaya Pendaftaran</span>
                        <span class="text-base font-black font-mono text-emerald-700">Rp {{ number_format($kelas->harga, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex items-center justify-between pb-2.5 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Mentor Pengampu</span>
                        <span class="text-xs font-bold text-gray-900 text-right">
                            @if($kelas->mentor)
                                {{ $kelas->mentor->nama_lengkap }}{{ $kelas->mentor->gelar ? ', ' . $kelas->mentor->gelar : '' }}
                            @else
                                <span class="text-gray-400 italic">Belum ditentukan</span>
                            @endif
                        </span>
                    </div>

                    <div class="flex items-center justify-between pb-2.5 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Jadwal Pelaksanaan</span>
                        <span class="text-xs font-semibold text-gray-800">
                            {{ $kelas->tanggal_mulai ? date('d M Y', strtotime($kelas->tanggal_mulai)) : '-' }} s/d {{ $kelas->tanggal_selesai ? date('d M Y', strtotime($kelas->tanggal_selesai)) : '-' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between pb-2.5 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Program Terkait</span>
                        <span class="text-xs font-semibold text-sky-700">
                            {{ $kelas->program ? $kelas->program->nama_program : 'Non-Program / Mandiri' }}
                        </span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Link Pertemuan</span>
                        @if($kelas->link_meeting)
                            <a href="{{ $kelas->link_meeting }}" target="_blank" class="inline-flex items-center gap-1 text-xs font-bold text-[#1768B5] hover:underline">
                                <span>Buka Link</span>
                                <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        @else
                            <span class="text-xs text-gray-400 italic">Belum disetel</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <a href="{{ route('pelatihan.payment', $kelas->id) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1768B5] hover:underline">
                    <i data-lucide="shopping-cart" class="w-3.5 h-3.5"></i>
                    Pratinjau Halaman Checkout
                </a>
                <span class="text-[11px] text-gray-400">{{ $kelas->materis->count() }} Modul Silabus</span>
            </div>
        </div>

        <!-- Middle & Right: Silabus & Description -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-xs p-6 space-y-5">
            <div>
                <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 mb-2 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4 text-sky-500"></i>
                    Deskripsi &amp; Cakupan Kelas
                </h3>
                @if($kelas->deskripsi)
                    <div class="text-xs text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100">
                        {!! nl2br(e($kelas->deskripsi)) !!}
                    </div>
                @else
                    <p class="text-xs text-gray-400 italic">Belum ada deskripsi lengkap untuk kelas ini.</p>
                @endif
            </div>

            <div>
                <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 mb-2 flex items-center gap-2">
                    <i data-lucide="list-checks" class="w-4 h-4 text-purple-500"></i>
                    Silabus &amp; Ringkasan Materi
                </h3>
                @if($kelas->silabus_singkat)
                    <div class="text-xs text-gray-700 bg-purple-50/40 p-4 rounded-xl border border-purple-100/60 leading-relaxed">
                        {{ $kelas->silabus_singkat }}
                    </div>
                @elseif($kelas->materis->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        @foreach($kelas->materis as $materi)
                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-100 text-xs flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-sky-100 text-[#1768B5] font-bold text-[11px] flex items-center justify-center shrink-0">
                                    {{ $loop->iteration }}
                                </span>
                                <span class="font-medium text-gray-800 truncate">{{ $materi->judul_materi }}</span>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-400 italic">Belum ada silabus khusus yang didaftarkan.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- User / Buyer Management Table Section -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <i data-lucide="users" class="w-5 h-5 text-[#1768B5]"></i>
                    <h2 class="text-lg font-black text-gray-900 tracking-tight">Daftar User &amp; Mahasiswa Pembeli Kelas</h2>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">Kelola data pembeli, rincian pembayaran, verifikasi bukti transfer, batalkan pesanan, atau selesaikan status pelatihan peserta.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="relative">
                    <input type="text" id="search-pembeli" placeholder="Cari nama, email, no reg..." class="text-xs rounded-xl border border-gray-200 pl-8 pr-3 py-2 w-56 focus:ring-2 focus:ring-[#1768B5] focus:border-transparent">
                    <i data-lucide="search" class="w-3.5 h-3.5 text-gray-400 absolute left-2.5 top-2.5"></i>
                </div>
                <span class="text-xs font-bold px-3 py-1.5 rounded-xl bg-sky-50 text-[#1768B5] border border-sky-100">
                    Total: {{ $transaksis->count() }} Pendaftar
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="table-pembeli">
                <thead>
                    <tr class="bg-gray-50/75 border-b border-gray-200 text-[11px] font-black uppercase tracking-wider text-gray-500">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nomor Registrasi</th>
                        <th class="py-3 px-4">Mahasiswa / User</th>
                        <th class="py-3 px-4">Rincian Pembayaran</th>
                        <th class="py-3 px-4 text-center">Status Pembayaran</th>
                        <th class="py-3 px-4 text-center">Status Peserta &amp; Progress</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($transaksis as $index => $trx)
                    <tr class="hover:bg-sky-50/30 transition row-pembeli" data-search="{{ strtolower($trx->nama_pemesan . ' ' . $trx->email_pemesan . ' ' . $trx->nomor_registrasi . ' ' . $trx->universitas) }}">
                        <!-- No -->
                        <td class="py-3.5 px-4 text-center text-gray-400 font-mono text-[11px]">
                            {{ $index + 1 }}
                        </td>

                        <!-- Nomor Registrasi -->
                        <td class="py-3.5 px-4">
                            <span class="font-mono font-bold text-[#1768B5] text-xs">{{ $trx->nomor_registrasi }}</span>
                            <div class="text-[11px] text-gray-400 mt-0.5">
                                {{ $trx->tanggal_pembelian ? $trx->tanggal_pembelian->format('d M Y, H:i') : ($trx->created_at ? $trx->created_at->format('d M Y, H:i') : '-') }}
                            </div>
                        </td>

                        <!-- Info Mahasiswa -->
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-gray-900 text-sm leading-tight">{{ $trx->nama_pemesan ?: ($trx->user ? $trx->user->name : 'Anonim') }}</div>
                            <div class="text-[11px] text-gray-500 mt-0.5 flex flex-col gap-0.5">
                                <span><i data-lucide="mail" class="inline w-3 h-3 text-gray-400"></i> {{ $trx->email_pemesan ?: ($trx->user ? $trx->user->email : '-') }}</span>
                                @if($trx->telepon_pemesan)
                                <span><i data-lucide="phone" class="inline w-3 h-3 text-gray-400"></i> {{ $trx->telepon_pemesan }}</span>
                                @endif
                                @if($trx->universitas)
                                <span class="text-sky-700 font-medium"><i data-lucide="school" class="inline w-3 h-3 text-sky-500"></i> {{ $trx->universitas }}</span>
                                @endif
                            </div>
                        </td>

                        <!-- Rincian Pembayaran -->
                        <td class="py-3.5 px-4">
                            <div class="font-black text-gray-900 text-sm">
                                Rp {{ number_format($trx->total_bayar ?: $trx->biaya, 0, ',', '.') }}
                            </div>
                            <div class="text-[11px] text-gray-600 mt-0.5">
                                <span class="font-bold text-gray-700">{{ $trx->metode_pembayaran ?: 'Transfer Bank' }}</span>
                                @if($trx->kode_pembayaran)
                                <span class="font-mono block text-gray-500 text-[10px]">{{ $trx->kode_pembayaran }}</span>
                                @endif
                            </div>

                            @if($trx->bukti_pembayaran_url)
                            <div class="mt-1.5">
                                <button type="button" onclick="previewBukti('{{ $trx->bukti_pembayaran_url }}', '{{ $trx->nomor_registrasi }}')" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-[#1768B5] border border-sky-200 hover:bg-sky-100 transition">
                                    <i data-lucide="image" class="w-3 h-3"></i>
                                    Lihat Bukti Bayar
                                </button>
                            </div>
                            @endif
                        </td>

                        <!-- Status Pembayaran -->
                        <td class="py-3.5 px-4 text-center">
                            @if($trx->status_pembayaran === 'Lunas')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    <i data-lucide="check-circle" class="w-3 h-3"></i> Lunas
                                </span>
                            @elseif($trx->status_pembayaran === 'Menunggu Verifikasi')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-50 text-sky-700 border border-sky-200 animate-pulse">
                                    <i data-lucide="clock" class="w-3 h-3"></i> Menunggu Verifikasi
                                </span>
                            @elseif($trx->status_pembayaran === 'Dibatalkan')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 text-red-700 border border-red-200">
                                    <i data-lucide="x-circle" class="w-3 h-3"></i> Dibatalkan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    <i data-lucide="clock" class="w-3 h-3"></i> Menunggu Bayar
                                </span>
                            @endif
                        </td>

                        <!-- Status Peserta & Progress -->
                        <td class="py-3.5 px-4 text-center">
                            @if($trx->status === 'Aktif Berjalan')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    Aktif Berjalan
                                </span>
                            @elseif($trx->status === 'Selesai')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    <i data-lucide="check-check" class="w-3 h-3"></i> Selesai
                                </span>
                            @elseif($trx->status === 'Menunggu Verifikasi')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    Verifikasi Admin
                                </span>
                            @elseif($trx->status === 'Dibatalkan')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    Dibatalkan
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700">
                                    {{ $trx->status }}
                                </span>
                            @endif

                            <div class="w-24 mx-auto bg-gray-200 rounded-full h-1.5 mt-2 overflow-hidden">
                                <div class="bg-[#1768B5] h-1.5 rounded-full" style="width: {{ $trx->progress ?? 0 }}%"></div>
                            </div>
                            <span class="text-[10px] text-gray-400 block mt-0.5">{{ $trx->progress ?? 0 }}% selesai ({{ $trx->modul_selesai ?? 0 }}/{{ $trx->total_modul ?? 8 }} sesi)</span>
                        </td>

                        <!-- Aksi -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                <!-- Verifikasi Pembayaran -->
                                @if($trx->status_pembayaran !== 'Lunas' && $trx->status_pembayaran !== 'Dibatalkan')
                                <button onclick="verifikasiPembayaranPeserta({{ $trx->id }}, '{{ $trx->nomor_registrasi }}', '{{ e($trx->nama_pemesan) }}')" class="px-2.5 py-1.5 rounded-lg border border-emerald-200 bg-emerald-500 hover:bg-emerald-600 text-white text-[11px] font-bold transition flex items-center gap-1 shadow-xs" title="Verifikasi Pembayaran & Aktifkan Peserta">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                                    Verifikasi
                                </button>
                                @endif

                                <!-- Selesaikan Pelatihan Peserta -->
                                @if($trx->status === 'Aktif Berjalan')
                                <button onclick="selesaikanPelatihanPeserta({{ $trx->id }}, '{{ e($trx->nama_pemesan) }}')" class="px-2.5 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-[11px] font-bold transition flex items-center gap-1" title="Tandai Selesai Pelatihan Peserta">
                                    <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                                    Selesaikan
                                </button>
                                @endif

                                <!-- Batalkan Transaksi -->
                                @if($trx->status_pembayaran !== 'Dibatalkan')
                                <button onclick="batalkanTransaksiPeserta({{ $trx->id }}, '{{ $trx->nomor_registrasi }}')" class="px-2.5 py-1.5 rounded-lg border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100 text-[11px] font-bold transition flex items-center gap-1" title="Batalkan Pembayaran / Pendaftaran">
                                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                    Batalkan
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center">
                            <div class="flex flex-col items-center justify-center space-y-2">
                                <div class="w-12 h-12 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                                    <i data-lucide="user-x" class="w-6 h-6"></i>
                                </div>
                                <span class="text-sm font-bold text-gray-700">Belum Ada Mahasiswa Pembeli</span>
                                <p class="text-xs text-gray-400 max-w-sm">Belum ada mahasiswa yang melakukan pemesanan untuk kelas ini. Promosikan kelas untuk mendapatkan pendaftar.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Preview Bukti Transfer -->
<div id="modal-bukti" class="fixed inset-0 z-50 hidden bg-gray-900/70 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 bg-gray-50/80 shrink-0">
            <div>
                <h3 class="font-bold text-gray-900 text-sm">Bukti Transfer Pembayaran</h3>
                <p id="modal-bukti-subtitle" class="text-xs text-gray-500 font-mono"></p>
            </div>
            <button type="button" onclick="closeBuktiModal()" class="text-gray-400 hover:text-gray-600 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="p-4 overflow-y-auto flex-1 flex items-center justify-center bg-gray-100/50">
            <img id="img-bukti-preview" src="" alt="Bukti Transfer" class="max-h-[60vh] max-w-full rounded-xl shadow-xs border border-gray-200 object-contain">
        </div>
        <div class="flex items-center justify-between px-6 py-3 border-t border-gray-200 bg-gray-50 shrink-0">
            <a id="btn-open-bukti" href="" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1768B5] hover:underline">
                <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                Buka Tab Penuh
            </a>
            <button type="button" onclick="closeBuktiModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 bg-white border border-gray-200 hover:bg-gray-100 transition">
                Tutup
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        if (window.lucide) {
            lucide.createIcons();
        }

        // Live search filter on pembeli table
        $('#search-pembeli').on('keyup', function() {
            let val = $(this).val().toLowerCase();
            $('.row-pembeli').each(function() {
                let text = $(this).data('search');
                if (text && text.indexOf(val) > -1) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });

        // Close modal on backdrop click or escape
        $('#modal-bukti').on('click', function(e) {
            if (e.target === this) {
                closeBuktiModal();
            }
        });
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && !$('#modal-bukti').hasClass('hidden')) {
                closeBuktiModal();
            }
        });
    });

    function previewBukti(url, noReg) {
        $('#modal-bukti-subtitle').text('No. Registrasi: ' + noReg);
        $('#img-bukti-preview').attr('src', url);
        $('#btn-open-bukti').attr('href', url);
        $('#modal-bukti').removeClass('hidden');
        if (window.lucide) { lucide.createIcons(); }
    }

    function closeBuktiModal() {
        $('#modal-bukti').addClass('hidden');
    }

    function ubahStatusKelas(id, newStatus, title) {
        let actionWord = 'mengubah status';
        let confirmColor = '#1768B5';
        let actionDesc = 'Status kelas akan diubah menjadi ' + newStatus + '.';

        if (newStatus === 'Ditutup') {
            actionWord = 'menutup kelas';
            confirmColor = '#e11d48';
            actionDesc = 'Pendaftaran untuk kelas ini akan ditutup dan tidak dapat dipesan oleh mahasiswa.';
        } else if (newStatus === 'Selesai') {
            actionWord = 'menselesaikan kelas';
            confirmColor = '#4f46e5';
            actionDesc = 'Kelas akan ditandai selesai. Pelaksanaan workshop dianggap telah rampung.';
        } else if (newStatus === 'Dibuka') {
            actionWord = 'membuka pendaftaran kelas';
            confirmColor = '#059669';
            actionDesc = 'Pendaftaran kelas ini akan dibuka kembali untuk mahasiswa.';
        }

        Swal.fire({
            title: 'Konfirmasi ' + actionWord.charAt(0).toUpperCase() + actionWord.slice(1) + '?',
            text: 'Apakah Anda yakin ingin ' + actionWord + ' "' + title + '"? ' + actionDesc,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: confirmColor,
            confirmButtonText: 'Ya, ' + (newStatus === 'Ditutup' ? 'Tutup Kelas' : (newStatus === 'Selesai' ? 'Selesaikan Kelas' : 'Buka Kelas')),
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("master/kelas") }}/' + id + '/update-status',
                    type: 'POST',
                    data: {
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Gagal memperbarui status kelas.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: msg
                        });
                    }
                });
            }
        });
    }

    function verifikasiPembayaranPeserta(id, noReg, nama) {
        Swal.fire({
            title: 'Verifikasi Pembayaran?',
            text: 'Verifikasi transaksi #' + noReg + ' atas nama ' + nama + '. Status pembayaran akan diubah menjadi Lunas dan status peserta menjadi Aktif Berjalan.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            confirmButtonText: 'Ya, Verifikasi Lunas!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("master/kelas/transaksi") }}/' + id + '/verifikasi',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil Diverifikasi!',
                            text: res.message,
                            timer: 1800,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Gagal memverifikasi transaksi.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: msg
                        });
                    }
                });
            }
        });
    }

    function selesaikanPelatihanPeserta(id, nama) {
        Swal.fire({
            title: 'Tandai Selesai?',
            text: 'Tandai bahwa peserta ' + nama + ' telah menyelesaikan seluruh sesi kelas workshop ini.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#4f46e5',
            confirmButtonText: 'Ya, Tandai Selesai!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("master/kelas/transaksi") }}/' + id + '/selesaikan',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil!',
                            text: res.message,
                            timer: 1800,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Gagal menselesaikan peserta.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: msg
                        });
                    }
                });
            }
        });
    }

    function batalkanTransaksiPeserta(id, noReg) {
        Swal.fire({
            title: 'Batalkan Pembayaran?',
            text: 'Batalkan transaksi pendaftaran #' + noReg + '?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e11d48',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Tutup'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("master/kelas/transaksi") }}/' + id + '/batalkan',
                    type: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    success: function(res) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Dibatalkan!',
                            text: res.message,
                            timer: 1800,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(err) {
                        let msg = err.responseJSON && err.responseJSON.message ? err.responseJSON.message : 'Gagal membatalkan transaksi.';
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: msg
                        });
                    }
                });
            }
        });
    }
</script>
@endpush
