@extends('layouts.app')

@section('title', 'Invoice ' . $transaksi->nomor_transaksi . ' - ' . config('app.name'))
@section('page_title', 'Rincian Pembayaran Program')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-12">
    <!-- Status Banner -->
    @if($transaksi->status_pembayaran === 'Lunas')
    <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-[#1768B5] rounded-3xl p-6 sm:p-8 text-white shadow-md text-center space-y-3">
        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-xs flex items-center justify-center mx-auto text-white shadow-inner">
            <i data-lucide="check-circle-2" class="w-8 h-8"></i>
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-white text-[11px] font-bold tracking-wider uppercase backdrop-blur-xs">
            <span class="w-2 h-2 rounded-full bg-emerald-300 animate-pulse"></span>
            Pembayaran Terverifikasi &amp; Program Aktif
        </div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
            Pemesanan &amp; Pembayaran Sukses!
        </h1>
        <p class="text-xs sm:text-sm text-white/90 max-w-xl mx-auto leading-relaxed">
            Paket program bimbingan Anda telah aktif. Anda dapat langsung terhubung dengan mentor dan mengajukan sesi konsultasi.
        </p>
    </div>
    @elseif($transaksi->status_pembayaran === 'Menunggu Verifikasi')
    <div class="bg-gradient-to-r from-sky-600 via-indigo-600 to-[#1768B5] rounded-3xl p-6 sm:p-8 text-white shadow-md text-center space-y-3">
        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-xs flex items-center justify-center mx-auto text-white shadow-inner">
            <i data-lucide="clock" class="w-8 h-8"></i>
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 text-[11px] font-bold tracking-wider uppercase backdrop-blur-xs border border-amber-300/30">
            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
            Bukti Pembayaran Sedang Diverifikasi Admin
        </div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
            Bukti Transfer Berhasil Diunggah
        </h1>
        <p class="text-xs sm:text-sm text-white/90 max-w-xl mx-auto leading-relaxed">
            Terima kasih! Bukti pembayaran Anda telah tersimpan dan sedang dalam antrean verifikasi tim admin. Program bimbingan akan aktif segera setelah diverifikasi.
        </p>
    </div>
    @else
    <div class="bg-gradient-to-r from-amber-600 to-[#1768B5] rounded-3xl p-6 sm:p-8 text-white shadow-md text-center space-y-3">
        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-xs flex items-center justify-center mx-auto text-white shadow-inner">
            <i data-lucide="alert-circle" class="w-8 h-8"></i>
        </div>
        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-white text-[11px] font-bold tracking-wider uppercase backdrop-blur-xs">
            Status: {{ $transaksi->status_pembayaran }}
        </div>
        <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white">
            Menunggu Penyelesaian Pembayaran
        </h1>
    </div>
    @endif

    <!-- Invoice Details Card -->
    <div class="bg-white rounded-3xl border border-gray-200 shadow-xs overflow-hidden">
        <!-- Invoice Header Bar -->
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
            <div>
                <span class="text-gray-400 font-semibold">Nomor Transaksi:</span>
                <span class="font-mono font-bold text-gray-800 ml-1">{{ $transaksi->nomor_transaksi }}</span>
            </div>
            <div>
                <span class="text-gray-400 font-semibold">Waktu Pemesanan:</span>
                <span class="font-semibold text-gray-700 ml-1">{{ $transaksi->tanggal_pembelian ? $transaksi->tanggal_pembelian->translatedFormat('d F Y, H:i') : now()->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- Payment Instruction / VA Box -->
            <div class="rounded-2xl border border-sky-100 bg-sky-50/70 p-5 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold uppercase tracking-wider text-sky-800">Metode &amp; Kode Pembayaran</span>
                    @if($transaksi->status_pembayaran === 'Lunas')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700">
                        <i data-lucide="check" class="w-3 h-3"></i>
                        Lunas
                    </span>
                    @elseif($transaksi->status_pembayaran === 'Menunggu Verifikasi')
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                        <i data-lucide="clock" class="w-3 h-3"></i>
                        Menunggu Verifikasi Admin
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700">
                        {{ $transaksi->status_pembayaran }}
                    </span>
                    @endif
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pt-1">
                    <div>
                        <p class="text-sm font-bold text-gray-800">{{ $transaksi->metode_pembayaran ?? 'Virtual Account' }}</p>
                        <p class="text-xs text-gray-500 font-mono mt-0.5 tracking-wider">{{ $transaksi->kode_pembayaran ?? '80777123456789' }}</p>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[11px] text-gray-500">Total Tagihan:</span>
                        <p class="text-xl font-black text-gray-900">
                            Rp {{ number_format($transaksi->total_bayar ?: ($transaksi->biaya + 5000), 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Program Summary -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Rincian Paket Program</h3>
                <div class="rounded-2xl border border-gray-100 bg-gray-50/80 p-4 space-y-3">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h4 class="text-sm font-bold text-gray-900">{{ $transaksi->nama_program }}</h4>
                            <p class="text-xs text-gray-500 mt-0.5">
                                {{ $transaksi->total_sesi }}x Sesi Bimbingan Intensif 1-on-1 • Sisa {{ $transaksi->sisa_sesi }} Sesi Aktif
                            </p>
                        </div>
                        <span class="text-xs font-bold text-gray-900 shrink-0">
                            Rp {{ number_format($transaksi->biaya, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="pt-2.5 border-t border-gray-200/60 flex items-center justify-between text-xs text-gray-500">
                        <span>Biaya Administrasi &amp; Layanan Sistem</span>
                        <span class="font-semibold text-gray-700">Rp {{ number_format($transaksi->admin_fee ?: 5000, 0, ',', '.') }}</span>
                    </div>

                    <div class="pt-2 border-t border-gray-200 flex items-center justify-between text-xs">
                        <span class="font-bold text-gray-800">Total Pembayaran</span>
                        <span class="font-black text-gray-900 text-sm">
                            Rp {{ number_format($transaksi->total_bayar ?: ($transaksi->biaya + 5000), 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Customer & WhatsApp Coordinator -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
                <div class="p-4 rounded-2xl border border-gray-100 bg-gray-50/50 space-y-2 text-xs">
                    <span class="text-gray-400 font-semibold block text-[11px] uppercase tracking-wider">Data Pemesan</span>
                    <p class="font-bold text-gray-800">{{ $transaksi->nama_pemesan ?? '-' }}</p>
                    <p class="text-gray-500">{{ $transaksi->email_pemesan ?? '-' }}</p>
                    <p class="text-gray-500 font-mono">{{ $transaksi->telepon_pemesan ?? '-' }}</p>
                    @if($transaksi->universitas)
                    <p class="text-sky-700 font-medium">Univ: {{ $transaksi->universitas }}</p>
                    @endif
                    @if($transaksi->bukti_pembayaran_url)
                    <div class="pt-2 border-t border-gray-200/60">
                        <span class="text-[10px] text-gray-400 font-bold uppercase block mb-1">Bukti Transfer:</span>
                        <a href="{{ $transaksi->bukti_pembayaran_url }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs text-[#1768B5] font-bold hover:underline">
                            <i data-lucide="file-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                            <span>Lihat Bukti Pembayaran Terlampir</span>
                        </a>
                    </div>
                    @endif
                </div>

                <div class="p-4 rounded-2xl border border-emerald-100 bg-emerald-50/50 space-y-2 text-xs flex flex-col justify-between">
                    <div>
                        <span class="text-emerald-700 font-bold block text-[11px] uppercase tracking-wider flex items-center gap-1">
                            <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                            Grup WhatsApp &amp; Mentor
                        </span>
                        <p class="text-[11px] text-emerald-800/80 mt-1">
                            Bergabunglah ke grup WhatsApp resmi bimbingan untuk koordinasi jadwal, share draft skripsi, dan informasi seminar.
                        </p>
                    </div>
                    <a href="https://chat.whatsapp.com" target="_blank" class="inline-flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs">
                        <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                        <span>Gabung Grup WhatsApp Bimbingan</span>
                    </a>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center gap-3">
                @if($transaksi->status === 'Aktif')
                <a href="{{ route('bimbingan.create', ['program_id' => $transaksi->program_id]) }}" class="w-full sm:flex-1 py-3 px-5 rounded-xl bg-gradient-to-r from-[#2B8BD1] to-[#1768B5] hover:from-[#1768B5] hover:to-[#102654] text-white font-bold text-xs text-center shadow-xs flex items-center justify-center gap-2 transition">
                    <i data-lucide="calendar-plus" class="w-4 h-4"></i>
                    <span>Mulai Ajukan Sesi Bimbingan Pertama</span>
                </a>
                @elseif($transaksi->status === 'Menunggu Verifikasi')
                <div class="w-full sm:flex-1 py-3 px-5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 font-bold text-xs text-center flex items-center justify-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-amber-600"></i>
                    <span>Menunggu verifikasi admin untuk mengaktifkan pengajuan bimbingan</span>
                </div>
                @endif
                <a href="{{ route('bimbingan.invoices.print', $transaksi->id) }}" target="_blank" class="w-full sm:w-auto px-4 py-3 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 transition text-center flex items-center justify-center gap-1.5">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    <span>Cetak Faktur</span>
                </a>
                <a href="{{ route('bimbingan.invoices.index') }}" class="w-full sm:w-auto px-4 py-3 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-50 transition text-center flex items-center justify-center gap-1.5">
                    <i data-lucide="receipt" class="w-4 h-4"></i>
                    <span>Rekap Invoice</span>
                </a>
                <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-4 py-3 rounded-xl bg-gray-100 text-xs font-bold text-gray-700 hover:bg-gray-200 transition text-center">
                    Dashboard
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
