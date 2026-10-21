@extends('layouts.app')

@section('title', 'Pembayaran: ' . $transaksi->nomor_registrasi . ' - ' . config('app.name'))
@section('page_title', 'Checkout Pembayaran')

@push('styles')
<style>
    .xendit-aside {
        background-color: #F8FAFC;
    }
    .payment-channel-btn:hover {
        border-color: #1768B5;
    }
</style>
@endpush

@section('content')
<div class="max-w-5xl mx-auto space-y-5 pb-14">
    <!-- Top Bar: Back & Merchant Info -->
    <div class="flex items-center justify-between py-2 text-xs text-gray-500">
        <a href="{{ route('pelatihan.index') }}" class="inline-flex items-center gap-1.5 font-bold text-[#1768B5] hover:text-[#0e487f] transition">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Katalog</span>
        </a>
        <div class="flex items-center gap-1.5 font-semibold text-gray-600">
            <i data-lucide="shield-check" class="w-4 h-4 text-emerald-600"></i>
            <span>Pembayaran Aman &amp; Terenkripsi</span>
        </div>
    </div>

    <!-- Notifications -->
    @if(session('info'))
    <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200 text-[#1768B5] text-xs flex items-center gap-2.5 font-semibold shadow-2xs">
        <i data-lucide="info" class="w-4 h-4 text-[#1768B5] shrink-0"></i>
        <span>{{ session('info') }}</span>
    </div>
    @endif

    @if(session('success'))
    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center gap-2.5 font-semibold shadow-2xs">
        <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600 shrink-0"></i>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs space-y-1 shadow-2xs">
        <div class="flex items-center gap-2 font-bold">
            <i data-lucide="alert-triangle" class="w-4 h-4 shrink-0"></i>
            <span>Terdapat kendala:</span>
        </div>
        <ul class="list-disc pl-5">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Main Xendit-Style 2-Column Checkout Container -->
    <div class="rounded-3xl border border-gray-200 bg-white shadow-sm overflow-hidden grid grid-cols-1 lg:grid-cols-12">
        
        <!-- ============================================== -->
        <!-- LEFT PANEL (Col-span 7): PAYMENT INTERACTION   -->
        <!-- ============================================== -->
        <div class="lg:col-span-7 p-6 sm:p-8 space-y-6">
            
            <!-- Merchant Header & Total Amount (Xendit Style) -->
            <div class="space-y-4 pb-6 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-9 h-9 rounded-xl bg-[#1768B5] text-white flex items-center justify-center font-black text-sm shadow-xs">
                            A
                        </div>
                        <div>
                            <h2 class="text-xs font-bold text-gray-500 uppercase tracking-wider">Aether Edukasi</h2>
                            <p class="text-[11px] text-gray-400">Order ID: <span class="font-mono font-bold text-gray-700">{{ $transaksi->nomor_registrasi }}</span></p>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                        <span>Menunggu Pembayaran</span>
                    </span>
                </div>

                <!-- Big Amount & Countdown -->
                <div class="bg-slate-50/80 rounded-2xl p-5 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-[11px] uppercase tracking-wider text-gray-500 font-bold block mb-0.5">Total Pembayaran</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">
                                Rp {{ number_format($transaksi->total_bayar ?: ($transaksi->biaya + 5000), 0, ',', '.') }}
                            </span>
                            <button type="button" onclick="copyToClipboard('{{ $transaksi->total_bayar ?: ($transaksi->biaya + 5000) }}', 'copy-total-btn')" id="copy-total-btn" class="text-xs font-bold text-[#1768B5] hover:underline flex items-center gap-1">
                                <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                <span>Salin</span>
                            </button>
                        </div>
                    </div>

                    <!-- Countdown Timer -->
                    <div class="text-left sm:text-right">
                        <span class="text-[11px] text-gray-500 block">Bayar sebelum:</span>
                        <div class="text-xs font-bold text-rose-600 flex items-center sm:justify-end gap-1.5 mt-0.5" id="countdown-box">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                            <span id="countdown-timer">23:59:59</span>
                        </div>
                    </div>
                </div>
            </div>

            @php
                $isQris = str_contains(strtoupper($transaksi->metode_pembayaran ?? ''), 'QRIS');
                $isTransferManual = str_contains(strtoupper($transaksi->metode_pembayaran ?? ''), 'TRANSFER');
                $isVA = !$isQris && !$isTransferManual;
                $activeMethod = $transaksi->metode_pembayaran ?? 'QRIS Instant';
            @endphp

            <!-- ============================================== -->
            <!-- SECTION: METODE PEMBAYARAN AKTIF & INSTRUKSI  -->
            <!-- ============================================== -->
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-black uppercase tracking-wider text-gray-700">
                        Metode Pembayaran Dipilih:
                    </label>
                    <button type="button" onclick="togglePaymentMethodsList()" id="btn-toggle-methods" class="text-xs font-bold text-[#1768B5] hover:text-[#102654] transition flex items-center gap-1">
                        <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                        <span>Ganti Metode</span>
                    </button>
                </div>

                <!-- 1. ACTIVE PAYMENT CARD DETAIL -->
                <div class="rounded-2xl border-2 border-[#1768B5]/20 bg-white p-5 shadow-xs space-y-4" id="active-method-card">
                    <!-- Header Active Method -->
                    <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            @if($isQris)
                                <div class="w-10 h-10 rounded-xl bg-rose-500 text-white font-black text-xs flex items-center justify-center shadow-2xs">QRIS</div>
                            @elseif(str_contains(strtoupper($activeMethod), 'BCA'))
                                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white font-black text-xs flex items-center justify-center shadow-2xs">BCA</div>
                            @elseif(str_contains(strtoupper($activeMethod), 'MANDIRI'))
                                <div class="w-10 h-10 rounded-xl bg-amber-600 text-white font-black text-xs flex items-center justify-center shadow-2xs">MDR</div>
                            @elseif(str_contains(strtoupper($activeMethod), 'BNI'))
                                <div class="w-10 h-10 rounded-xl bg-teal-600 text-white font-black text-xs flex items-center justify-center shadow-2xs">BNI</div>
                            @elseif(str_contains(strtoupper($activeMethod), 'BRI'))
                                <div class="w-10 h-10 rounded-xl bg-sky-600 text-white font-black text-xs flex items-center justify-center shadow-2xs">BRI</div>
                            @else
                                <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white font-black text-xs flex items-center justify-center shadow-2xs">BANK</div>
                            @endif

                            <div>
                                <h3 class="text-sm font-bold text-gray-900">{{ $activeMethod }}</h3>
                                <p class="text-[11px] text-gray-500">
                                    @if($isQris)
                                        Scan kode QR menggunakan aplikasi e-wallet atau mobile banking
                                    @elseif($isTransferManual)
                                        Transfer manual ke rekening resmi Aether
                                    @else
                                        Transfer via Virtual Account otomatis 24 jam
                                    @endif
                                </p>
                            </div>
                        </div>

                        <span class="w-6 h-6 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                            <i data-lucide="check" class="w-3.5 h-3.5"></i>
                        </span>
                    </div>

                    <!-- KONTEN A: JIKA QRIS -->
                    @if($isQris)
                    <div class="flex flex-col sm:flex-row items-center gap-6 py-2">
                        <!-- QR Graphic Container -->
                        <div class="relative w-44 h-44 bg-white p-3 rounded-2xl border-2 border-dashed border-gray-300 flex flex-col items-center justify-center shadow-inner shrink-0 group">
                            <svg class="w-full h-full text-gray-900" viewBox="0 0 100 100" fill="currentColor">
                                <rect x="5" y="5" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="5"/>
                                <rect x="12" y="12" width="12" height="12" fill="currentColor"/>
                                <rect x="69" y="5" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="5"/>
                                <rect x="76" y="12" width="12" height="12" fill="currentColor"/>
                                <rect x="5" y="69" width="26" height="26" rx="4" fill="none" stroke="currentColor" stroke-width="5"/>
                                <rect x="12" y="76" width="12" height="12" fill="currentColor"/>
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
                                <span class="text-gray-400 font-semibold block text-[11px]">Merchant Resmi:</span>
                                <p class="font-bold text-gray-900 text-sm">AETHER EDUKASI INDONESIA</p>
                                <p class="text-[11px] text-gray-400 font-mono">NMID: ID1024392819203</p>
                            </div>

                            <div class="p-3 rounded-xl bg-gray-50 text-[11px] text-gray-600 space-y-1">
                                <p class="font-bold text-gray-800">Cara Bayar via QRIS:</p>
                                <p>1. Buka aplikasi e-Wallet (GoPay, OVO, DANA, ShopeePay) atau m-Banking Anda.</p>
                                <p>2. Pilih menu <strong>Scan / Bayar QRIS</strong> dan scan barcode di samping.</p>
                                <p>3. Masukkan nominal tagihan tepat <strong>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</strong>.</p>
                                <p>4. Simpan screenshot bukti transfer untuk diupload di bawah.</p>
                            </div>
                        </div>
                    </div>

                    <!-- KONTEN B: JIKA VIRTUAL ACCOUNT ATAU TRANSFER BANK -->
                    @else
                    <div class="space-y-3 py-1">
                        <!-- Nomor VA / Rekening Box -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                            <div>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider block">
                                    {{ $isTransferManual ? 'Nomor Rekening Tujuan:' : 'Nomor Virtual Account:' }}
                                </span>
                                <span id="payment-code-text" class="text-xl sm:text-2xl font-mono font-black text-gray-900 tracking-wider">
                                    {{ $transaksi->kode_pembayaran ?? '80777123456789' }}
                                </span>
                                @if($isTransferManual)
                                <span class="text-[11px] text-gray-500 block mt-0.5">Atas Nama: <strong class="text-gray-800">PT Aether Edukasi Indonesia</strong></span>
                                @endif
                            </div>
                            <button type="button" onclick="copyToClipboard('{{ $transaksi->kode_pembayaran }}', 'btn-copy-code')" id="btn-copy-code" class="px-4 py-2.5 rounded-xl bg-[#1768B5] hover:bg-[#102654] text-white text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-xs cursor-pointer shrink-0">
                                <i data-lucide="copy" class="w-4 h-4"></i>
                                <span>Salin Nomor</span>
                            </button>
                        </div>

                        <!-- Instructions Accordion -->
                        <div class="pt-2 text-xs space-y-2">
                            <details class="group rounded-xl border border-gray-100 bg-gray-50/50 p-3" open>
                                <summary class="font-bold text-gray-800 cursor-pointer flex items-center justify-between text-xs">
                                    <span>Petunjuk Pembayaran (Mobile Banking &amp; ATM)</span>
                                    <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 group-open:rotate-180 transition-transform"></i>
                                </summary>
                                <ol class="mt-2.5 text-[11px] text-gray-600 space-y-1.5 list-decimal pl-4">
                                    <li>Buka aplikasi m-Banking atau kunjungi ATM terdekat.</li>
                                    <li>Pilih menu <strong>Transfer &gt; {{ $isTransferManual ? 'Transfer Antar Bank' : 'Virtual Account' }}</strong>.</li>
                                    <li>Masukkan nomor <strong>{{ $transaksi->kode_pembayaran }}</strong> di atas.</li>
                                    <li>Pastikan nominal transfer tepat <strong>Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</strong>.</li>
                                    <li>Selesaikan pembayaran dan simpan struk transfer Anda.</li>
                                </ol>
                            </details>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- 2. PAYMENT METHODS LIST (EXPANDABLE - XENDIT STYLE) -->
                <div id="payment-methods-picker" class="hidden rounded-2xl border border-gray-200 bg-white p-5 shadow-xs space-y-4">
                    <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                        <p class="text-xs font-bold text-gray-700">Pilih Metode Pembayaran Lain:</p>
                        <button type="button" onclick="togglePaymentMethodsList()" class="text-xs text-gray-400 hover:text-gray-600">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div class="space-y-2">
                        @php
                            $channelOptions = [
                                ['name' => 'QRIS Instant', 'category' => 'Semua E-Wallet / Mobile Banking', 'badge' => 'QRIS', 'color' => 'bg-rose-50 text-rose-700'],
                                ['name' => 'BCA Virtual Account', 'category' => 'Virtual Account', 'badge' => 'BCA', 'color' => 'bg-blue-50 text-blue-700'],
                                ['name' => 'Mandiri Virtual Account', 'category' => 'Virtual Account', 'badge' => 'MDR', 'color' => 'bg-amber-50 text-amber-700'],
                                ['name' => 'BNI Virtual Account', 'category' => 'Virtual Account', 'badge' => 'BNI', 'color' => 'bg-teal-50 text-teal-700'],
                                ['name' => 'BRI Virtual Account', 'category' => 'Virtual Account', 'badge' => 'BRI', 'color' => 'bg-sky-50 text-sky-700'],
                                ['name' => 'Transfer Bank BCA', 'category' => 'Transfer Manual', 'badge' => 'TF', 'color' => 'bg-indigo-50 text-indigo-700'],
                                ['name' => 'Transfer Bank Mandiri', 'category' => 'Transfer Manual', 'badge' => 'TF', 'color' => 'bg-amber-50 text-amber-700'],
                            ];
                        @endphp

                        @foreach($channelOptions as $ch)
                        <form action="{{ route('pelatihan.payment.ganti-metode', $transaksi->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="metode_pembayaran" value="{{ $ch['name'] }}">
                            <button type="submit" class="w-full text-left p-3 rounded-xl border {{ $activeMethod === $ch['name'] ? 'border-[#1768B5] bg-sky-50/40' : 'border-gray-200 bg-white hover:border-gray-300' }} transition flex items-center justify-between group">
                                <div class="flex items-center gap-3">
                                    <span class="w-8 h-8 rounded-lg {{ $ch['color'] }} font-black text-xs flex items-center justify-center border border-gray-100">
                                        {{ $ch['badge'] }}
                                    </span>
                                    <div>
                                        <p class="text-xs font-bold text-gray-900 group-hover:text-[#1768B5] transition">{{ $ch['name'] }}</p>
                                        <p class="text-[10px] text-gray-400">{{ $ch['category'] }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if($activeMethod === $ch['name'])
                                        <span class="text-[10px] font-bold text-[#1768B5] bg-sky-100 px-2 py-0.5 rounded-full">Aktif</span>
                                    @else
                                        <span class="text-xs text-gray-400 group-hover:text-[#1768B5] transition">Pilih →</span>
                                    @endif
                                </div>
                            </button>
                        </form>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- ============================================== -->
            <!-- SECTION: UPLOAD BUKTI TRANSFER (XENDIT STYLE)  -->
            <!-- ============================================== -->
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-2.5 pb-3 border-b border-gray-100">
                    <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center">
                        <i data-lucide="upload-cloud" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-xs sm:text-sm font-bold text-gray-900">Konfirmasi &amp; Upload Bukti Pembayaran</h3>
                        <p class="text-[11px] text-gray-500">Unggah foto struk atau screenshot bukti transfer Anda di bawah ini</p>
                    </div>
                </div>

                <form action="{{ route('pelatihan.payment.upload-bukti', $transaksi->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="flex flex-col sm:flex-row items-center gap-4">
                        <!-- Preview Box -->
                        <div id="receipt-preview-wrapper" class="relative w-full sm:w-36 h-28 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 flex flex-col items-center justify-center overflow-hidden shrink-0 group transition hover:border-emerald-400">
                            <img id="receipt-preview-img" src="" alt="Bukti Pembayaran" class="w-full h-full object-cover hidden">
                            <div id="receipt-placeholder" class="flex flex-col items-center justify-center p-2 text-center text-gray-400">
                                <i data-lucide="image" class="w-6 h-6 text-emerald-500 mb-1"></i>
                                <span class="text-[10px] font-bold text-gray-600">Foto Bukti</span>
                            </div>
                        </div>

                        <!-- File Input Control -->
                        <div class="flex-1 w-full space-y-2">
                            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran" required accept="image/*" onchange="previewReceiptImage(event)" class="w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition cursor-pointer">
                            <p class="text-[10px] text-gray-400">Format: JPG, JPEG, PNG, WEBP (Maksimal 3 MB).</p>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-gray-600 mb-1">Catatan Tambahan (Opsional):</label>
                        <input type="text" name="catatan" placeholder="Contoh: Transfer via rekening an Budi" class="w-full px-3.5 py-2 rounded-xl border border-gray-200 text-xs focus:outline-none focus:ring-2 focus:ring-[#1768B5]/20 focus:border-[#1768B5]">
                    </div>

                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md transition flex items-center justify-center gap-2 cursor-pointer group">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        <span>Saya Sudah Membayar &amp; Kirim Bukti</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- ============================================== -->
        <!-- RIGHT PANEL (Col-span 5): ORDER SUMMARY (XENDIT)-->
        <!-- ============================================== -->
        <aside class="lg:col-span-5 xendit-aside p-6 sm:p-8 border-t lg:border-t-0 lg:border-l border-gray-200 flex flex-col justify-between space-y-6">
            <div class="space-y-6">
                <!-- Header -->
                <div class="pb-3 border-b border-gray-200/80">
                    <h3 class="text-xs font-black uppercase tracking-wider text-gray-500">Ringkasan Pesanan</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Detail pendaftaran workshop</p>
                </div>

                <!-- Customer Details -->
                <div class="rounded-2xl border border-gray-200/80 bg-white p-4 space-y-2 text-xs">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Data Pemesan:</span>
                    <div class="space-y-1 text-gray-700">
                        <p class="font-bold text-gray-900">{{ $transaksi->nama_pemesan }}</p>
                        <p class="text-[11px] text-gray-500">{{ $transaksi->email_pemesan }}</p>
                        <p class="text-[11px] text-gray-500">{{ $transaksi->telepon_pemesan }}</p>
                        @if(!empty($transaksi->universitas))
                        <p class="text-[11px] text-gray-500">{{ $transaksi->universitas }}</p>
                        @endif
                    </div>
                </div>

                <!-- Item Breakdown -->
                <div class="rounded-2xl border border-gray-200/80 bg-white p-4 space-y-3 text-xs">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Item Pelatihan:</span>
                    
                    <div class="flex items-start justify-between gap-3">
                        <div class="space-y-0.5">
                            <h4 class="font-bold text-gray-900 leading-snug">{{ $transaksi->judul_kelas }}</h4>
                            <p class="text-[11px] text-gray-500">1x Workshop &amp; Modul Praktik</p>
                        </div>
                        <span class="font-bold text-gray-900 shrink-0">
                            Rp {{ number_format($transaksi->biaya ?? 200000, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between text-gray-600 text-[11px]">
                        <span>Biaya Layanan Admin</span>
                        <span class="font-bold text-emerald-600">+ Rp {{ number_format($transaksi->admin_fee ?: 5000, 0, ',', '.') }}</span>
                    </div>

                    <div class="pt-2.5 border-t border-gray-200 flex items-center justify-between">
                        <span class="font-bold text-gray-900 text-xs">Total Pembayaran</span>
                        <span class="font-black text-gray-900 text-base text-emerald-600">
                            Rp {{ number_format($transaksi->total_bayar ?: ($transaksi->biaya + 5000), 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <!-- Invoice Link -->
                <div class="text-center">
                    <a href="{{ route('pelatihan.payment.invoice', $transaksi->id) }}" target="_blank" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#1768B5] hover:underline">
                        <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                        <span>Lihat Faktur / Invoice Tagihan</span>
                    </a>
                </div>
            </div>

            <!-- Footer Trust Info -->
            <div class="pt-6 border-t border-gray-200/80 text-center space-y-2">
                <div class="flex items-center justify-center gap-1.5 text-[11px] text-gray-500 font-medium">
                    <i data-lucide="lock" class="w-3.5 h-3.5 text-emerald-600"></i>
                    <span>Checkout Resmi &amp; Terverifikasi Aether</span>
                </div>
                <p class="text-[10px] text-gray-400">Hubungi bantuan support kami jika mengalami kendala dalam pembayaran.</p>
            </div>
        </aside>
    </div>
</div>

<!-- Toast Salin -->
<div id="copy-toast" class="fixed bottom-6 right-6 z-50 transform transition-all duration-300 opacity-0 pointer-events-none translate-y-2">
    <div class="bg-gray-900 text-white px-4 py-2.5 rounded-xl shadow-xl flex items-center gap-2 text-xs font-bold">
        <i data-lucide="check" class="w-4 h-4 text-emerald-400"></i>
        <span id="copy-toast-msg">Berhasil disalin!</span>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePaymentMethodsList() {
        const picker = document.getElementById('payment-methods-picker');
        picker.classList.toggle('hidden');
        if (!picker.classList.contains('hidden')) {
            picker.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
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
        toastMsg.textContent = 'Berhasil disalin: ' .concat(text);
        toast.classList.remove('opacity-0', 'pointer-events-none', 'translate-y-2');
        toast.classList.add('opacity-100', 'translate-y-0');

        setTimeout(function() {
            toast.classList.remove('opacity-100', 'translate-y-0');
            toast.classList.add('opacity-0', 'pointer-events-none', 'translate-y-2');
        }, 2200);
    }

    function previewReceiptImage(event) {
        const input = event.target;
        const previewImg = document.getElementById('receipt-preview-img');
        const placeholder = document.getElementById('receipt-placeholder');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.classList.remove('hidden');
                placeholder.classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Countdown Timer 24 hours simulation
    (function initCountdown() {
        const timerEl = document.getElementById('countdown-timer');
        if (!timerEl) return;

        let totalSeconds = 23 * 3600 + 59 * 60 + 59;
        setInterval(function() {
            if (totalSeconds <= 0) return;
            totalSeconds--;
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            timerEl.textContent = 
                String(hours).padStart(2, '0') + ':' + 
                String(minutes).padStart(2, '0') + ':' + 
                String(seconds).padStart(2, '0');
        }, 1000);
    })();
</script>
@endpush
