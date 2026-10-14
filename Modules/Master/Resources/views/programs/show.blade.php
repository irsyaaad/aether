@extends('layouts.app')

@section('title', 'Detail Program & Daftar Pembeli - ' . config('app.name'))
@section('page_title', 'Detail Program Bimbingan')

@push('styles')
<style>
    .program-desc-content ul {
        list-style-type: disc !important;
        margin-left: 1.25rem !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
    }
    .program-desc-content ol {
        list-style-type: decimal !important;
        margin-left: 1.25rem !important;
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
    }
    .program-desc-content li {
        margin-bottom: 0.25rem !important;
    }
    .program-desc-content p {
        margin-bottom: 0.5rem !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar & Breadcrumbs -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-sky-50 text-[#1768B5] text-xs font-bold mb-1.5 border border-sky-100">
                <i data-lucide="layers" class="w-3.5 h-3.5"></i>
                Detail Master Program & Mahasiswa
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <h1 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight">{{ $program->nama_program }}</h1>
                @if($program->is_active)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Program Aktif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                        <span class="w-2 h-2 rounded-full bg-gray-400"></span> Program Nonaktif
                    </span>
                @endif
                @if($program->is_populer)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800 border border-amber-200">
                        ⭐ Populer
                    </span>
                @endif
            </div>
            <p class="text-xs text-gray-500 font-mono mt-1">Kode Paket: {{ $program->kode_program }} &bull; Kategori: {{ $program->kategori }} &bull; Jenjang: {{ $program->jenjang ?: 'Semua Jenjang' }}</p>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('master.programs.edit', $program->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border border-gray-300 text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-sky-600 transition">
                <i data-lucide="edit-3" class="w-4 h-4"></i>
                Edit Program
            </a>
            <button onclick="toggleProgramActive({{ $program->id }}, {{ $program->is_active ? 'false' : 'true' }})" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl border text-xs font-bold transition {{ $program->is_active ? 'border-amber-300 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-300 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                <i data-lucide="{{ $program->is_active ? 'pause-circle' : 'play-circle' }}" class="w-4 h-4"></i>
                {{ $program->is_active ? 'Nonaktifkan Program' : 'Aktifkan Program' }}
            </button>
            <a href="{{ route('master.programs.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold hover:bg-black transition">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Metric Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0 border border-sky-100">
                <i data-lucide="users" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Total Pembeli</span>
                <span class="text-xl font-black text-gray-900">{{ number_format($stats['total_pembeli']) }}</span>
                <span class="text-[10px] text-gray-500 block">Mahasiswa terdaftar</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100">
                <i data-lucide="badge-check" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Transaksi Lunas</span>
                <span class="text-xl font-black text-emerald-600">{{ number_format($stats['total_lunas']) }}</span>
                <span class="text-[10px] text-gray-500 block">Pesanan terkonfirmasi</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0 border border-indigo-100">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Total Omset</span>
                <span class="text-lg font-black text-gray-900">Rp {{ number_format($stats['total_omset'], 0, ',', '.') }}</span>
                <span class="text-[10px] text-gray-500 block">Pendapatan program</span>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-xs flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0 border border-amber-100">
                <i data-lucide="calendar-check-2" class="w-6 h-6"></i>
            </div>
            <div>
                <span class="text-[11px] font-bold uppercase tracking-wider text-gray-400 block">Sesi Terpakai / Total</span>
                <span class="text-xl font-black text-gray-900">{{ $stats['sesi_digunakan'] }} <span class="text-sm font-normal text-gray-400">/ {{ $stats['total_sesi'] }}</span></span>
                <span class="text-[10px] text-gray-500 block">{{ $stats['sisa_sesi'] }} sisa kuota bimbingan</span>
            </div>
        </div>
    </div>

    <!-- Program Specs & Facilities Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Banner & Quick Info -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-xs overflow-hidden flex flex-col justify-between">
            <div>
                @if($program->banner_url)
                <div class="h-48 w-full bg-gray-100 relative overflow-hidden border-b border-gray-100">
                    <img src="{{ $program->banner_url }}" alt="{{ $program->nama_program }}" class="w-full h-full object-cover">
                    @if($program->badge)
                    <div class="absolute top-3 right-3">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-[#1768B5] text-white shadow-sm">
                            {{ $program->badge }}
                        </span>
                    </div>
                    @endif
                </div>
                @else
                <div class="h-36 w-full bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] relative flex items-center justify-between p-6 overflow-hidden">
                    <i data-lucide="graduation-cap" class="w-24 h-24 text-white/10 absolute -right-3 -bottom-3"></i>
                    <div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white/20 text-white uppercase tracking-wider">
                            {{ $program->kategori }}
                        </span>
                        <h4 class="text-white font-black text-lg mt-1">{{ $program->nama_program }}</h4>
                    </div>
                </div>
                @endif

                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Tarif / Biaya Standar</span>
                        <span class="text-lg font-black text-gray-900">{{ $program->formatted_biaya }}</span>
                    </div>

                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Durasi & Pertemuan</span>
                        <span class="text-xs font-bold text-gray-800">{{ $program->ringkasan_sesi }}</span>
                    </div>

                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <span class="text-xs text-gray-500">Target Bab / Fokus</span>
                        <span class="text-xs font-bold text-sky-600">{{ $program->target_bab ?: 'Seluruh Bab Skripsi' }}</span>
                    </div>

                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">Status Pembelian</span>
                        <span class="text-xs font-bold {{ $program->is_active ? 'text-emerald-600' : 'text-amber-600' }}">
                            {{ $program->is_active ? 'Terbuka untuk Mahasiswa' : 'Ditutup / Dinonaktifkan' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="p-4 bg-gray-50 border-t border-gray-100 text-center">
                <a href="{{ route('bimbingan.payment', $program->id) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1768B5] hover:underline">
                    <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                    Pratinjau Halaman Checkout Pembayaran
                </a>
            </div>
        </div>

        <!-- Middle & Right: Facilities & Description -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-xs p-6 space-y-6">
            <div>
                <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 mb-3 flex items-center gap-2">
                    <i data-lucide="sparkles" class="w-4 h-4 text-amber-500"></i>
                    Fasilitas & Benefit Program
                </h3>
                @php $fasilitasArr = $program->fasilitas_array; @endphp
                @if(count($fasilitasArr) > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    @foreach($fasilitasArr as $item)
                    <div class="flex items-start gap-2.5 p-3 rounded-xl bg-sky-50/50 border border-sky-100">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-[#1768B5] shrink-0 mt-0.5"></i>
                        <span class="text-xs text-gray-800 font-medium leading-relaxed">{{ $item }}</span>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-gray-400 italic">Belum ada rincian fasilitas terdaftar.</p>
                @endif
            </div>

            <div class="pt-4 border-t border-gray-100">
                <h3 class="text-sm font-black uppercase tracking-wider text-gray-900 mb-2 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4 text-sky-500"></i>
                    Rincian & Deskripsi Lengkap
                </h3>
                @if($program->deskripsi)
                <div class="program-desc-content text-xs text-gray-600 leading-relaxed bg-gray-50 p-4 rounded-xl border border-gray-100">
                    {!! $program->deskripsi !!}
                </div>
                @else
                <p class="text-xs text-gray-400 italic">Tidak ada deskripsi tambahan.</p>
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
                    <h2 class="text-lg font-black text-gray-900 tracking-tight">Daftar Mahasiswa Pembeli Program</h2>
                </div>
                <p class="text-xs text-gray-500 mt-0.5">Kelola data pembeli, rincian pembayaran, verifikasi bukti transfer, batalkan transaksi, atau kelola akses mahasiswa.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-gray-500">Total: {{ $transaksis->count() }} Pendaftar</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="table-pembeli">
                <thead>
                    <tr class="bg-gray-50/75 border-b border-gray-200 text-[11px] font-black uppercase tracking-wider text-gray-500">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4">Nomor Transaksi</th>
                        <th class="py-3 px-4">Mahasiswa / Kontak</th>
                        <th class="py-3 px-4">Informasi Pembayaran</th>
                        <th class="py-3 px-4 text-center">Status Pembayaran</th>
                        <th class="py-3 px-4 text-center">Status Program</th>
                        <th class="py-3 px-4 text-center">Progress Sesi</th>
                        <th class="py-3 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($transaksis as $index => $trx)
                    <tr class="hover:bg-sky-50/30 transition">
                        <!-- No -->
                        <td class="py-3.5 px-4 text-center text-gray-400 font-mono text-[11px]">
                            {{ $index + 1 }}
                        </td>

                        <!-- Nomor Transaksi -->
                        <td class="py-3.5 px-4">
                            <span class="font-mono font-bold text-[#1768B5] text-xs">{{ $trx->nomor_transaksi }}</span>
                            <div class="text-[11px] text-gray-400 mt-0.5">
                                {{ $trx->created_at ? $trx->created_at->format('d M Y, H:i') : '-' }}
                            </div>
                        </td>

                        <!-- Info Mahasiswa -->
                        <td class="py-3.5 px-4">
                            <div class="font-bold text-gray-900 text-sm leading-tight">{{ $trx->nama_pemesan }}</div>
                            <div class="text-[11px] text-gray-500 mt-0.5 flex flex-col gap-0.5">
                                <span><i data-lucide="mail" class="inline w-3 h-3 text-gray-400"></i> {{ $trx->email_pemesan }}</span>
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
                                <a href="{{ $trx->bukti_pembayaran_url }}" target="_blank" class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-sky-50 text-[#1768B5] border border-sky-200 hover:bg-sky-100 transition">
                                    <i data-lucide="image" class="w-3 h-3"></i>
                                    Lihat Bukti Bayar
                                </a>
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

                        <!-- Status Program / User -->
                        <td class="py-3.5 px-4 text-center">
                            @if($trx->status === 'Aktif')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    Aktif
                                </span>
                            @elseif($trx->status === 'Selesai')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-800 border border-indigo-200">
                                    <i data-lucide="check-check" class="w-3 h-3"></i> Selesai
                                </span>
                            @elseif($trx->status === 'Nonaktif')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-gray-100 text-gray-700">
                                    Nonaktif
                                </span>
                            @elseif($trx->status === 'Menunggu Verifikasi')
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                    Verifikasi Admin
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                    {{ $trx->status }}
                                </span>
                            @endif
                        </td>

                        <!-- Progress Sesi -->
                        <td class="py-3.5 px-4 text-center">
                            <span class="font-bold text-gray-900">{{ $trx->sesi_digunakan }}</span> / {{ $trx->total_sesi }} Sesi
                            <span class="text-[10px] text-gray-400 block">Sisa {{ $trx->sisa_sesi }} sesi</span>
                        </td>

                        <!-- Aksi -->
                        <td class="py-3.5 px-4 text-right">
                            <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                <!-- Verifikasi Pembayaran (Jika belum lunas atau menunggu verifikasi) -->
                                @if($trx->status_pembayaran !== 'Lunas' && $trx->status_pembayaran !== 'Dibatalkan')
                                <button onclick="verifikasiPembayaran({{ $trx->id }}, '{{ $trx->nomor_transaksi }}', '{{ e($trx->nama_pemesan) }}')" class="px-2.5 py-1.5 rounded-lg border border-emerald-200 bg-emerald-500 hover:bg-emerald-600 text-white text-[11px] font-bold transition flex items-center gap-1 shadow-xs" title="Verifikasi Pembayaran & Aktifkan Program">
                                    <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                                    Verifikasi
                                </button>
                                @endif

                                <!-- Selesaikan Program (Jika statusnya Aktif) -->
                                @if($trx->status === 'Aktif')
                                <button onclick="selesaikanProgram({{ $trx->id }}, '{{ e($trx->nama_pemesan) }}')" class="px-2.5 py-1.5 rounded-lg border border-indigo-200 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 text-[11px] font-bold transition flex items-center gap-1" title="Selesaikan Program untuk Mahasiswa Ini">
                                    <i data-lucide="check-check" class="w-3.5 h-3.5"></i>
                                    Selesaikan
                                </button>
                                @endif

                                <!-- Nonaktifkan / Aktifkan User -->
                                @if($trx->status !== 'Selesai' && $trx->status_pembayaran === 'Lunas')
                                <button onclick="toggleUserAkses({{ $trx->id }}, '{{ $trx->status }}', '{{ e($trx->nama_pemesan) }}')" class="px-2.5 py-1.5 rounded-lg border text-[11px] font-bold transition flex items-center gap-1 {{ $trx->status === 'Aktif' ? 'border-amber-200 bg-amber-50 text-amber-700 hover:bg-amber-100' : 'border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}" title="{{ $trx->status === 'Aktif' ? 'Nonaktifkan Akses User' : 'Aktifkan Akses User' }}">
                                    <i data-lucide="{{ $trx->status === 'Aktif' ? 'user-x' : 'user-check' }}" class="w-3.5 h-3.5"></i>
                                    {{ $trx->status === 'Aktif' ? 'Nonaktifkan User' : 'Aktifkan User' }}
                                </button>
                                @endif

                                <!-- Batalkan Pembayaran -->
                                @if($trx->status_pembayaran !== 'Dibatalkan' && $trx->status !== 'Selesai')
                                <button onclick="batalkanPembayaran({{ $trx->id }}, '{{ $trx->nomor_transaksi }}')" class="px-2 py-1.5 rounded-lg border border-red-200 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 text-[11px] font-bold transition flex items-center gap-1" title="Batalkan Pembayaran">
                                    <i data-lucide="x-circle" class="w-3.5 h-3.5"></i>
                                    Batalkan Bayar
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-gray-400">
                            <i data-lucide="users-2" class="w-12 h-12 mx-auto text-gray-300 mb-2"></i>
                            <p class="font-bold text-gray-600 text-sm">Belum Ada Mahasiswa Membeli Program Ini</p>
                            <p class="text-xs text-gray-400 mt-0.5">Semua data transaksi pemesanan paket ini akan tampil lengkap di tabel ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (window.lucide) {
            lucide.createIcons();
        }
    });

    function verifikasiPembayaran(transaksiId, nomorTrx, namaUser) {
        if (!confirm('Verifikasi bukti transfer transaksi #' + nomorTrx + ' untuk mahasiswa ' + namaUser + '? Status pembayaran akan menjadi LUNAS dan program akan otomatis AKTIF.')) return;

        $.ajax({
            url: "{{ url('master/programs/transaksi') }}/" + transaksiId + "/verifikasi",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
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

    function selesaikanProgram(transaksiId, namaUser) {
        if (!confirm('Tandai program bimbingan untuk mahasiswa ' + namaUser + ' sebagai SELESAI? Mahasiswa tidak akan dapat mengajukan sesi baru dan hanya dapat mengakses riwayat bimbingan.')) return;

        $.ajax({
            url: "{{ url('master/programs/transaksi') }}/" + transaksiId + "/selesaikan",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Program Selesai!', text: res.message, timer: 1800, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    alert(res.message);
                    location.reload();
                }
            },
            error: function() {
                alert('Gagal menyelesaikan program.');
            }
        });
    }

    function toggleProgramActive(id, newStatus) {
        let actionWord = newStatus ? 'mengaktifkan kembali' : 'menonaktifkan';
        if (!confirm('Apakah Anda yakin ingin ' + actionWord + ' program bimbingan ini?')) return;

        $.ajax({
            url: "{{ url('master/programs') }}/" + id + "/toggle-status",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    alert(res.message);
                    location.reload();
                }
            },
            error: function() {
                alert('Gagal mengubah status program.');
            }
        });
    }

    function batalkanPembayaran(transaksiId, nomorTrx) {
        if (!confirm('Batalkan status pembayaran untuk transaksi #' + nomorTrx + '? Status program mahasiswa ini juga akan dinonaktifkan.')) return;

        $.ajax({
            url: "{{ url('master/programs/transaksi') }}/" + transaksiId + "/batalkan",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Dibatalkan', text: res.message, timer: 1800, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    alert(res.message);
                    location.reload();
                }
            },
            error: function() {
                alert('Gagal membatalkan transaksi pembayaran.');
            }
        });
    }

    function toggleUserAkses(transaksiId, currentStatus, namaUser) {
        let targetWord = currentStatus === 'Aktif' ? 'menonaktifkan' : 'mengaktifkan kembali';
        if (!confirm('Apakah Anda ingin ' + targetWord + ' akses program mahasiswa ' + namaUser + '?')) return;

        $.ajax({
            url: "{{ url('master/programs/transaksi') }}/" + transaksiId + "/toggle-user",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'success', title: 'Berhasil', text: res.message, timer: 1800, showConfirmButton: false })
                        .then(() => location.reload());
                } else {
                    alert(res.message);
                    location.reload();
                }
            },
            error: function() {
                alert('Gagal mengubah status akses user.');
            }
        });
    }
</script>
@endpush
