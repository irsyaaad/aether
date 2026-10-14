@extends('layouts.app')

@section('title', 'Instruksi Pembayaran ' . $transaksi->nomor_registrasi . ' - ' . config('app.name'))
@section('page_title', 'Instruksi Pembayaran & Upload Bukti')

@push('styles')
<style>
    .copy-tooltip {
        transition: opacity 0.2s ease-in-out;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-6 pb-12">
    <!-- Breadcrumb & Step Progress -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-2 text-xs text-gray-500">
            <a href="{{ route('pelatihan.index') }}" class="inline-flex items-center gap-1.5 font-bold text-[#1768B5] hover:underline">
                <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                Katalog Program
            </a>
            <span class="text-gray-300">•</span>
            <span class="text-gray-700 font-semibold truncate">{{ $transaksi->nomor_registrasi }}</span>
        </div>

        <!-- 3 Steps Bar -->
        <div class="flex items-center gap-2 text-[11px] font-bold">
            <span class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                1. Isi Profil
            </span>
            <span class="text-gray-300">→</span>
            <span class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#1768B5] text-white shadow-xs">
                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                2. Bayar &amp; Upload Bukti
            </span>
            <span class="text-gray-300">→</span>
            <span class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-400">
                3. Kelas Aktif
            </span>
        </div>
    </div>

    <!-- Main Container Card -->
    <div class="rounded-3xl border border-gray-200 bg-white shadow-xs overflow-hidden">
        <!-- Hero Header Card with Signature Gradient -->
        <div class="bg-gradient-to-r from-[#1D6990] via-[#2B346F] to-[#1768B5] px-6 sm:px-8 py-8 text-white flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-400/20 text-amber-300 text-[11px] font-bold uppercase tracking-widest border border-amber-300/30 backdrop-blur-xs">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                    Menunggu Pembayaran &amp; Upload Bukti
                </div>
                <h1 class="text-2xl sm:text-3xl font-black leading-tight tracking-tight text-white">
                    Instruksi Pembayaran Program
                </h1>
                <p class="max-w-2xl text-xs sm:text-sm text-white/90 leading-relaxed">
                    Pesanan Anda telah dicatat dengan nomor transaksi <strong class="font-mono text-white underline decoration-sky-300">{{ $transaksi->nomor_registrasi }}</strong>. Silakan lakukan pembayaran dan unggah bukti transfer di bawah ini.
                </p>
            </div>

            <div class="bg-white/10 rounded-2xl p-4 border border-white/15 backdrop-blur-xs shrink-0 text-right flex flex-col items-end justify-between gap-3">
                <div>
                    <span class="text-[11px] text-white/80 block uppercase tracking-wider font-semibold">Total Tagihan:</span>
                    <span class="text-2xl font-black text-white">
                        Rp {{ number_format($transaksi->total_bayar ?: ($transaksi->biaya + 5000), 0, ',', '.') }}
                    </span>
                    <div class="text-[10px] text-amber-300 mt-1 flex items-center justify-end gap-1 font-semibold">
                        <i data-lucide="clock" class="w-3 h-3"></i>
                        Batas Waktu: 24 Jam
                    </div>
                </div>

                @if($transaksi->status_pembayaran !== 'Lunas')
                <button type="button" onclick="openGantiMetodeModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/20 hover:bg-white/30 text-white text-xs font-bold transition border border-white/30 backdrop-blur-xs shadow-xs cursor-pointer">
                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                    <span>Ganti Metode Bayar</span>
                </button>
                @endif
            </div>
        </div>

        @if(session('info'))
        <div class="m-6 p-4 rounded-2xl bg-sky-50 border border-sky-200 text-[#1768B5] text-xs flex items-center gap-2 font-semibold">
            <i data-lucide="info" class="w-4 h-4 shrink-0"></i>
            <span>{{ session('info') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="m-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1">
            <div class="flex items-center gap-2 font-bold">
                <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
                <span>Terdapat kesalahan saat mengunggah bukti:</span>
            </div>
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 p-6 sm:p-8">
            <!-- Left Column: Payment Details & Upload Box (Col-span 2) -->
            <div class="lg:col-span-2 space-y-6">
                @php
                    $isQris = str_contains(strtoupper($transaksi->metode_pembayaran ?? ''), 'QRIS');
                    $isTransferManual = str_contains(strtoupper($transaksi->metode_pembayaran ?? ''), 'TRANSFER');
                @endphp

                <!-- KASUS 1: PEMBAYARAN DENGAN QRIS -->
                @if($isQris)
                <div class="rounded-2xl border-2 border-rose-100 bg-gradient-to-br from-white via-rose-50/20 to-orange-50/20 p-6 sm:p-7 shadow-xs space-y-5">
                    <div class="flex items-center justify-between pb-4 border-b border-rose-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-rose-500 text-white font-black text-xs flex items-center justify-center shadow-xs">
                                QRIS
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">QRIS Pembayaran Digital</h3>
                                <p class="text-[11px] text-gray-500">Scan via GoPay, OVO, DANA, ShopeePay, BCA Mobile, Livin, dll.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700">
                                Semua E-Wallet / Bank
                            </span>
                            <button type="button" onclick="openGantiMetodeModal()" class="px-2.5 py-1 rounded-lg text-xs font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 transition">
                                Ganti Metode
                            </button>
                        </div>
                    </div>

                    <!-- QR Code Frame Display -->
                    <div class="flex flex-col sm:flex-row items-center gap-6 bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs">
                        <!-- QR Code Graphic Container -->
                        <div class="relative w-48 h-48 bg-white p-3 rounded-2xl border-2 border-dashed border-gray-300 flex flex-col items-center justify-center shadow-inner shrink-0 group">
                            <!-- SVG QR Pattern Representation -->
                            <svg class="w-full h-full text-gray-900" viewBox="0 0 100 100" fill="currentColor">
                                <!-- Corner Squares -->
                                <rect x="5" y="5" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="5"/>
                                <rect x="12" y="12" width="12" height="12" fill="currentColor"/>
                                <rect x="69" y="5" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="5"/>
                                <rect x="76" y="12" width="12" height="12" fill="currentColor"/>
                                <rect x="5" y="69" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="5"/>
                                <rect x="12" y="76" width="12" height="12" fill="currentColor"/>
                                <!-- Data Pattern Mock -->
                                <rect x="37" y="8" width="6" height="6"/>
                                <rect x="47" y="15" width="6" height="12"/>
                                <rect x="57" y="8" width="6" height="6"/>
                                <rect x="37" y="27" width="12" height="6"/>
                                <rect x="8" y="37" width="12" height="6"/>
                                <rect x="25" y="42" width="6" height="12"/>
                                <rect x="37" y="37" width="8" height="8"/>
                                <rect x="50" y="40" width="10" height="6"/>
                                <rect x="65" y="37" width="8" height="8"/>
                                <rect x="78" y="42" width="14" height="6"/>
                                <rect x="8" y="53" width="6" height="10"/>
                                <rect x="20" y="58" width="8" height="6"/>
                                <rect x="35" y="50" width="8" height="14"/>
                                <rect x="48" y="52" width="14" height="6"/>
                                <rect x="66" y="52" width="6" height="12"/>
                                <rect x="76" y="55" width="16" height="6"/>
                                <rect x="37" y="70" width="6" height="14"/>
                                <rect x="47" y="65" width="10" height="6"/>
                                <rect x="60" y="72" width="14" height="6"/>
                                <rect x="78" y="68" width="14" height="12"/>
                                <rect x="48" y="80" width="8" height="12"/>
                                <rect x="62" y="84" width="12" height="8"/>
                                <circle cx="50" cy="50" r="5" fill="#1768B5"/>
                            </svg>
                            <span class="text-[9px] font-bold text-gray-500 uppercase tracking-widest mt-1">Scan QRIS</span>
                        </div>

                        <!-- Instructions beside QR -->
                        <div class="space-y-3 text-xs flex-1">
                            <div>
                                <span class="text-gray-400 font-semibold block text-[11px]">Merchant Penerima:</span>
                                <p class="font-bold text-gray-900 text-sm">AETHER ACADEMY INDONESIA</p>
                                <p class="text-[11px] text-gray-400 font-mono">NMID: ID1024392819203</p>
                            </div>

                            <div class="pt-2 border-t border-gray-100 flex items-center justify-between">
                                <div>
                                    <span class="text-gray-400 font-semibold block text-[11px]">Jumlah yang Harus Dibayar:</span>
                                    <p class="text-base font-black text-rose-600">
                                        Rp {{ number_format($transaksi->total_bayar ?: ($transaksi->biaya + 5000), 0, ',', '.') }}
                                    </p>
                                </div>
                                <button type="button" onclick="copyToClipboard('{{ $transaksi->total_bayar ?: ($transaksi->biaya + 5000) }}', 'copy-total-toast')" class="px-3 py-1.5 rounded-lg border border-gray-200 text-[11px] font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-1">
                                    <i data-lucide="copy" class="w-3.5 h-3.5 text-[#1768B5]"></i>
                                    <span>Salin Nominal</span>
                                </button>
                            </div>

                            <div class="p-2.5 rounded-xl bg-gray-50 text-[11px] text-gray-500 space-y-1">
                                <p class="font-bold text-gray-700">Cara Scan QRIS:</p>
                                <p>1. Buka aplikasi m-Banking atau e-Wallet di smartphone Anda.</p>
                                <p>2. Pilih menu <strong>Scan / Bayar QRIS</strong> dan arahkan kamera ke barcode di samping.</p>
                                <p>3. Pastikan nama merchant sesuai dan nominal sesuai dengan tagihan.</p>
                                <p>4. Simpan screenshot bukti transaksi untuk diupload di bawah.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KASUS 2: PEMBAYARAN TRANSFER MANUAL KE REKENING -->
                @elseif($isTransferManual)
                <div class="rounded-2xl border-2 border-indigo-100 bg-gradient-to-br from-white via-indigo-50/20 to-blue-50/20 p-6 sm:p-7 shadow-xs space-y-5">
                    <div class="flex items-center justify-between pb-4 border-b border-indigo-100">
                        <div class="flex items-center gap-3">
                            @php
                                $bankLabel = 'BCA';
                                $bankColor = 'bg-indigo-600 text-white';
                                $atasNama = 'PT Aether Edukasi Indonesia';
                                if (str_contains(strtoupper($transaksi->metode_pembayaran), 'MANDIRI')) {
                                    $bankLabel = 'MDR';
                                    $bankColor = 'bg-amber-600 text-white';
                                }
                            @endphp
                            <div class="w-10 h-10 rounded-xl {{ $bankColor }} font-black text-xs flex items-center justify-center shadow-xs">
                                {{ $bankLabel }}
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">{{ $transaksi->metode_pembayaran }}</h3>
                                <p class="text-[11px] text-gray-500">Transfer langsung ke rekening resmi lembaga</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-indigo-100 text-indigo-700">
                                Rekening Bank
                            </span>
                            <button type="button" onclick="openGantiMetodeModal()" class="px-2.5 py-1 rounded-lg text-xs font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 transition">
                                Ganti Metode
                            </button>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div>
                                <span class="text-[11px] text-gray-400 font-bold uppercase tracking-wider block">Nomor Rekening Tujuan:</span>
                                <span id="rekening-number-text" class="text-xl sm:text-2xl font-mono font-black text-gray-900 tracking-wider">
                                    {{ $transaksi->kode_pembayaran ?? '7310889201' }}
                                </span>
                                <span class="text-[11px] text-gray-500 block mt-0.5">Atas Nama: <strong class="text-gray-800">{{ $atasNama }}</strong></span>
                            </div>
                            <button type="button" onclick="copyToClipboard('{{ $transaksi->kode_pembayaran ?? '7310889201' }}', 'copy-rekening-toast')" class="px-4 py-2.5 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs cursor-pointer shrink-0">
                                <i data-lucide="copy" class="w-4 h-4"></i>
                                <span>Salin No. Rekening</span>
                            </button>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div>
                                <span class="text-[11px] text-gray-400 font-bold uppercase tracking-wider block">Total Nominal Transfer:</span>
                                <span class="text-lg font-black text-emerald-600">
                                    Rp {{ number_format($transaksi->total_bayar ?: ($transaksi->biaya + 5000), 0, ',', '.') }}
                                </span>
                            </div>
                            <button type="button" onclick="copyToClipboard('{{ $transaksi->total_bayar ?: ($transaksi->biaya + 5000) }}', 'copy-total-toast')" class="px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-100 transition flex items-center justify-center gap-1.5 shrink-0">
                                <i data-lucide="copy" class="w-3.5 h-3.5 text-gray-500"></i>
                                <span>Salin Jumlah Nominal</span>
                            </button>
                        </div>

                        <!-- Petunjuk Transfer Rekening -->
                        <div class="p-3 rounded-xl bg-gray-50 text-[11px] text-gray-600 space-y-1">
                            <p class="font-bold text-gray-700">Langkah Transfer Manual Rekening Bank:</p>
                            <p>1. Buka m-Banking / ATM / Internet Banking Anda dan pilih menu <strong>Transfer Antar Bank / Sesama Bank</strong>.</p>
                            <p>2. Masukkan nomor rekening di atas dan pastikan nama penerima: <strong>{{ $atasNama }}</strong>.</p>
                            <p>3. Masukkan nominal transfer tepat sesuai tagihan di atas.</p>
                            <p>4. Simpan bukti transfer dan unggah melalui formulir di bawah ini.</p>
                        </div>
                    </div>
                </div>

                <!-- KASUS 3: PEMBAYARAN DENGAN VIRTUAL ACCOUNT -->
                @else
                <div class="rounded-2xl border-2 border-sky-100 bg-gradient-to-br from-white via-sky-50/20 to-blue-50/20 p-6 sm:p-7 shadow-xs space-y-5">
                    <div class="flex items-center justify-between pb-4 border-b border-sky-100">
                        <div class="flex items-center gap-3">
                            @php
                                $vaMethod = $transaksi->metode_pembayaran ?? 'BCA Virtual Account';
                                $bankCode = 'VA';
                                $badgeColor = 'bg-blue-50 text-blue-700 border-blue-200';
                                if (str_contains(strtoupper($vaMethod), 'BCA')) { $bankCode = 'BCA'; $badgeColor = 'bg-blue-600 text-white'; }
                                elseif (str_contains(strtoupper($vaMethod), 'MANDIRI')) { $bankCode = 'MDR'; $badgeColor = 'bg-amber-600 text-white'; }
                                elseif (str_contains(strtoupper($vaMethod), 'BNI')) { $bankCode = 'BNI'; $badgeColor = 'bg-teal-600 text-white'; }
                                elseif (str_contains(strtoupper($vaMethod), 'BRI')) { $bankCode = 'BRI'; $badgeColor = 'bg-sky-600 text-white'; }
                            @endphp
                            <div class="w-10 h-10 rounded-xl {{ $badgeColor }} font-black text-xs flex items-center justify-center shadow-xs">
                                {{ $bankCode }}
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-gray-900">{{ $transaksi->metode_pembayaran }}</h3>
                                <p class="text-[11px] text-gray-500">Nomor Virtual Account pembayaran Anda</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-sky-100 text-[#1768B5]">
                                Verifikasi 24 Jam
                            </span>
                            <button type="button" onclick="openGantiMetodeModal()" class="px-2.5 py-1 rounded-lg text-xs font-bold text-sky-700 bg-sky-50 hover:bg-sky-100 border border-sky-200 transition">
                                Ganti Metode
                            </button>
                        </div>
                    </div>

                    <!-- VA Number Display Box with Copy Button -->
                    <div class="bg-white p-5 rounded-2xl border border-gray-200/80 shadow-xs space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div>
                                <span class="text-[11px] text-gray-400 font-bold uppercase tracking-wider block">Nomor Virtual Account:</span>
                                <span id="va-number-text" class="text-xl sm:text-2xl font-mono font-black text-gray-900 tracking-wider">
                                    {{ $transaksi->kode_pembayaran ?? '80777123456789' }}
                                </span>
                            </div>
                            <button type="button" onclick="copyToClipboard('{{ $transaksi->kode_pembayaran }}', 'copy-va-toast')" class="px-4 py-2.5 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs cursor-pointer shrink-0">
                                <i data-lucide="copy" class="w-4 h-4"></i>
                                <span>Salin Nomor VA</span>
                            </button>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div>
                                <span class="text-[11px] text-gray-400 font-bold uppercase tracking-wider block">Total Tagihan Transfer:</span>
                                <span class="text-lg font-black text-emerald-600">
                                    Rp {{ number_format($transaksi->total_bayar ?: ($transaksi->biaya + 5000), 0, ',', '.') }}
                                </span>
                            </div>
                            <button type="button" onclick="copyToClipboard('{{ $transaksi->total_bayar ?: ($transaksi->biaya + 5000) }}', 'copy-total-toast')" class="px-3.5 py-2 rounded-xl border border-gray-200 text-xs font-bold text-gray-700 hover:bg-gray-100 transition flex items-center justify-center gap-1.5 shrink-0">
                                <i data-lucide="copy" class="w-3.5 h-3.5 text-gray-500"></i>
                                <span>Salin Jumlah Nominal</span>
                            </button>
                        </div>

                        <!-- Bank Transfer Instructions Accordion -->
                        <div class="pt-2 text-xs space-y-2">
                            <p class="font-bold text-gray-700 uppercase tracking-wider text-[11px]">Petunjuk Pembayaran Transfer:</p>
                            <details class="group rounded-xl border border-gray-100 bg-gray-50/50 p-3">
                                <summary class="font-bold text-gray-800 cursor-pointer flex items-center justify-between">
                                    <span>Via Mobile Banking (m-Banking)</span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform"></i>
                                </summary>
                                <ol class="mt-2 text-[11px] text-gray-600 space-y-1 list-decimal pl-4">
                                    <li>Buka aplikasi m-Banking Anda, lalu pilih menu <strong>Transfer &gt; Virtual Account</strong>.</li>
                                    <li>Masukkan Nomor Virtual Account di atas.</li>
                                    <li>Periksa rincian tagihan (Nama Mahasiswa &amp; Pelatihan & Workshop).</li>
                                    <li>Konfirmasi pembayaran dan simpan bukti transfer Anda.</li>
                                </ol>
                            </details>

                            <details class="group rounded-xl border border-gray-100 bg-gray-50/50 p-3">
                                <summary class="font-bold text-gray-800 cursor-pointer flex items-center justify-between">
                                    <span>Via ATM / Mesin Setor Tunai</span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform"></i>
                                </summary>
                                <ol class="mt-2 text-[11px] text-gray-600 space-y-1 list-decimal pl-4">
                                    <li>Masukkan kartu ATM dan PIN Anda.</li>
                                    <li>Pilih menu <strong>Transaksi Lainnya &gt; Pembayaran / Pembelian &gt; Virtual Account</strong>.</li>
                                    <li>Ketikkan Nomor Virtual Account di atas.</li>
                                    <li>Lanjutkan hingga struk pembayaran keluar dan foto struknya.</li>
                                </ol>
                            </details>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Upload Bukti Pembayaran Form Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-6 sm:p-7 shadow-xs space-y-5">
                    <div class="flex items-center gap-2 pb-3 border-b border-gray-100">
                        <i data-lucide="upload-cloud" class="w-5 h-5 text-emerald-600"></i>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900">Upload Bukti Pembayaran</h3>
                            <p class="text-[11px] text-gray-500">Kirimkan foto struk atau screenshot mutasi Anda untuk mengaktifkan kelas workshop.</p>
                        </div>
                    </div>

                    <form action="{{ route('pelatihan.payment.upload-bukti', $transaksi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                        @csrf

                        <!-- Upload Zone & Live Preview -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-2">
                                Foto Struk / Screenshot Bukti Pembayaran <span class="text-rose-500">*</span>
                            </label>
                            
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                <!-- Preview Box -->
                                <div id="receipt-preview-wrapper" class="relative w-full sm:w-44 h-36 rounded-2xl border-2 border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center overflow-hidden shrink-0 group transition hover:border-emerald-400">
                                    <img id="receipt-preview-img" src="" alt="Bukti Pembayaran" class="w-full h-full object-cover hidden">
                                    <div id="receipt-placeholder" class="flex flex-col items-center justify-center p-3 text-center text-gray-400">
                                        <i data-lucide="image" class="w-8 h-8 text-emerald-500 mb-1.5"></i>
                                        <span class="text-[11px] font-bold text-gray-600">Preview Bukti</span>
                                        <span class="text-[10px] text-gray-400">Belum ada file dipilih</span>
                                    </div>
                                </div>

                                <!-- File Input & Instructions -->
                                <div class="flex-1 space-y-2 w-full">
                                    <input type="file" id="bukti_pembayaran" name="bukti_pembayaran" accept="image/jpeg,image/png,image/jpg,image/webp" required onchange="previewReceiptImage(event)" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer">
                                    <p class="text-[11px] text-gray-400 leading-relaxed">
                                        Format didukung: <strong>JPG, JPEG, PNG, WEBP</strong> (Maks. 3 MB). Pastikan teks nominal transfer dan tanggal terbaca jelas.
                                    </p>
                                    <button type="button" id="clear-receipt-btn" onclick="clearReceiptPreview()" class="text-[11px] font-bold text-rose-600 hover:underline hidden flex items-center gap-1">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        <span>Ganti / Batalkan File Ini</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Catatan / Keterangan Pengirim (Opsional) -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1.5">
                                Catatan / Nama Pemilik Rekening Pengirim (Opsional)
                            </label>
                            <input type="text" name="catatan" value="{{ old('catatan') }}" placeholder="Contoh: Transfer atas nama Budi Santoso / Rekening BCA" class="w-full px-3.5 py-2.5 rounded-xl border border-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
                        </div>

                        <!-- Submit Button -->
                        <div class="pt-3 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <span class="text-[11px] text-gray-400 flex items-center gap-1">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
                                <span>Verifikasi Cepat &amp; Aman</span>
                            </span>

                            <button type="submit" class="w-full sm:w-auto px-7 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-bold text-xs shadow-md transition flex items-center justify-center gap-2 cursor-pointer">
                                <i data-lucide="send" class="w-4 h-4"></i>
                                <span>Kirim Bukti Pembayaran</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Right Column: Sidebar Program Summary -->
            <aside class="space-y-5">
                <!-- Program Info Card -->
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-xs space-y-4">
                    <p class="text-[11px] uppercase tracking-[0.15em] text-gray-400 font-bold">Ringkasan Program</p>
                    
                    @if($transaksi->program && $transaksi->program->banner_url)
                    <div class="w-full h-32 rounded-xl overflow-hidden border border-gray-100 shadow-xs">
                        <img src="{{ $transaksi->program->banner_url }}" alt="{{ $transaksi->judul_kelas }}" class="w-full h-full object-cover">
                    </div>
                    @endif

                    <div class="rounded-xl border border-gray-100 bg-gray-50/80 p-4">
                        <h4 class="text-xs font-bold text-gray-900 leading-snug">{{ $transaksi->judul_kelas }}</h4>
                        <div class="flex items-center gap-2 mt-1.5 text-[11px] text-gray-500">
                            <span class="inline-flex items-center gap-1 text-sky-700 font-semibold">
                                <i data-lucide="calendar" class="w-3 h-3"></i>
                                {{ $transaksi->total_sesi }}x Sesi Pertemuan
                            </span>
                            <span>•</span>
                            <span>Konsultasi 1-on-1</span>
                        </div>
                    </div>

                    <!-- Customer Info -->
                    <div class="pt-3 border-t border-gray-100 space-y-1.5 text-xs">
                        <span class="text-gray-400 font-bold uppercase tracking-wider text-[10px]">Data Pemesan:</span>
                        <p class="font-bold text-gray-800">{{ $transaksi->nama_pemesan }}</p>
                        <p class="text-gray-500 text-[11px]">{{ $transaksi->email_pemesan }}</p>
                        <p class="text-gray-500 text-[11px] font-mono">{{ $transaksi->telepon_pemesan }}</p>
                        @if($transaksi->universitas)
                        <p class="text-sky-700 text-[11px] font-semibold">{{ $transaksi->universitas }}</p>
                        @endif
                    </div>
                </div>

                <!-- WhatsApp Help & Support Card -->
                <div class="rounded-2xl border border-emerald-100 bg-emerald-50/60 p-5 shadow-xs space-y-3">
                    <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs">
                        <i data-lucide="help-circle" class="w-4 h-4 text-emerald-600"></i>
                        <span>Butuh Bantuan Transfer?</span>
                    </div>
                    <p class="text-[11px] text-emerald-800/80 leading-relaxed">
                        Jika Anda mengalami kendala saat transfer VA atau scan QRIS, hubungi tim support akademik kami via WhatsApp.
                    </p>
                    <a href="https://wa.me/6281234567890?text=Halo%20Admin%2C%20saya%20butuh%20bantuan%20pembayaran%20dengan%20nomor%20transaksi%20{{ $transaksi->nomor_registrasi }}" target="_blank" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs flex items-center justify-center gap-1.5 shadow-xs transition">
                        <i data-lucide="message-circle" class="w-3.5 h-3.5"></i>
                        <span>Chat WhatsApp Support</span>
                    </a>
                </div>
            </aside>
        </div>
    </div>
</div>

<!-- Copy Feedback Toast Notification -->
<div id="copy-toast" class="fixed bottom-6 right-6 z-50 bg-gray-900 text-white text-xs px-4 py-2.5 rounded-xl shadow-lg flex items-center gap-2 opacity-0 pointer-events-none transition-opacity duration-200">
    <i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i>
    <span id="copy-toast-msg">Berhasil disalin ke clipboard!</span>
</div>

<!-- Modal Ganti Metode Pembayaran -->
<div id="modal-ganti-metode" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs hidden transition-opacity duration-300">
    <div class="relative w-full max-w-lg bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden transform transition-all">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-[#102654] via-[#1768B5] to-[#2B8BD1] px-6 py-5 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-white/20 flex items-center justify-center text-white backdrop-blur-xs border border-white/20">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-base font-black text-white leading-tight">Ganti Metode Pembayaran</h3>
                    <p class="text-xs text-white/80">Pilih opsi pembayaran baru untuk tagihan ini</p>
                </div>
            </div>
            <button type="button" onclick="closeGantiMetodeModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Modal Body & Form -->
        <form action="{{ route('pelatihan.payment.ganti-metode', $transaksi->id) }}" method="POST" class="p-6 space-y-5">
            @csrf
            
            <div class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800 text-xs flex items-start gap-2.5">
                <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                <div class="leading-relaxed">
                    <p class="font-bold">Konfirmasi Pergantian Metode:</p>
                    <p class="mt-0.5 text-[11px] text-amber-700">Tagihan lama (<strong>#{{ $transaksi->nomor_registrasi }}</strong>) akan otomatis dibatalkan, dan sistem akan membuat tagihan baru beserta nomor VA / QRIS / Rekening baru.</p>
                </div>
            </div>

            <div class="space-y-2.5">
                <label class="block text-xs font-black uppercase tracking-wider text-gray-700">
                    Pilih Metode Pembayaran Baru:
                </label>

                <div class="space-y-2 max-h-72 overflow-y-auto pr-1">
                    <!-- QRIS Instant -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="QRIS Instant" class="peer sr-only" {{ str_contains(strtoupper($transaksi->metode_pembayaran), 'QRIS') ? 'checked' : '' }}>
                        <div class="p-3 rounded-xl border border-gray-200 bg-white peer-checked:border-[#1768B5] peer-checked:bg-sky-50/50 hover:border-gray-300 transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-rose-50 text-rose-700 font-black text-[11px] flex items-center justify-center border border-rose-100">QRIS</span>
                                <div>
                                    <p class="text-xs font-bold text-gray-800">QRIS Instant (Semua Bank / E-Wallet)</p>
                                    <p class="text-[10px] text-gray-400">Scan barcode via GoPay, OVO, DANA, BCA, Mandiri, dll.</p>
                                </div>
                            </div>
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5]"></div>
                        </div>
                    </label>

                    <!-- BCA Virtual Account -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="BCA Virtual Account" class="peer sr-only" {{ $transaksi->metode_pembayaran == 'BCA Virtual Account' ? 'checked' : '' }}>
                        <div class="p-3 rounded-xl border border-gray-200 bg-white peer-checked:border-[#1768B5] peer-checked:bg-sky-50/50 hover:border-gray-300 transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-700 font-black text-[11px] flex items-center justify-center border border-blue-100">BCA</span>
                                <div>
                                    <p class="text-xs font-bold text-gray-800">BCA Virtual Account</p>
                                    <p class="text-[10px] text-gray-400">Verifikasi instan via m-Banking / ATM BCA</p>
                                </div>
                            </div>
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5]"></div>
                        </div>
                    </label>

                    <!-- Mandiri Virtual Account -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="Mandiri Virtual Account" class="peer sr-only" {{ $transaksi->metode_pembayaran == 'Mandiri Virtual Account' ? 'checked' : '' }}>
                        <div class="p-3 rounded-xl border border-gray-200 bg-white peer-checked:border-[#1768B5] peer-checked:bg-sky-50/50 hover:border-gray-300 transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 font-black text-[11px] flex items-center justify-center border border-amber-100">MDR</span>
                                <div>
                                    <p class="text-xs font-bold text-gray-800">Mandiri Virtual Account</p>
                                    <p class="text-[10px] text-gray-400">Via Livin' by Mandiri / ATM Mandiri</p>
                                </div>
                            </div>
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5]"></div>
                        </div>
                    </label>

                    <!-- BNI Virtual Account -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="BNI Virtual Account" class="peer sr-only" {{ $transaksi->metode_pembayaran == 'BNI Virtual Account' ? 'checked' : '' }}>
                        <div class="p-3 rounded-xl border border-gray-200 bg-white peer-checked:border-[#1768B5] peer-checked:bg-sky-50/50 hover:border-gray-300 transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-teal-50 text-teal-700 font-black text-[11px] flex items-center justify-center border border-teal-100">BNI</span>
                                <div>
                                    <p class="text-xs font-bold text-gray-800">BNI Virtual Account</p>
                                    <p class="text-[10px] text-gray-400">Via BNI Mobile Banking / ATM BNI</p>
                                </div>
                            </div>
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5]"></div>
                        </div>
                    </label>

                    <!-- BRI Virtual Account -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="BRI Virtual Account" class="peer sr-only" {{ $transaksi->metode_pembayaran == 'BRI Virtual Account' ? 'checked' : '' }}>
                        <div class="p-3 rounded-xl border border-gray-200 bg-white peer-checked:border-[#1768B5] peer-checked:bg-sky-50/50 hover:border-gray-300 transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-sky-50 text-sky-700 font-black text-[11px] flex items-center justify-center border border-sky-100">BRI</span>
                                <div>
                                    <p class="text-xs font-bold text-gray-800">BRI (BRIVA)</p>
                                    <p class="text-[10px] text-gray-400">Via BRImo / ATM BRI</p>
                                </div>
                            </div>
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5]"></div>
                        </div>
                    </label>

                    <!-- Transfer Manual Rekening BCA -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="Transfer Bank BCA" class="peer sr-only" {{ $transaksi->metode_pembayaran == 'Transfer Bank BCA' ? 'checked' : '' }}>
                        <div class="p-3 rounded-xl border border-gray-200 bg-white peer-checked:border-[#1768B5] peer-checked:bg-sky-50/50 hover:border-gray-300 transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-black text-[11px] flex items-center justify-center border border-indigo-100">TF</span>
                                <div>
                                    <p class="text-xs font-bold text-gray-800">Transfer Rekening BCA</p>
                                    <p class="text-[10px] text-gray-400">No. Rek: 7310889201 a.n PT Aether Edukasi Indonesia</p>
                                </div>
                            </div>
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5]"></div>
                        </div>
                    </label>

                    <!-- Transfer Manual Rekening Mandiri -->
                    <label class="block cursor-pointer">
                        <input type="radio" name="metode_pembayaran" value="Transfer Bank Mandiri" class="peer sr-only" {{ $transaksi->metode_pembayaran == 'Transfer Bank Mandiri' ? 'checked' : '' }}>
                        <div class="p-3 rounded-xl border border-gray-200 bg-white peer-checked:border-[#1768B5] peer-checked:bg-sky-50/50 hover:border-gray-300 transition flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="w-8 h-8 rounded-lg bg-amber-50 text-amber-700 font-black text-[11px] flex items-center justify-center border border-amber-100">TF</span>
                                <div>
                                    <p class="text-xs font-bold text-gray-800">Transfer Rekening Mandiri</p>
                                    <p class="text-[10px] text-gray-400">No. Rek: 1370019283741 a.n PT Aether Edukasi Indonesia</p>
                                </div>
                            </div>
                            <div class="w-4 h-4 rounded-full border-2 border-gray-300 peer-checked:border-[#1768B5] peer-checked:bg-[#1768B5]"></div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                <button type="button" onclick="closeGantiMetodeModal()" class="px-4 py-2.5 rounded-xl border border-gray-300 text-xs font-bold text-gray-600 hover:bg-gray-50 transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    <span>Terapkan &amp; Buat Tagihan Baru</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openGantiMetodeModal() {
        const modal = document.getElementById('modal-ganti-metode');
        modal.classList.remove('hidden');
        if (window.lucide) {
            lucide.createIcons();
        }
    }

    function closeGantiMetodeModal() {
        const modal = document.getElementById('modal-ganti-metode');
        modal.classList.add('hidden');
    }

    function copyToClipboard(text, elementId) {
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(text);
        } else {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            textArea.style.position = "fixed";
            textArea.style.left = "-999999px";
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            document.execCommand('copy');
            textArea.remove();
        }

        const toast = document.getElementById('copy-toast');
        const toastMsg = document.getElementById('copy-toast-msg');
        toastMsg.textContent = 'Berhasil disalin: ' + text;
        toast.classList.remove('opacity-0', 'pointer-events-none');
        toast.classList.add('opacity-100');

        setTimeout(function() {
            toast.classList.remove('opacity-100');
            toast.classList.add('opacity-0', 'pointer-events-none');
        }, 2200);
    }

    function previewReceiptImage(event) {
        const input = event.target;
        const previewImg = document.getElementById('receipt-preview-img');
        const placeholder = document.getElementById('receipt-placeholder');
        const clearBtn = document.getElementById('clear-receipt-btn');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                placeholder.classList.add('hidden');
                clearBtn.classList.remove('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function clearReceiptPreview() {
        const input = document.getElementById('bukti_pembayaran');
        const previewImg = document.getElementById('receipt-preview-img');
        const placeholder = document.getElementById('receipt-placeholder');
        const clearBtn = document.getElementById('clear-receipt-btn');

        input.value = '';
        previewImg.src = '';
        previewImg.classList.add('hidden');
        placeholder.classList.remove('hidden');
        clearBtn.classList.add('hidden');
    }
</script>
@endpush

