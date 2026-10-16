@extends('layouts.app')

@section('title', 'Invoice ' . $transaksi->nomor_transaksi . ' - Aether')
@section('page_title', 'Detail Invoice Program')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-12">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('bimbingan.invoices.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-[#1768B5] transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Rekap Invoice</span>
        </a>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('bimbingan.invoices.print', ['id' => $transaksi->id, 'jenis' => $transaksi->jenis]) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-[#102654] bg-white border border-gray-300 hover:bg-gray-50 shadow-xs transition">
                <i data-lucide="printer" class="w-3.5 h-3.5"></i>
                <span>Cetak Invoice</span>
            </a>
            <a href="{{ route('bimbingan.pembayaran.index', ['search' => $transaksi->nomor_transaksi, 'jenis' => $transaksi->jenis]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-[#1768B5] bg-sky-50 border border-sky-200 hover:bg-sky-100 shadow-xs transition">
                <i data-lucide="credit-card" class="w-3.5 h-3.5"></i>
                <span>Data Pembayaran</span>
            </a>

            @if(!$isRole99)
                @if($transaksi->status_pembayaran !== 'Lunas' && $transaksi->status_pembayaran !== 'Dibatalkan')
                <button type="button" onclick="verifikasiPembayaran({{ $transaksi->id }}, '{{ $transaksi->nomor_transaksi }}', '{{ e($transaksi->nama_pemesan) }}', '{{ $transaksi->jenis }}')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition" title="Verifikasi Pembayaran & Aktifkan Program">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                    <span>Verifikasi Lunas</span>
                </button>
                @endif
            @endif
        </div>
    </div>

    <!-- Invoice Details Card -->
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
        <!-- Invoice Header Bar -->
        <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-sky-50/50 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <!-- Category Badge -->
                    @if($transaksi->jenis === 'bimbingan')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-sky-100 text-sky-800 border border-sky-200">
                            <i data-lucide="graduation-cap" class="w-3 h-3"></i>
                            Bimbingan
                        </span>
                    @elseif($transaksi->jenis === 'olahdata')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            <i data-lucide="bar-chart-2" class="w-3 h-3"></i>
                            Olah Data
                        </span>
                    @elseif($transaksi->jenis === 'pelatihan')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200">
                            <i data-lucide="award" class="w-3 h-3"></i>
                            Kelas &amp; Workshop
                        </span>
                    @endif

                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Invoice Tagihan</span>

                    @if($transaksi->status_pembayaran === 'Lunas')
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                            <i data-lucide="check" class="w-3 h-3"></i>
                            LUNAS
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                            <i data-lucide="clock" class="w-3 h-3"></i>
                            {{ strtoupper($transaksi->status_pembayaran) }}
                        </span>
                    @endif
                </div>
                <h2 class="text-xl font-black text-gray-900 font-mono mt-1">{{ $transaksi->nomor_transaksi }}</h2>
            </div>
            <div class="text-left sm:text-right text-xs">
                <span class="text-gray-400 font-medium block">Tanggal Penerbitan:</span>
                <span class="font-bold text-gray-700">
                    {{ $transaksi->tanggal_pembelian ? $transaksi->tanggal_pembelian->translatedFormat('d F Y, H:i') : ($transaksi->created_at ? $transaksi->created_at->translatedFormat('d F Y, H:i') : '-') }} WIB
                </span>
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- Customer & Payment Box -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-4 rounded-2xl border border-gray-200 bg-gray-50/60 space-y-1.5 text-xs">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Ditagihkan Kepada:</span>
                    <p class="text-sm font-bold text-gray-900">{{ $transaksi->nama_pemesan ?: ($transaksi->user->nm_user ?? 'Mahasiswa') }}</p>
                    <p class="text-gray-600 flex items-center gap-1.5">
                        <i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400"></i>
                        <span>{{ $transaksi->email_pemesan ?: ($transaksi->user->email ?? '-') }}</span>
                    </p>
                    <p class="text-gray-600 flex items-center gap-1.5">
                        <i data-lucide="phone" class="w-3.5 h-3.5 text-gray-400"></i>
                        <span class="font-mono">{{ $transaksi->telepon_pemesan ?: '-' }}</span>
                    </p>
                    @if($transaksi->universitas)
                    <p class="text-[#1768B5] font-semibold flex items-center gap-1.5 pt-1 border-t border-gray-200/60">
                        <i data-lucide="building-2" class="w-3.5 h-3.5 text-[#1768B5]"></i>
                        <span>{{ $transaksi->universitas }}</span>
                    </p>
                    @endif
                    @if($transaksi->judul_skripsi)
                    <p class="text-gray-500 italic text-[11px] pt-1">
                        "{{ $transaksi->judul_skripsi }}"
                    </p>
                    @endif
                </div>

                <div class="p-4 rounded-2xl border border-sky-100 bg-sky-50/60 space-y-2 text-xs flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-sky-800 block mb-1">Metode &amp; Status Pembayaran:</span>
                        <p class="text-sm font-bold text-gray-900">{{ $transaksi->metode_pembayaran ?: 'Virtual Account' }}</p>
                        @if($transaksi->kode_pembayaran)
                        <div class="mt-1 flex items-center justify-between bg-white px-2.5 py-1.5 rounded-lg border border-sky-200">
                            <span class="text-[11px] text-gray-500">Kode Bayar:</span>
                            <span class="font-mono font-bold text-gray-800 text-xs">{{ $transaksi->kode_pembayaran }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="pt-2 border-t border-sky-200/60 flex items-center justify-between">
                        <span class="text-[11px] text-sky-900 font-medium">Status Program / Layanan:</span>
                        <span class="font-bold {{ in_array($transaksi->status, ['Aktif', 'Aktif Berjalan', 'Proses Running Data']) ? 'text-emerald-700' : 'text-amber-700' }}">
                            {{ $transaksi->status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Program Item Summary -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">Rincian Paket &amp; Layanan</h3>
                <div class="rounded-2xl border border-gray-200 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="py-3 px-4">Deskripsi Layanan</th>
                                <th class="py-3 px-4 text-center">Kuantitas</th>
                                <th class="py-3 px-4 text-right">Harga Satuan</th>
                                <th class="py-3 px-4 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-4 px-4">
                                    <div class="font-bold text-gray-900 text-sm">{{ $transaksi->nama_program }}</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">
                                        {{ $transaksi->detail_layanan }}
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-center font-bold text-gray-700">
                                    {{ $transaksi->total_sesi }}x
                                </td>
                                <td class="py-4 px-4 text-right font-medium text-gray-700">
                                    Rp {{ number_format($transaksi->biaya, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-right font-bold text-gray-900">
                                    Rp {{ number_format($transaksi->biaya, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4 text-gray-600" colspan="3">
                                    Biaya Administrasi &amp; Pemrosesan Sistem
                                </td>
                                <td class="py-3 px-4 text-right font-medium text-gray-700">
                                    Rp {{ number_format($transaksi->admin_fee ?: 5000, 0, ',', '.') }}
                                </td>
                            </tr>
                            <tr class="bg-gray-50 font-black text-sm">
                                <td class="py-4 px-4 text-gray-900 uppercase tracking-wide" colspan="3">
                                    Total Tagihan Akhir
                                </td>
                                <td class="py-4 px-4 text-right text-gray-900 text-base">
                                    Rp {{ number_format($transaksi->total_bayar ?: ($transaksi->biaya + 5000), 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Notes & Attached Proof -->
            @if($transaksi->catatan || $transaksi->bukti_pembayaran_url)
            <div class="p-4 rounded-2xl border border-gray-200 bg-gray-50/50 space-y-2 text-xs">
                @if($transaksi->catatan)
                <div>
                    <span class="font-bold text-gray-700 block mb-0.5">Catatan Transaksi:</span>
                    <p class="text-gray-600">{{ $transaksi->catatan }}</p>
                </div>
                @endif
                @if($transaksi->bukti_pembayaran_url)
                <div class="pt-2 {{ $transaksi->catatan ? 'border-t border-gray-200' : '' }}">
                    <span class="font-bold text-gray-700 block mb-1">Bukti Transfer Terlampir:</span>
                    <a href="{{ $transaksi->bukti_pembayaran_url }}" target="_blank" class="inline-flex items-center gap-1.5 text-[#1768B5] font-bold hover:underline">
                        <i data-lucide="file-check" class="w-3.5 h-3.5 text-emerald-600"></i>
                        <span>Lihat Dokumen / Foto Bukti Transfer</span>
                    </a>
                </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@if(!$isRole99)
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) {
            lucide.createIcons();
        }
    });

    function verifikasiPembayaran(transaksiId, nomorTrx, namaUser, jenis) {
        if (!confirm('Verifikasi bukti transfer transaksi #' + nomorTrx + ' untuk ' + namaUser + '? Status pembayaran akan menjadi LUNAS dan program layanan akan otomatis AKTIF.')) return;

        $.ajax({
            url: "{{ url('bimbingan/pembayaran') }}/" + transaksiId + "/status",
            type: 'POST',
            data: {
                status_pembayaran: 'Lunas',
                jenis: jenis || 'bimbingan',
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Terverifikasi!', text: res.message, timer: 1800, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    alert(res.message);
                    location.reload();
                }
            },
            error: function() {
                alert('Gagal memverifikasi pembayaran.');
            }
        });
    }
</script>
@endpush
@endif
