<?php

namespace Modules\Bimbingan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Bimbingan\Entities\TransaksiBimbingan;
use Modules\Bimbingan\Entities\TransaksiProgram;
use Modules\Master\Entities\MasterMentor;
use Modules\Master\Entities\MasterProgram;

class BimbinganController extends Controller
{
    public function index()
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $query = TransaksiBimbingan::with(['program', 'mentor'])
            ->orderBy('id', 'desc');

        if ($isRole99) {
            $query->where('user_id', $userId);
        }

        $dbSessions = $query->take(20)->get();

        $statsQuery = TransaksiBimbingan::query();
        if ($isRole99) {
            $statsQuery->where('user_id', $userId);
        }

        $totalCount = (clone $statsQuery)->count();
        $aktifCount = (clone $statsQuery)->whereIn('status', ['Menunggu Review', 'Dijadwalkan'])->count();
        $selesaiCount = (clone $statsQuery)->where('status', 'Selesai')->count();
        $mendatangCount = (clone $statsQuery)->where('status', 'Dijadwalkan')->count();

        $stats = [
            'total'            => $isRole99 ? $totalCount : max($totalCount, 5),
            'aktif'            => $isRole99 ? $aktifCount : max($aktifCount, 2),
            'selesai'          => $isRole99 ? $selesaiCount : max($selesaiCount, 3),
            'jadwal_mendatang' => $isRole99 ? $mendatangCount : max($mendatangCount, 1),
        ];

        // Format sessions for view
        $sessions = [];
        if ($dbSessions->isNotEmpty()) {
            foreach ($dbSessions as $item) {
                $sessions[] = [
                    'id'      => $item->id,
                    'judul'   => $item->judul_skripsi,
                    'program' => $item->paket_bimbingan ?: ($item->program->nama_program ?? 'Bimbingan Skripsi'),
                    'mentor'  => $item->mentor_id ? ($item->mentor ? $item->mentor->nama_lengkap : 'Mentor Spesialis') : 'Tim Konsultan Aether',
                    'bab'     => $item->topik_bidang ?: 'Konsultasi Riset',
                    'tanggal' => $item->jadwal_konsultasi ? $item->jadwal_konsultasi->translatedFormat('d M Y, H:i') . ' WIB' : 'Menunggu Penjadwalan',
                    'media'   => $item->link_zoom ? 'Google Meet (Online)' : 'Online Video Meeting',
                    'status'  => $item->status,
                    'biaya'   => $item->biaya ? 'Rp ' . number_format($item->biaya, 0, ',', '.') : '-',
                    'catatan' => $item->keluhan_kendala ?: 'Fokus telaah materi dan pembahasan revisi.'
                ];
            }
        } elseif (!$isRole99 && TransaksiBimbingan::count() === 0) {
            $sessions = [
                [
                    'id' => 1,
                    'judul' => 'Pengaruh E-WOM dan Brand Image Terhadap Keputusan Pembelian Generasi Z',
                    'program' => 'Pendampingan Skripsi S1 (Kuantitatif & Bab 4-5)',
                    'mentor' => 'Nur Atika Rochmah, M.Si.',
                    'bab' => 'Bab 4 - Hasil & Pembahasan',
                    'tanggal' => '10 Okt 2026, 14:00 WIB',
                    'media' => 'Google Meet (Online)',
                    'status' => 'Dijadwalkan',
                    'biaya' => 'Rp 650.000',
                    'catatan' => 'Fokus pembahasan pada interpretasi koefisien jalur dan uji efek mediasi.'
                ],
                [
                    'id' => 2,
                    'judul' => 'Analisis Volatilitas Saham Sektor Perbankan Menggunakan Metode GARCH',
                    'program' => 'Bimbingan Tesis Magister (S2 SEM-PLS & Ekonometrika)',
                    'mentor' => 'Deny Aditya Pratama, S.Stat.',
                    'bab' => 'Bab 3 - Metodologi Riset',
                    'tanggal' => '12 Okt 2026, 19:30 WIB',
                    'media' => 'Zoom Meeting',
                    'status' => 'Menunggu Konfirmasi',
                    'biaya' => 'Rp 1.850.000',
                    'catatan' => 'Pemeriksaan stasioneritas data runtut waktu dan spesifikasi model estimasi.'
                ],
                [
                    'id' => 3,
                    'judul' => 'Studi Fenomenologi Pengalaman UMKM dalam Digitalisasi Keuangan',
                    'program' => 'Paket Bimbingan Skripsi Komprehensif (Full Bab 1 s/d Bab 5)',
                    'mentor' => 'Alessandra Natasya Panie, S.Psi.',
                    'bab' => 'Bab 4 & Coding NVivo',
                    'tanggal' => '05 Okt 2026, 10:00 WIB',
                    'media' => 'Google Meet',
                    'status' => 'Selesai',
                    'biaya' => 'Rp 1.500.000',
                    'catatan' => 'Triangulasi data dan validasi coding tree antar informan tuntas.'
                ]
            ];
        }

