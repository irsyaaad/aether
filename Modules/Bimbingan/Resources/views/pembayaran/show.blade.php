@extends('layouts.app')

@section('title', 'Detail Pembayaran ' . $transaksi->nomor_transaksi . ' - Aether')
@section('page_title', 'Detail Pembayaran Program')

@section('content')
<div class="max-w-3xl mx-auto space-y-6 pb-12">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('bimbingan.pembayaran.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-600 hover:text-[#1768B5] transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke List Pembayaran</span>
        </a>
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('bimbingan.invoices.show', ['id' => $transaksi->id, 'jenis' => $transaksi->jenis]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-[#1768B5] bg-white border border-[#1768B5]/30 hover:bg-sky-50 shadow-xs transition">
                <i data-lucide="receipt" class="w-3.5 h-3.5"></i>
                <span>Lihat Invoice</span>
            </a>

            @if(!$isRole99)
                @if($transaksi->status_pembayaran !== 'Lunas')
                <button type="button" onclick="verifikasiPembayaran({{ $transaksi->id }}, '{{ $transaksi->nomor_transaksi }}', '{{ e($transaksi->nama_pemesan) }}', '{{ $transaksi->jenis }}')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xs transition" title="Verifikasi Pembayaran & Aktifkan Program">
                    <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                    <span>Verifikasi Lunas</span>
                </button>
                @endif

                <button type="button" onclick="document.getElementById('statusModal').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 shadow-xs transition" title="Ubah Status Manual">
                    <i data-lucide="edit-3" class="w-3.5 h-3.5"></i>
                    <span>Ubah Status</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Payment Details Card -->
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-6 py-5 bg-gradient-to-r from-gray-50 to-sky-50/50 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap mb-1">
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
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Rincian Pembayaran</span>
                </div>
                <h2 class="text-xl font-black text-gray-900 font-mono">{{ $transaksi->nomor_transaksi }}</h2>
            </div>
            <div class="text-left sm:text-right">
                @if($transaksi->status_pembayaran === 'Lunas')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        Terverifikasi (Lunas)
                    </span>
                @elseif($transaksi->status_pembayaran === 'Menunggu Verifikasi')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                        Menunggu Verifikasi
                    </span>
                @elseif($transaksi->status_pembayaran === 'Ditolak')
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800 border border-rose-200">
                        <i data-lucide="x" class="w-3.5 h-3.5"></i>
                        Ditolak
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800 border border-gray-200">
                        {{ $transaksi->status_pembayaran }}
                    </span>
                @endif
            </div>
        </div>

        <div class="p-6 sm:p-8 space-y-6">
            <!-- Summary Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-4 rounded-2xl border border-gray-200 bg-gray-50/60 space-y-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Informasi Pemesan:</span>
                    <p class="text-sm font-bold text-gray-900">{{ $transaksi->nama_pemesan ?: ($transaksi->user->nm_user ?? '-') }}</p>
                    <p class="text-gray-600">{{ $transaksi->email_pemesan ?: ($transaksi->user->email ?? '-') }}</p>
                    <p class="text-gray-600 font-mono">{{ $transaksi->telepon_pemesan ?: '-' }}</p>
                    @if($transaksi->universitas)
                    <p class="text-[#1768B5] font-semibold mt-1">{{ $transaksi->universitas }}</p>
                    @endif
                </div>

                <div class="p-4 rounded-2xl border border-gray-200 bg-gray-50/60 space-y-1.5">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400 block mb-1">Metode &amp; Tagihan:</span>
                    <p class="text-sm font-bold text-gray-900">{{ $transaksi->metode_pembayaran ?: 'Virtual Account' }}</p>
                    @if($transaksi->kode_pembayaran)
                    <p class="text-gray-600 font-mono">Kode Bayar: <span class="font-bold text-gray-800">{{ $transaksi->kode_pembayaran }}</span></p>
                    @endif
                    <div class="pt-2 border-t border-gray-200">
                        <span class="text-gray-500 text-[11px]">Total Pembayaran:</span>
                        <p class="text-lg font-black text-gray-900">Rp {{ number_format($transaksi->total_bayar ?: ($transaksi->biaya + 5000), 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Program Info -->
            <div class="p-4 rounded-2xl border border-sky-100 bg-sky-50/50 space-y-1 text-xs">
                <span class="text-[10px] font-bold uppercase tracking-wider text-sky-800 block mb-1">Paket Program / Layanan:</span>
                <p class="text-sm font-bold text-gray-900">{{ $transaksi->nama_program }}</p>
                <p class="text-gray-600">{{ $transaksi->detail_layanan }} • Status Layanan: <span class="font-bold text-emerald-700">{{ $transaksi->status }}</span></p>
            </div>

            <!-- Proof of Payment Image -->
            @if($transaksi->bukti_pembayaran_url)
            <div class="space-y-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-gray-500">Bukti Pembayaran / Transfer</h4>
                <div class="p-4 rounded-2xl border border-gray-200 bg-gray-900 flex flex-col items-center justify-center gap-3">
                    <img src="{{ $transaksi->bukti_pembayaran_url }}" alt="Bukti Transfer" class="max-h-96 object-contain rounded-lg shadow-md">
                    <a href="{{ $transaksi->bukti_pembayaran_url }}" target="_blank" download class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        <span>Buka / Unduh Bukti Asli</span>
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@if(!$isRole99)
<!-- Modal Update Status -->
<div id="statusModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h4 class="text-sm font-bold text-gray-900">Ubah Status Pembayaran ({{ $transaksi->jenis_label }})</h4>
            <button onclick="document.getElementById('statusModal').classList.add('hidden')" class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('bimbingan.pembayaran.update-status', $transaksi->id) }}" class="p-6 space-y-4">
            @csrf
            <input type="hidden" name="jenis" value="{{ $transaksi->jenis }}">

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Status Pembayaran *</label>
                <select name="status_pembayaran" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1768B5] outline-none">
                    <option value="Lunas" {{ $transaksi->status_pembayaran === 'Lunas' ? 'selected' : '' }}>Lunas (Setujui &amp; Aktifkan Layanan)</option>
                    <option value="Menunggu Verifikasi" {{ $transaksi->status_pembayaran === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Ditolak" {{ $transaksi->status_pembayaran === 'Ditolak' ? 'selected' : '' }}>Ditolak (Minta Upload Ulang)</option>
                    <option value="Menunggu Pembayaran" {{ $transaksi->status_pembayaran === 'Menunggu Pembayaran' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Catatan</label>
                <textarea name="catatan" rows="2" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1768B5] outline-none">{{ $transaksi->catatan }}</textarea>
            </div>
            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('statusModal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-gray-100 text-gray-700 text-xs font-bold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#1768B5] text-white text-xs font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endif
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
