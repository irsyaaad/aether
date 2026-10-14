@extends('layouts.app')

@section('title', 'Daftar Bukti Bayar User - Aether')
@section('page_title', 'Bukti Bayar User')

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] rounded-2xl p-6 text-white shadow-lg flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 text-[#F6C75A] text-xs font-bold mb-2 border border-white/15">
                <i data-lucide="image" class="w-3.5 h-3.5"></i>
                Bukti Pembayaran User
            </div>
            <h1 class="text-2xl font-black">Daftar Bukti Bayar User</h1>
            <p class="text-sm text-sky-100 mt-1 max-w-2xl leading-relaxed">
                Daftar bukti transfer dan pembayaran yang diunggah oleh user yang membeli program bimbingan untuk diverifikasi dan dikonfirmasi keaktifannya.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 shrink-0">
            <a href="{{ route('bimbingan.invoices.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-white/15 hover:bg-white/25 border border-white/20 transition backdrop-blur-xs shadow-xs">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                <span>Rekap Tagihan (Invoice)</span>
            </a>
            <a href="{{ route('bimbingan.programs') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold text-[#102654] bg-[#F6C75A] hover:bg-amber-300 shadow-md transition">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                <span>Beli Program</span>
            </a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Bukti Diupload -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between text-sky-600 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Bukti Diupload</span>
                <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center">
                    <i data-lucide="image" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-gray-900">{{ number_format($summary['total_bukti_uploaded']) }}</div>
            <span class="text-[11px] text-gray-400">Total bukti bayar masuk</span>
        </div>

        <!-- Terverifikasi Lunas -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between text-emerald-600 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Terverifikasi (Lunas)</span>
                <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i data-lucide="check-circle-2" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-emerald-600">Rp {{ number_format($summary['total_nominal_masuk'], 0, ',', '.') }}</div>
            <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                <span>Bukti Sah:</span>
                <span class="font-bold text-emerald-700">{{ number_format($summary['total_lunas_count']) }} Transaksi</span>
            </div>
        </div>

        <!-- Menunggu Verifikasi Admin -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between text-amber-500 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Menunggu Verifikasi</span>
                <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-600">{{ number_format($summary['total_menunggu_verifikasi_count']) }}</div>
            <div class="mt-1 flex items-center justify-between text-xs text-gray-500">
                <span>Perlu Ditinjau:</span>
                <span class="font-bold text-amber-700">Rp {{ number_format($summary['total_menunggu_verifikasi_nominal'], 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Bukti Ditolak -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs">
            <div class="flex items-center justify-between text-rose-600 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Bukti Ditolak</span>
                <div class="w-8 h-8 rounded-lg bg-rose-50 text-rose-600 flex items-center justify-center">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-rose-600">{{ number_format($summary['total_ditolak_count']) }}</div>
            <span class="text-[11px] text-gray-400">Tidak valid / ditolak</span>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-xs">
        <form method="GET" action="{{ route('bimbingan.pembayaran.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3.5 items-end">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-xs font-bold text-gray-700 mb-1">Cari Bukti Bayar</label>
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="No. Transaksi, Nama Mahasiswa, Kode Bayar, WA..."
                           class="w-full pl-9 pr-3.5 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition">
                </div>
            </div>

            <!-- Status Pembayaran / Verifikasi -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Status Verifikasi</label>
                <select name="status_pembayaran" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition">
                    <option value="Semua">Semua Status</option>
                    <option value="Menunggu Verifikasi" {{ request('status_pembayaran') === 'Menunggu Verifikasi' ? 'selected' : '' }}>Menunggu Verifikasi</option>
                    <option value="Lunas" {{ request('status_pembayaran') === 'Lunas' ? 'selected' : '' }}>Lunas / Terverifikasi</option>
                    <option value="Ditolak" {{ request('status_pembayaran') === 'Ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Metode Pembayaran -->
            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Metode Bayar</label>
                <select name="metode_pembayaran" class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none transition">
                    <option value="Semua">Semua Metode</option>
                    @foreach($metodeList as $m)
                        <option value="{{ $m }}" {{ request('metode_pembayaran') === $m ? 'selected' : '' }}>{{ $m }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3.5 bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold rounded-xl shadow-xs transition flex items-center justify-center gap-1.5">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Filter</span>
                </button>
                <a href="{{ route('bimbingan.pembayaran.index') }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition flex items-center justify-center" title="Reset Filter">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-sm font-bold text-gray-900">Daftar Bukti Bayar User</h3>
                <p class="text-xs text-gray-500">Menampilkan {{ $pembayarans->firstItem() ?? 0 }} - {{ $pembayarans->lastItem() ?? 0 }} dari total {{ $pembayarans->total() }} bukti pembayaran yang diunggah</p>
            </div>
            @if(request()->anyFilled(['search', 'status_pembayaran', 'metode_pembayaran']))
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200 text-xs font-medium">
                <i data-lucide="info" class="w-3.5 h-3.5"></i>
                Filter Aktif Diterapkan
            </span>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3.5 px-4 w-12 text-center">No</th>
                        <th class="py-3.5 px-4">No. Transaksi &amp; Waktu</th>
                        <th class="py-3.5 px-4">Mahasiswa / Pemesan</th>
                        <th class="py-3.5 px-4">Program yang Dibeli</th>
                        <th class="py-3.5 px-4">Metode Bayar</th>
                        <th class="py-3.5 px-4 text-right">Nominal Bayar</th>
                        <th class="py-3.5 px-4 text-center">Bukti Bayar User</th>
                        <th class="py-3.5 px-4 text-center">Status Verifikasi</th>
                        <th class="py-3.5 px-4 text-center w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pembayarans as $index => $item)
                    <tr class="hover:bg-sky-50/40 transition">
                        <td class="py-3.5 px-4 text-center text-gray-400 font-medium">
                            {{ $pembayarans->firstItem() + $index }}
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-mono font-bold text-gray-900 block">{{ $item->nomor_transaksi }}</span>
                            <span class="text-[11px] text-gray-500">
                                {{ $item->tanggal_pembelian ? $item->tanggal_pembelian->translatedFormat('d M Y, H:i') : ($item->updated_at ? $item->updated_at->translatedFormat('d M Y, H:i') : '-') }} WIB
                            </span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-gray-900">{{ $item->nama_pemesan ?: ($item->user->nm_user ?? 'User') }}</div>
                            <div class="text-[11px] text-gray-500 font-mono">{{ $item->telepon_pemesan ?: ($item->email_pemesan ?: '-') }}</div>
                            @if($item->universitas)
                            <div class="text-[10px] text-sky-700 font-medium mt-0.5">{{ $item->universitas }}</div>
                            @endif
                        </td>
                        <td class="py-3.5 px-4">
                            <span class="font-semibold text-gray-800 block">{{ $item->nama_program }}</span>
                            <span class="text-[10px] text-gray-500">{{ $item->total_sesi }} Sesi Bimbingan</span>
                        </td>
                        <td class="py-3.5 px-4">
                            <div class="font-semibold text-gray-800">{{ $item->metode_pembayaran ?: '-' }}</div>
                            @if($item->kode_pembayaran)
                            <div class="text-[10px] font-mono text-gray-500 tracking-wider">{{ $item->kode_pembayaran }}</div>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-right font-black text-gray-900 text-sm">
                            Rp {{ number_format($item->total_bayar ?: ($item->biaya + 5000), 0, ',', '.') }}
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($item->bukti_pembayaran_url)
                                <button type="button"
                                        onclick="showProofModal('{{ $item->bukti_pembayaran_url }}', '{{ $item->nomor_transaksi }}', '{{ addslashes($item->nama_pemesan ?? '') }}', '{{ $item->formatted_biaya }}', '{{ $item->metode_pembayaran }}')"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-[11px] font-bold bg-sky-100 text-sky-800 hover:bg-sky-200 transition border border-sky-300 shadow-xs">
                                    <i data-lucide="image" class="w-3.5 h-3.5 text-[#1768B5]"></i>
                                    <span>Lihat Foto Bukti</span>
                                </button>
                            @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] text-gray-400 bg-gray-100">
                                    Belum Ada
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            @if($item->status_pembayaran === 'Lunas')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                    <i data-lucide="check" class="w-3 h-3"></i>
                                    Terverifikasi (Lunas)
                                </span>
                            @elseif($item->status_pembayaran === 'Menunggu Verifikasi')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                    <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                                    Menunggu Verifikasi
                                </span>
                            @elseif($item->status_pembayaran === 'Ditolak')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                    <i data-lucide="x" class="w-3 h-3"></i>
                                    Ditolak
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-800 border border-gray-200">
                                    {{ $item->status_pembayaran }}
                                </span>
                            @endif
                        </td>
                        <td class="py-3.5 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('bimbingan.invoices.show', $item->id) }}" class="p-1.5 rounded-lg text-gray-600 hover:text-[#1768B5] hover:bg-sky-50 transition" title="Lihat Tagihan / Invoice">
                                    <i data-lucide="receipt" class="w-4 h-4"></i>
                                </a>

                                @if(!$isRole99)
                                <button type="button"
                                        onclick="openStatusModal({{ $item->id }}, '{{ $item->nomor_transaksi }}', '{{ $item->status_pembayaran }}', '{{ addslashes($item->catatan ?? '') }}')"
                                        class="p-1.5 rounded-lg text-gray-600 hover:text-emerald-700 hover:bg-emerald-50 transition" title="Verifikasi / Update Status Bukti Bayar">
                                    <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="py-12 text-center text-gray-500">
                            <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-3">
                                <i data-lucide="image" class="w-6 h-6"></i>
                            </div>
                            <p class="font-bold text-gray-700 text-sm">Belum ada bukti pembayaran yang diupload user</p>
                            <p class="text-xs text-gray-400 mt-0.5">Daftar ini khusus merekap bukti transfer pembayaran yang telah diupload oleh pembeli.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pembayarans->hasPages())
        <div class="px-6 py-4 border-t border-gray-200 bg-gray-50/50">
            {{ $pembayarans->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal: Preview Bukti Transfer (Lightbox) -->
<div id="proofModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl space-y-0 animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h4 class="text-sm font-bold text-gray-900" id="proofModalTitle">Bukti Pembayaran User</h4>
                <p class="text-[11px] text-gray-500" id="proofModalSub"></p>
            </div>
            <button onclick="closeProofModal()" class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <div class="p-4 bg-gray-900 flex items-center justify-center min-h-[300px] max-h-[70vh] overflow-auto">
            <img id="proofModalImg" src="" alt="Bukti Transfer" class="max-w-full max-h-[65vh] object-contain rounded-lg shadow-md">
        </div>
        <div class="p-4 bg-white border-t border-gray-100 flex items-center justify-between">
            <a id="proofDownloadBtn" href="" target="_blank" download class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold transition">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Unduh Gambar Bukti</span>
            </a>
            <button onclick="closeProofModal()" class="px-4 py-2 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<!-- Modal: Verifikasi Bukti Pembayaran (Admin Only) -->
@if(!$isRole99)
<div id="statusModal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full overflow-hidden shadow-2xl animate-in fade-in zoom-in duration-200">
        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
            <h4 class="text-sm font-bold text-gray-900">Verifikasi Bukti Pembayaran</h4>
            <button onclick="closeStatusModal()" class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 text-gray-600 flex items-center justify-center transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
        <form id="statusForm" method="POST" action="" class="p-6 space-y-4">
            @csrf
            <div>
                <span class="text-xs text-gray-500 font-semibold block">Nomor Transaksi:</span>
                <span class="text-sm font-mono font-black text-gray-900 block" id="statusModalNomorTrx"></span>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Keputusan Verifikasi *</label>
                <select name="status_pembayaran" id="statusSelect" required class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none">
                    <option value="Lunas">Setujui / Lunas (Otomatis Aktifkan Program)</option>
                    <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                    <option value="Ditolak">Tolak Bukti Bayar (Minta User Upload Ulang)</option>
                    <option value="Menunggu Pembayaran">Kembalikan ke Menunggu Pembayaran</option>
                </select>
                <p class="text-[11px] text-gray-400 mt-1">Status <strong>Lunas</strong> akan memverifikasi transfer dan mengaktifkan kuota sesi bimbingan user.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 mb-1">Catatan Verifikasi / Alasan Penolakan</label>
                <textarea name="catatan" id="catatanInput" rows="2" placeholder="Contoh: Transfer BCA telah diverifikasi masuk ke rekening..." class="w-full px-3 py-2 text-xs border border-gray-300 rounded-xl focus:ring-2 focus:ring-[#1768B5] focus:border-[#1768B5] outline-none resize-none"></textarea>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-end gap-2">
                <button type="button" onclick="closeStatusModal()" class="px-4 py-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold shadow-xs transition">
                    Simpan Keputusan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function showProofModal(imgUrl, noTrx, name, nominal, metode) {
        document.getElementById('proofModalImg').src = imgUrl;
        document.getElementById('proofDownloadBtn').href = imgUrl;
        document.getElementById('proofModalTitle').innerText = 'Bukti Pembayaran - ' + noTrx;
        document.getElementById('proofModalSub').innerText = 'Pemesan: ' + (name || 'Mahasiswa') + (metode ? ' • ' + metode : '');
        document.getElementById('proofModal').classList.remove('hidden');
    }

    function closeProofModal() {
        document.getElementById('proofModal').classList.add('hidden');
    }

    function openStatusModal(id, noTrx, currentStatus, currentCatatan) {
        var form = document.getElementById('statusForm');
        form.action = "{{ url('bimbingan/pembayaran') }}/" + id + "/status";
        document.getElementById('statusModalNomorTrx').innerText = noTrx;
        document.getElementById('statusSelect').value = currentStatus;
        document.getElementById('catatanInput').value = currentCatatan || '';
        document.getElementById('statusModal').classList.remove('hidden');
    }

    function closeStatusModal() {
        document.getElementById('statusModal').classList.add('hidden');
    }
</script>
@endsection