        $userActivePrograms = collect();
        if ($isRole99 && $userId) {
            $userActivePrograms = TransaksiProgram::with('program')
                ->where('user_id', $userId)
                ->where('status', 'Aktif')
                ->get();
        }
        $hasActiveProgram = !$isRole99 || $userActivePrograms->isNotEmpty();

        return view('bimbingan::index', compact('stats', 'sessions', 'isRole99', 'userActivePrograms', 'hasActiveProgram'));
    }

    public function programs(Request $request)
    {
        $query = MasterProgram::where('is_active', true);

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $programs = $query->orderBy('is_populer', 'desc')
            ->orderBy('id', 'asc')
            ->get();

        $userPurchasedProgramIds = [];
        if (Auth::check()) {
            $userPurchasedProgramIds = TransaksiProgram::where('user_id', Auth::id())
                ->where('status', 'Aktif')
                ->pluck('program_id')
                ->toArray();
        }

        return view('bimbingan::programs', compact('programs', 'userPurchasedProgramIds'));
    }

    public function payment($id)
    {
        $program = MasterProgram::where('is_active', true)->findOrFail($id);
        $user = Auth::user();

        $alreadyPurchased = false;
        if ($user) {
            $userId = $user->id_user ?? $user->id;
            $alreadyPurchased = TransaksiProgram::where('user_id', $userId)
                ->where('program_id', $program->id)
                ->where('status', 'Aktif')
                ->exists();
        }

        $adminPrice = 5000;
        $totalPrice = $program->biaya_standar + $adminPrice;

        return view('bimbingan::payment', compact('program', 'user', 'alreadyPurchased', 'adminPrice', 'totalPrice'));
    }

    public function processPayment(Request $request, $id)
    {
        $program = MasterProgram::where('is_active', true)->findOrFail($id);
        $user = Auth::user();

        $validated = $request->validate([
            'nama_pemesan'      => 'required|string|max:150',
            'email_pemesan'     => 'required|email|max:150',
            'telepon_pemesan'   => 'required|string|max:30',
            'universitas'       => 'nullable|string|max:150',
            'judul_skripsi'     => 'nullable|string|max:255',
            'metode_pembayaran' => 'required|string|max:100',
        ], [
            'nama_pemesan.required'      => 'Nama lengkap wajib diisi.',
            'email_pemesan.required'     => 'Email aktif wajib diisi.',
            'email_pemesan.email'        => 'Format email tidak valid.',
            'telepon_pemesan.required'   => 'Nomor WhatsApp wajib diisi.',
            'metode_pembayaran.required' => 'Pilih salah satu metode pembayaran.',
        ]);

        $userId = $user ? ($user->id_user ?? $user->id) : null;
        $adminPrice = 5000;
        $totalBayar = $program->biaya_standar + $adminPrice;
        $nomorTransaksi = 'TRX-PRG-' . date('Ym') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $paymentCode = match ($validated['metode_pembayaran']) {
            'BCA Virtual Account'     => '80777' . rand(10000000, 99999999),
            'Mandiri Virtual Account' => '88908' . rand(10000000, 99999999),
            'BNI Virtual Account'     => '98801' . rand(10000000, 99999999),
            'BRI Virtual Account'     => '12800' . rand(10000000, 99999999),
            'QRIS Instant'            => 'QRIS-' . strtoupper(Str::random(10)),
            'Transfer Bank BCA'       => '7310889201', // No Rekening BCA PT Aether Academy
            'Transfer Bank Mandiri'   => '1370019283741', // No Rekening Mandiri PT Aether Academy
            'Transfer Bank (Manual)'  => '7310889201', // Default BCA
            default                   => 'PAY-' . strtoupper(Str::random(10)),
        };

        $transaksi = TransaksiProgram::create([
            'nomor_transaksi'   => $nomorTransaksi,
            'user_id'           => $userId,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => $validated['nama_pemesan'],
            'email_pemesan'     => $validated['email_pemesan'],
            'telepon_pemesan'   => $validated['telepon_pemesan'],
            'universitas'       => $validated['universitas'],
            'judul_skripsi'     => $validated['judul_skripsi'],
            'biaya'             => $program->biaya_standar,
            'admin_fee'         => $adminPrice,
            'total_bayar'       => $totalBayar,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'kode_pembayaran'   => $paymentCode,
            'total_sesi'        => $program->jumlah_sesi ?: 1,
            'sisa_sesi'         => $program->jumlah_sesi ?: 1,
            'sesi_digunakan'    => 0,
            'status'            => 'Menunggu Pembayaran',
            'status_pembayaran' => 'Menunggu Pembayaran',
            'tanggal_pembelian' => now(),
        ]);

        return redirect()->route('bimbingan.payment.instruction', $transaksi->id)
            ->with('info', 'Pesanan berhasil dibuat. Silakan selesaikan pembayaran dan upload bukti transfer Anda.');
    }

    public function paymentInstruction($id)
    {
        $transaksi = TransaksiProgram::with('program')->findOrFail($id);
        return view('bimbingan::instruction', compact('transaksi'));
    }

    public function gantiMetodeBayar(Request $request, $id)
    {
        $oldTransaksi = TransaksiProgram::with('program')->findOrFail($id);

        $validated = $request->validate([
            'metode_pembayaran' => 'required|string|max:100',
        ], [
            'metode_pembayaran.required' => 'Pilih salah satu metode pembayaran baru.',
        ]);

        // 1. Batalkan transaksi lama
        $oldTransaksi->status_pembayaran = 'Dibatalkan';
        $oldTransaksi->status = 'Dibatalkan';
        $oldTransaksi->catatan = 'Dibatalkan oleh pemesan karena ganti metode bayar ke ' . $validated['metode_pembayaran'];
        $oldTransaksi->save();

        // 2. Generate kode bayar untuk metode baru
        $paymentCode = match ($validated['metode_pembayaran']) {
            'BCA Virtual Account'     => '80777' . rand(10000000, 99999999),
            'Mandiri Virtual Account' => '88908' . rand(10000000, 99999999),
            'BNI Virtual Account'     => '98801' . rand(10000000, 99999999),
            'BRI Virtual Account'     => '12800' . rand(10000000, 99999999),
            'QRIS Instant'            => 'QRIS-' . strtoupper(Str::random(10)),
            'Transfer Bank BCA'       => '7310889201', // No Rekening BCA PT Aether Academy
            'Transfer Bank Mandiri'   => '1370019283741', // No Rekening Mandiri PT Aether Academy
            'Transfer Bank (Manual)'  => '7310889201',
            default                   => 'PAY-' . strtoupper(Str::random(10)),
        };

        // 3. Generate transaksi baru
        $newNomorTransaksi = 'TRX-PRG-' . date('Ym') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $newTransaksi = TransaksiProgram::create([
            'nomor_transaksi'   => $newNomorTransaksi,
            'user_id'           => $oldTransaksi->user_id,
            'program_id'        => $oldTransaksi->program_id,
            'nama_program'      => $oldTransaksi->nama_program,
            'nama_pemesan'      => $oldTransaksi->nama_pemesan,
            'email_pemesan'     => $oldTransaksi->email_pemesan,
            'telepon_pemesan'   => $oldTransaksi->telepon_pemesan,
            'universitas'       => $oldTransaksi->universitas,
            'judul_skripsi'     => $oldTransaksi->judul_skripsi,
            'biaya'             => $oldTransaksi->biaya,
            'admin_fee'         => $oldTransaksi->admin_fee,
            'total_bayar'       => $oldTransaksi->total_bayar,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'kode_pembayaran'   => $paymentCode,
            'total_sesi'        => $oldTransaksi->total_sesi,
            'sisa_sesi'         => $oldTransaksi->total_sesi,
            'sesi_digunakan'    => 0,
            'status'            => 'Menunggu Pembayaran',
            'status_pembayaran' => 'Menunggu Pembayaran',
            'tanggal_pembelian' => now(),
            'catatan'           => 'Pergantian metode dari #' . $oldTransaksi->nomor_transaksi,
        ]);

        return redirect()->route('bimbingan.payment.instruction', $newTransaksi->id)
            ->with('info', "Metode pembayaran berhasil diubah ke {$validated['metode_pembayaran']}. Tagihan lama (#{$oldTransaksi->nomor_transaksi}) telah dibatalkan.");
    }

    public function uploadBuktiPembayaran(Request $request, $id)
    {
        $transaksi = TransaksiProgram::with('program')->findOrFail($id);

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'catatan'          => 'nullable|string|max:255',
        ], [
            'bukti_pembayaran.required' => 'File bukti pembayaran wajib diupload.',
            'bukti_pembayaran.image'    => 'Bukti pembayaran harus berupa gambar foto/screenshot.',
            'bukti_pembayaran.mimes'    => 'Format file yang didukung: JPG, PNG, WEBP.',
            'bukti_pembayaran.max'      => 'Ukuran file maksimal 3 MB.',
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = 'bukti_' . str_replace(['/', '\\', ' '], '_', $transaksi->nomor_transaksi) . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('bukti_pembayaran', $filename, 'public');
            $transaksi->bukti_pembayaran = $path;
        }

        if ($request->filled('catatan')) {
            $transaksi->catatan = $request->catatan;
        }

        $transaksi->status_pembayaran = 'Menunggu Verifikasi';
        $transaksi->status = 'Menunggu Verifikasi';
        $transaksi->tanggal_pembelian = now();
        $transaksi->save();

        return redirect()->route('bimbingan.payment.invoice', $transaksi->id)
            ->with('success', 'Bukti pembayaran berhasil diupload! Tim admin kami akan segera memverifikasi pembayaran Anda.');
    }

    public function paymentSuccess($id)
    {
        $transaksi = TransaksiProgram::with('program')->findOrFail($id);
        return view('bimbingan::invoice', compact('transaksi'));
    }

    public function beliProgram($id)
    {
        $program = MasterProgram::where('is_active', true)->findOrFail($id);
        $user = Auth::user();
        $userId = $user ? ($user->id_user ?? $user->id) : null;

        TransaksiProgram::create([
            'nomor_transaksi'   => 'TRX-PRG-' . date('Ym') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
            'user_id'           => $userId,
            'program_id'        => $program->id,
            'nama_program'      => $program->nama_program,
            'nama_pemesan'      => $user->nm_user ?? 'Mahasiswa',
            'email_pemesan'     => $user->email ?? null,
            'telepon_pemesan'   => $user->telepon ?? null,
            'biaya'             => $program->biaya_standar,
            'admin_fee'         => 0,
            'total_bayar'       => $program->biaya_standar,
            'total_sesi'        => $program->jumlah_sesi ?: 1,
            'sisa_sesi'         => $program->jumlah_sesi ?: 1,
            'sesi_digunakan'    => 0,
            'status'            => 'Aktif',
            'status_pembayaran' => 'Lunas',
            'tanggal_pembelian' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Paket program ' . $program->nama_program . ' berhasil diaktifkan!');
    }

    public function create(Request $request)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        if ($isRole99) {
            $userActivePrograms = TransaksiProgram::with('program')
                ->where('user_id', $userId)
                ->where('status', 'Aktif')
                ->get();

            if ($userActivePrograms->isEmpty()) {
                return redirect()->route('bimbingan.programs')->with('warning', 'Anda belum memiliki paket program bimbingan yang dibeli. Silakan pilih dan beli program terlebih dahulu untuk mengajukan sesi.');
            }

            $purchasedProgramIds = $userActivePrograms->pluck('program_id')->toArray();
            $programs = MasterProgram::where('is_active', true)
                ->whereIn('id', $purchasedProgramIds)
                ->orderBy('is_populer', 'desc')
                ->orderBy('id', 'asc')
                ->get();
        } else {
            $programs = MasterProgram::where('is_active', true)
                ->orderBy('is_populer', 'desc')
                ->orderBy('id', 'asc')
                ->get();
        }

        $selectedProgram = null;
        if ($request->filled('program_id')) {
            $selectedProgram = $programs->find($request->program_id);
        }
        if (!$selectedProgram && $programs->isNotEmpty()) {
            $selectedProgram = $programs->first();
        }

        $mentorList = MasterMentor::where('is_active', true)->get();
        $mentors = [];
        if ($mentorList->isNotEmpty()) {
            foreach ($mentorList as $m) {
                $mentors[] = trim($m->nama_lengkap . ($m->gelar ? ', ' . $m->gelar : '') . ($m->spesialisasi ? ' (' . $m->spesialisasi . ')' : ''));
            }
        } else {
            $mentors = [
                'Nur Atika Rochmah, M.Si. (Spesialis Kuantitatif & SEM-PLS)',
                'Deny Aditya Pratama, S.Stat. (Spesialis Ekonometrika & Regresi Panel)',
                'Alessandra Natasya Panie, S.Psi. (Spesialis Kualitatif & NVivo)',
                'Arrum Rochmani Hidayah, M.Kom. (Spesialis Machine Learning & Big Data)',
            ];
        }

        return view('bimbingan::create', compact('programs', 'selectedProgram', 'mentors'));
    }

    public function store(Request $request)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        if ($isRole99) {
            if (!$request->filled('program_id')) {
                return redirect()->back()->withInput()->with('error', 'Silakan pilih paket program bimbingan yang telah Anda beli.');
            }

            $purchasedProgram = TransaksiProgram::where('user_id', $userId)
                ->where('program_id', $request->program_id)
                ->where('status', 'Aktif')
                ->first();

            if (!$purchasedProgram) {
                return redirect()->back()->withInput()->with('error', 'Pengajuan sesi gagal. Anda belum memiliki paket program bimbingan yang aktif untuk program ini.');
            }
        }

        $validated = $request->validate([
            'program_id' => 'nullable|exists:master_programs,id',
            'paket_bimbingan' => 'nullable|string|max:150',
            'judul_penelitian' => 'required|string|max:255',
            'topik_bab' => 'required|string|max:150',
            'mentor' => 'required|string',
            'tanggal_preferensi' => 'required|date',
            'media' => 'nullable|string|max:50',
            'catatan' => 'nullable|string',
        ]);

        $program = null;
        if ($request->filled('program_id')) {
            $program = MasterProgram::find($request->program_id);
        }

        $biaya = $program ? $program->biaya_standar : 650000;
        $namaProgram = $program ? $program->nama_program : ($request->paket_bimbingan ?: 'Pendampingan Skripsi');

        $user = Auth::user();
        $namaMahasiswa = $user ? ($user->nm_user ?? $user->name ?? 'Mahasiswa') : 'Mahasiswa Riset';

        $nomorTiket = 'BMB-' . date('Ym') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);

        $mentorId = null;
        if ($request->filled('mentor_id')) {
            $mentorId = $request->mentor_id;
        } elseif (!empty($validated['mentor'])) {
            $mentorClean = trim(explode('(', explode(',', $validated['mentor'])[0])[0]);
            $foundMentor = MasterMentor::where('nama_lengkap', 'like', '%' . $mentorClean . '%')->first();
            if ($foundMentor) {
                $mentorId = $foundMentor->id;
            }
        }

        TransaksiBimbingan::create([
            'nomor_tiket' => $nomorTiket,
            'user_id' => Auth::id(),
            'nama_mahasiswa' => $namaMahasiswa,
            'judul_skripsi' => $validated['judul_penelitian'],
            'topik_bidang' => $validated['topik_bab'],
            'paket_bimbingan' => $namaProgram,
            'program_id' => $program ? $program->id : null,
            'mentor_id' => $mentorId,
            'jadwal_konsultasi' => $validated['tanggal_preferensi'],
            'link_zoom' => ($validated['media'] ?? '') === 'Google Meet' ? 'https://meet.google.com/aether-bmb' : null,
            'keluhan_kendala' => $validated['catatan'] ?? null,
            'status' => 'Menunggu Review',
            'biaya' => $biaya,
            'status_pembayaran' => 'Menunggu Pembayaran',
        ]);

        if ($isRole99 && isset($purchasedProgram)) {
            $purchasedProgram->increment('sesi_digunakan');
            if ($purchasedProgram->sisa_sesi > 0) {
                $purchasedProgram->decrement('sisa_sesi');
            }
        }

        if ($request->input('redirect_to') === 'dashboard') {
            return redirect()->route('dashboard')->with('success', 'Pengajuan bimbingan untuk paket [' . $namaProgram . '] berhasil dikirim. Sesi konsultasi telah tertandai di kalender dashboard Anda!');
        }

        return redirect()->route('bimbingan.index')->with('success', 'Pengajuan bimbingan untuk paket [' . $namaProgram . '] berhasil dikirim. Mentor akan mengonfirmasi jadwal Anda segera!');
    }

    public function jadwal()
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $query = TransaksiBimbingan::with(['program', 'mentor'])
            ->whereNotNull('jadwal_konsultasi')
            ->orderBy('jadwal_konsultasi', 'asc');

        if ($isRole99) {
            $query->where('user_id', Auth::id());
        }

        $jadwals = $query->get();
        return view('bimbingan::jadwal', compact('jadwals', 'isRole99'));
    }

    public function riwayat()
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $query = TransaksiBimbingan::with(['program', 'mentor'])
            ->orderBy('id', 'desc');

        if ($isRole99) {
            $query->where('user_id', Auth::id());
        }

        $riwayats = $query->get();
        return view('bimbingan::riwayat', compact('riwayats', 'isRole99'));
    }

    public function show($id)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $bimbingan = TransaksiBimbingan::with(['program', 'mentor'])->findOrFail($id);

        if ($isRole99 && $bimbingan->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat data bimbingan ini.');
        }

        return view('bimbingan::show', compact('bimbingan', 'id', 'isRole99'));
    }
}
