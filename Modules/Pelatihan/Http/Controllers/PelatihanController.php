<?php

namespace Modules\Pelatihan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Modules\Master\Entities\MasterKelas;
use Modules\Master\Entities\MasterMateri;
use Modules\Pelatihan\Entities\TransaksiPelatihan;
use Barryvdh\DomPDF\Facade\Pdf;

class PelatihanController extends Controller
{
    public function index()
    {
        $courses = [
            [
                'id' => 1,
                'judul' => 'Mastering SEM-PLS 4: Dari Kerangka Konseptual Hingga Interpretasi Bab 4',
                'instruktur' => 'Nur Atika Rochmah, M.Si.',
                'level' => 'Intermediate',
                'kategori' => 'Kuantitatif & SEM',
                'total_modul' => '8 Sesi Video + Live Q&A',
                'jadwal' => '15 - 17 Okt 2026',
                'peserta' => 48,
                'status' => 'Pendaftaran Dibuka',
                'badge' => 'Terpopuler'
            ],
            [
                'id' => 2,
                'judul' => 'Workshop Regresi Data Panel Menggunakan EViews & Stata untuk Tesis Magister',
                'instruktur' => 'Deny Aditya Pratama, S.Stat.',
                'level' => 'Advanced',
                'kategori' => 'Ekonometrika',
                'total_modul' => '6 Sesi + Do-file Template',
                'jadwal' => '22 - 24 Okt 2026',
                'peserta' => 32,
                'status' => 'Pendaftaran Dibuka',
                'badge' => 'Intensif'
            ],
            [
                'id' => 3,
                'judul' => 'Analisis Data Kualitatif Praktis dengan NVivo: Thematic Coding & Visualisasi',
                'instruktur' => 'Alessandra Natasya Panie, S.Psi.',
                'level' => 'Pemula - Menengah',
                'kategori' => 'Kualitatif',
                'total_modul' => '5 Sesi + Dataset Latihan',
                'jadwal' => '29 - 30 Okt 2026',
                'peserta' => 25,
                'status' => 'Segera Hadir',
                'badge' => 'Praktikal'
            ],
            [
                'id' => 4,
                'judul' => 'Bootcamp Mahir SPSS: Uji Validitas, Reliabilitas, & Asumsi Klasik Tanpa Pusing',
                'instruktur' => 'Arrum Rochmani Hidayah, M.Kom.',
                'level' => 'Pemula',
                'kategori' => 'Statistik Dasar',
                'total_modul' => '10 Sesi Interaktif',
                'jadwal' => '05 - 07 Nov 2026',
                'peserta' => 60,
                'status' => 'Pendaftaran Dibuka',
                'badge' => 'Best Seller',
                'thumbnail_url' => null,
                'harga_formatted' => 'Rp 175.000',
            ]
        ];

        // Load dinamis dari MasterKelas jika tabel ada di database
        if (\Illuminate\Support\Facades\Schema::hasTable('master_kelas')) {
            $dbCourses = MasterKelas::with(['mentor', 'materis'])->where('is_active', true)->get();
            if ($dbCourses->isNotEmpty()) {
                $courses = $dbCourses->map(function($c) {
                    $mentorName = $c->mentor ? ($c->mentor->nama_lengkap . ($c->mentor->gelar ? ', ' . $c->mentor->gelar : '')) : 'Instruktur Ahli Aether';
                    $modulCount = $c->materis->count() > 0 ? $c->materis->count() . ' Modul Praktik' : ($c->silabus_singkat ?? 'Sesi Interaktif');
                    $jadwal = ($c->tanggal_mulai ? date('d M', strtotime($c->tanggal_mulai)) : 'Segera') . ($c->tanggal_selesai ? ' - ' . date('d M Y', strtotime($c->tanggal_selesai)) : '');

                    return [
                        'id' => $c->id,
                        'judul' => $c->judul,
                        'instruktur' => $mentorName,
                        'level' => $c->level,
                        'kategori' => $c->kategori ?? 'Metodologi & Data',
                        'total_modul' => $modulCount,
                        'jadwal' => $jadwal,
                        'peserta' => $c->kuota_maksimal,
                        'status' => 'Pendaftaran ' . $c->status,
                        'badge' => $c->badge ?? 'Riset',
                        'thumbnail' => $c->thumbnail,
                        'thumbnail_url' => $c->thumbnail_url,
                        'harga' => $c->harga,
                        'harga_formatted' => $c->harga_formatted,
                        'deskripsi_singkat' => $c->deskripsi_singkat,
                        'kode_kelas' => $c->kode_kelas,
                        'tipe' => $c->tipe,
                    ];
                })->toArray();
            }
        }

        return view('pelatihan::index', compact('courses'));
    }

    public function katalog()
    {
        return $this->index();
    }

    public function kelasSaya()
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $query = TransaksiPelatihan::with('kelas')->orderBy('id', 'desc');
        if ($isRole99) {
            $query->where('user_id', $userId);
        }

        $myCourses = $query->get();

        return view('pelatihan::kelas-saya', compact('myCourses', 'isRole99'));
    }

    public function sertifikat()
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $query = TransaksiPelatihan::with(['kelas.mentor', 'user'])
            ->where('progress', '>=', 100)
            ->where('status_pembayaran', 'Lunas')
            ->orderBy('id', 'desc');

        if ($isRole99) {
            $query->where('user_id', Auth::id());
        }
        $sertifikats = $query->get();

        return view('pelatihan::sertifikat', compact('sertifikats', 'isRole99'));
    }

    public function previewSertifikat($id)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $sertifikat = TransaksiPelatihan::with(['kelas.mentor', 'user'])->find($id);

        if (!$sertifikat) {
            abort(404, 'Sertifikat pelatihan tidak ditemukan.');
        }

        if ($isRole99 && (int)$sertifikat->user_id !== (int)$userId) {
            abort(403, 'Anda tidak memiliki hak akses melihat sertifikat ini.');
        }

        if ((int)$sertifikat->progress < 100) {
            abort(403, 'Sertifikat kelulusan belum dapat diakses karena pelatihan belum selesai 100%.');
        }

        return view('pelatihan::sertifikat-preview', compact('sertifikat'));
    }

    public function downloadSertifikat($id)
    {
        $isRole99 = ((int)(session('role')['id_role'] ?? 0) === 99);
        $userId = Auth::id();

        $sertifikat = TransaksiPelatihan::with(['kelas.mentor', 'user'])->find($id);

        if (!$sertifikat) {
            abort(404, 'Sertifikat pelatihan tidak ditemukan.');
        }

        if ($isRole99 && (int)$sertifikat->user_id !== (int)$userId) {
            abort(403, 'Anda tidak memiliki hak akses mengunduh sertifikat ini.');
        }

        if ((int)$sertifikat->progress < 100) {
            abort(403, 'Sertifikat kelulusan belum dapat diunduh karena pelatihan belum selesai 100%.');
        }

        $pdf = Pdf::loadView('pelatihan::sertifikat-pdf', compact('sertifikat'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultFont' => 'Helvetica',
            ]);

        $fileName = 'sertifikat-' . Str::slug($sertifikat->nomor_registrasi ?: ('cert-' . $sertifikat->id)) . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Halaman Publik Verifikasi E-Sertifikat Resmi
     */
    public function verifikasiSertifikat(Request $request, $identifier = null)
    {
        $identifier = $identifier ?: $request->query('q');
        $sertifikat = null;

        if ($identifier) {
            $identifierClean = trim($identifier);

            // 1. Cari berdasarkan nomor_registrasi
            $sertifikat = TransaksiPelatihan::with(['kelas.mentor', 'user'])
                ->where('nomor_registrasi', $identifierClean)
                ->first();

            // 2. Cari berdasarkan ID numerik langsung
            if (!$sertifikat && is_numeric($identifierClean)) {
                $sertifikat = TransaksiPelatihan::with(['kelas.mentor', 'user'])
                    ->find((int)$identifierClean);
            }

            // 3. Cari jika berupa format nomor sertifikat (misal AETH-CERT/2026/KLSP-00014 atau slug)
            if (!$sertifikat) {
                if (preg_match('/-(\d{1,6})$/', $identifierClean, $matches)) {
                    $candidate = TransaksiPelatihan::with(['kelas.mentor', 'user'])->find((int)$matches[1]);
                    if ($candidate) {
                        $sertifikat = $candidate;
                    }
                }
            }
        }

        $isValidCompleted = $sertifikat && ((int)$sertifikat->progress >= 100) && ($sertifikat->status_pembayaran === 'Lunas');

        return view('pelatihan::sertifikat-verifikasi', compact('sertifikat', 'identifier', 'isValidCompleted'));
    }

    public function show(Request $request, $id)
    {
        $kelas = MasterKelas::with(['mentor', 'materis' => function($q) {
            $q->where('is_active', true)->orderBy('urutan', 'asc');
        }])->find($id);

        if (!$kelas) {
            abort(404, 'Kelas pelatihan tidak ditemukan.');
        }

        $materis = $kelas->materis;
        $materiId = $request->query('materi');
        $activeMateri = $materiId ? $materis->firstWhere('id', (int) $materiId) : null;
        if (!$activeMateri) {
            $activeMateri = $materis->first();
        }

        // Ambil daftar materi yang telah selesai ditonton/dipelajari
        $completedMateriIds = session()->get("kelas_{$kelas->id}_completed_materis", []);

        $userId = Auth::id();
        $transaksi = null;
        if ($userId) {
            $transaksi = TransaksiPelatihan::where('user_id', $userId)
                ->where('kelas_id', $kelas->id)
                ->latest()
                ->first();

            if ($transaksi && !empty($transaksi->catatan)) {
                $decoded = json_decode($transaksi->catatan, true);
                if (is_array($decoded) && isset($decoded['completed_materis']) && is_array($decoded['completed_materis'])) {
                    $completedMateriIds = array_values(array_unique(array_merge($completedMateriIds, $decoded['completed_materis'])));
                }
            }
        }

        $allMateriIds = $materis->pluck('id')->toArray();
        $totalModul = count($allMateriIds);
        $selesaiCount = count(array_intersect($completedMateriIds, $allMateriIds));
        $progressPercent = $totalModul > 0 ? min(100, round(($selesaiCount / $totalModul) * 100)) : 0;

        return view('pelatihan::show', compact(
            'id', 
            'kelas', 
            'materis', 
            'activeMateri', 
            'completedMateriIds', 
            'selesaiCount', 
            'progressPercent',
            'transaksi'
        ));
    }

    /**
     * Set action bahwa video / materi selesai dilihat
     */
    public function markMateriSelesai(Request $request, $id)
    {
        $materi = MasterMateri::find($id);
        if (!$materi) {
            return response()->json([
                'success' => false,
                'message' => 'Materi tidak ditemukan.'
            ], 404);
        }

        $kelas = MasterKelas::with('materis')->find($materi->kelas_id);
        if (!$kelas) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas pelatihan tidak ditemukan.'
            ], 404);
        }

        $sessionKey = "kelas_{$kelas->id}_completed_materis";
        $completed = session()->get($sessionKey, []);
        $materiId = (int) $id;

        $action = $request->input('action', 'toggle'); // 'complete', 'incomplete', 'toggle'

        if ($action === 'incomplete') {
            $completed = array_values(array_diff($completed, [$materiId]));
            $isCompleted = false;
        } elseif ($action === 'complete') {
            if (!in_array($materiId, $completed)) {
                $completed[] = $materiId;
            }
            $isCompleted = true;
        } else {
            // toggle
            if (in_array($materiId, $completed)) {
                $completed = array_values(array_diff($completed, [$materiId]));
                $isCompleted = false;
            } else {
                $completed[] = $materiId;
                $isCompleted = true;
            }
        }

        session()->put($sessionKey, $completed);

        $allMateriIds = $kelas->materis()->where('is_active', true)->pluck('id')->toArray();
        $totalModul = count($allMateriIds) > 0 ? count($allMateriIds) : $kelas->materis->count();
        $selesaiCount = count(array_intersect($completed, $allMateriIds));
        $progress = $totalModul > 0 ? min(100, round(($selesaiCount / $totalModul) * 100)) : 0;

        // Jika user login, simpan progress ke transaksi pelatihan
        $userId = Auth::id();
        if ($userId) {
            $transaksi = TransaksiPelatihan::where('user_id', $userId)
                ->where('kelas_id', $kelas->id)
                ->latest()
                ->first();

            if ($transaksi) {
                $catatanData = [];
                if (!empty($transaksi->catatan)) {
                    $catatanData = json_decode($transaksi->catatan, true) ?: [];
                }
                $catatanData['completed_materis'] = $completed;

                $updateData = [
                    'total_modul' => $totalModul,
                    'modul_selesai' => $selesaiCount,
                    'progress' => $progress,
                    'catatan' => json_encode($catatanData),
                ];

                if ($progress >= 100 && $transaksi->status !== 'Selesai') {
                    $updateData['status'] = 'Selesai';
                }

                $transaksi->update($updateData);
            }
        }

        return response()->json([
            'success' => true,
            'is_completed' => $isCompleted,
            'completed_materis' => $completed,
            'modul_selesai' => $selesaiCount,
            'total_modul' => $totalModul,
            'progress' => $progress,
            'message' => $isCompleted 
                ? 'Materi video berhasil ditandai selesai dilihat.' 
                : 'Status selesai materi video telah dibatalkan.'
        ]);
    }

    public function payment($id)
    {
        $user = Auth::user();
        $kelas = MasterKelas::with(['mentor', 'materis' => function($q) {
            $q->where('is_active', true)->orderBy('urutan', 'asc');
        }])->find($id);

        if (!$kelas) {
            $catalog = [
                1 => ['judul' => 'Mastering SEM-PLS 4: Dari Kerangka Konseptual Hingga Interpretasi Bab 4', 'instruktur' => 'Nur Atika Rochmah, M.Si.', 'harga' => 250000, 'kategori' => 'Kuantitatif & SEM', 'badge' => 'Terpopuler'],
                2 => ['judul' => 'Workshop Regresi Data Panel Menggunakan EViews & Stata untuk Tesis Magister', 'instruktur' => 'Deny Aditya Pratama, S.Stat.', 'harga' => 350000, 'kategori' => 'Ekonometrika', 'badge' => 'Intensif'],
                3 => ['judul' => 'Analisis Data Kualitatif Praktis dengan NVivo: Thematic Coding & Visualisasi', 'instruktur' => 'Alessandra Natasya Panie, S.Psi.', 'harga' => 200000, 'kategori' => 'Kualitatif', 'badge' => 'Praktikal'],
                4 => ['judul' => 'Bootcamp Mahir SPSS: Uji Validitas, Reliabilitas, & Asumsi Klasik Tanpa Pusing', 'instruktur' => 'Arrum Rochmani Hidayah, M.Kom.', 'harga' => 175000, 'kategori' => 'Statistik Dasar', 'badge' => 'Best Seller'],
            ];
            $item = $catalog[$id] ?? ['judul' => 'Workshop Pelatihan Riset', 'instruktur' => 'Mentor Ahli Aether', 'harga' => 200000, 'kategori' => 'Riset', 'badge' => 'Riset'];
            $kelas = (object) [
                'id'              => $id,
                'judul'           => $item['judul'],
                'instruktur'      => $item['instruktur'],
                'harga'           => $item['harga'],
                'kategori'        => $item['kategori'],
                'badge'           => $item['badge'],
                'kode_kelas'      => 'KLS-' . str_pad($id, 3, '0', STR_PAD_LEFT),
                'silabus_singkat' => '8 Sesi Video Praktik + Template Word Bab 4 + Live Q&A',
                'deskripsi'       => 'Pelatihan intensif analisis data dan metodologi riset untuk penyusunan skripsi dan tesis.',
            ];
            $materis = collect([
                (object)[
                    'id' => 1,
                    'urutan' => 1,
                    'judul_materi' => 'Pengenalan & Fundamental Metodologi',
                    'tipe_materi' => 'Video',
                    'durasi_menit' => 45,
                    'deskripsi' => 'Pengenalan antarmuka software statistik, persiapan dataset, dan pembersihan missing value.',
                    'is_free_preview' => false,
                ],
                (object)[
                    'id' => 2,
                    'urutan' => 2,
                    'judul_materi' => 'Evaluasi Model Pengukuran & Uji Validitas Reliabilitas',
                    'tipe_materi' => 'Video',
                    'durasi_menit' => 60,
                    'deskripsi' => 'Pengujian validitas konvergen, validitas diskriminan, reliabilitas komposit, dan cross loadings.',
                    'is_free_preview' => false,
                ],
                (object)[
                    'id' => 3,
                    'urutan' => 3,
                    'judul_materi' => 'Template & Panduan Narasi Pembahasan Bab 4 Siap Sidang',
                    'tipe_materi' => 'Dokumen PDF',
                    'durasi_menit' => 0,
                    'deskripsi' => 'Template laporan Word format standar universitas dengan tata letak tabel dan penafsiran hipotesis.',
                    'is_free_preview' => false,
                ],
                (object)[
                    'id' => 4,
                    'urutan' => 4,
                    'judul_materi' => 'Master Dataset Praktik: Responden Kuisioner Skala Likert',
                    'tipe_materi' => 'Dataset Praktik',
                    'durasi_menit' => 0,
                    'deskripsi' => 'Dataset riil untuk latihan uji mediasi dan moderasi mandiri serta panduan analisis.',
                    'is_free_preview' => false,
                ],
            ]);
        } else {
            $materis = $kelas->materis ?? collect();
        }

        $userId = $user ? ($user->id_user ?? $user->id) : null;
        $alreadyPurchased = false;
        if ($userId) {
            $alreadyPurchased = TransaksiPelatihan::where('user_id', $userId)
                ->where('kelas_id', $id)
                ->where('status_pembayaran', 'Lunas')
                ->exists();
        }

        $adminPrice = 5000;
        $totalPrice = $kelas->harga + $adminPrice;

        return view('pelatihan::payment', compact('kelas', 'materis', 'user', 'alreadyPurchased', 'adminPrice', 'totalPrice'));
    }

    public function processPayment(Request $request, $id)
    {
        $user = Auth::user();
        $kelas = MasterKelas::with(['mentor', 'materis'])->find($id);

        if (!$kelas) {
            $catalog = [
                1 => ['judul' => 'Mastering SEM-PLS 4: Dari Kerangka Konseptual Hingga Interpretasi Bab 4', 'instruktur' => 'Nur Atika Rochmah, M.Si.', 'harga' => 250000, 'kategori' => 'Kuantitatif & SEM'],
                2 => ['judul' => 'Workshop Regresi Data Panel Menggunakan EViews & Stata untuk Tesis Magister', 'instruktur' => 'Deny Aditya Pratama, S.Stat.', 'harga' => 350000, 'kategori' => 'Ekonometrika'],
                3 => ['judul' => 'Analisis Data Kualitatif Praktis dengan NVivo: Thematic Coding & Visualisasi', 'instruktur' => 'Alessandra Natasya Panie, S.Psi.', 'harga' => 200000, 'kategori' => 'Kualitatif'],
                4 => ['judul' => 'Bootcamp Mahir SPSS: Uji Validitas, Reliabilitas, & Asumsi Klasik Tanpa Pusing', 'instruktur' => 'Arrum Rochmani Hidayah, M.Kom.', 'harga' => 175000, 'kategori' => 'Statistik Dasar'],
            ];
            $item = $catalog[$id] ?? ['judul' => 'Workshop Pelatihan Riset', 'instruktur' => 'Mentor Ahli Aether', 'harga' => 200000, 'kategori' => 'Riset'];
            $kelas = (object) [
                'id'         => $id,
                'judul'      => $item['judul'],
                'instruktur' => $item['instruktur'],
                'harga'      => $item['harga'],
                'kode_kelas' => 'KLS-' . str_pad($id, 3, '0', STR_PAD_LEFT),
            ];
        }

        $validated = $request->validate([
            'nama_pemesan'      => 'required|string|max:150',
            'email_pemesan'     => 'required|email|max:150',
            'telepon_pemesan'   => 'required|string|max:30',
            'universitas'       => 'nullable|string|max:150',
            'judul_skripsi'     => 'nullable|string|max:255',
            'metode_pembayaran' => 'nullable|string|max:100',
        ], [
            'nama_pemesan.required'    => 'Nama lengkap wajib diisi.',
            'email_pemesan.required'   => 'Email aktif wajib diisi.',
            'telepon_pemesan.required' => 'Nomor WhatsApp wajib diisi.',
        ]);

        $userId = $user ? ($user->id_user ?? $user->id) : null;
        $adminPrice = 5000;
        $biaya = $kelas->harga ?? 200000;
        $totalBayar = $biaya + $adminPrice;
        $nomorReg = 'REG-WKS-' . date('Ym') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);

        $metodePembayaran = !empty($validated['metode_pembayaran']) ? $validated['metode_pembayaran'] : 'QRIS Instant';

        $paymentCode = match ($metodePembayaran) {
            'BCA Virtual Account'     => '80777' . rand(10000000, 99999999),
            'Mandiri Virtual Account' => '88908' . rand(10000000, 99999999),
            'BNI Virtual Account'     => '98801' . rand(10000000, 99999999),
            'BRI Virtual Account'     => '12800' . rand(10000000, 99999999),
            'QRIS Instant'            => 'QRIS-' . strtoupper(Str::random(10)),
            'Transfer Bank BCA'       => '7310889201',
            'Transfer Bank Mandiri'   => '1370019283741',
            default                   => 'QRIS-' . strtoupper(Str::random(10)),
        };

        $instruktur = isset($kelas->mentor) ? ($kelas->mentor->nama_lengkap . ($kelas->mentor->gelar ? ', ' . $kelas->mentor->gelar : '')) : ($kelas->instruktur ?? 'Instruktur Ahli Aether');

        $totalModul = 8;
        if (isset($kelas->materis) && $kelas->materis->count() > 0) {
            $totalModul = $kelas->materis->count();
        }

        $transaksi = TransaksiPelatihan::create([
            'nomor_registrasi'  => $nomorReg,
            'user_id'           => $userId,
            'kelas_id'          => $kelas->id,
            'judul_kelas'       => $kelas->judul,
            'instruktur'        => $instruktur,
            'kode_kelas'        => $kelas->kode_kelas ?? ('KLS-' . str_pad($kelas->id, 3, '0', STR_PAD_LEFT)),
            'link_zoom'         => $kelas->link_meeting ?? 'https://meet.google.com/aether-wks',
            'progress'          => 0,
            'total_modul'       => $totalModul,
            'modul_selesai'     => 0,
            'status'            => 'Menunggu Pembayaran',
            'nama_pemesan'      => $validated['nama_pemesan'],
            'email_pemesan'     => $validated['email_pemesan'],
            'telepon_pemesan'   => $validated['telepon_pemesan'],
            'universitas'       => $validated['universitas'] ?? null,
            'biaya'             => $biaya,
            'admin_fee'         => $adminPrice,
            'total_bayar'       => $totalBayar,
            'metode_pembayaran' => $metodePembayaran,
            'kode_pembayaran'   => $paymentCode,
            'status_pembayaran' => 'Menunggu Pembayaran',
            'tanggal_pembelian' => now(),
            'catatan'           => !empty($validated['judul_skripsi']) ? json_encode(['topik_skripsi' => $validated['judul_skripsi']]) : null,
        ]);

        return redirect()->route('pelatihan.payment.instruction', $transaksi->id)
            ->with('info', 'Pendaftaran kelas berhasil dibuat. Silakan selesaikan pembayaran dan upload bukti transfer Anda.');
    }

    public function paymentInstruction($id)
    {
        $transaksi = TransaksiPelatihan::with('kelas')->findOrFail($id);
        return view('pelatihan::instruction', compact('transaksi'));
    }

    public function gantiMetodeBayar(Request $request, $id)
    {
        $oldTransaksi = TransaksiPelatihan::with('kelas')->findOrFail($id);

        $validated = $request->validate([
            'metode_pembayaran' => 'required|string|max:100',
        ]);

        // 1. Batalkan transaksi lama
        $oldTransaksi->status_pembayaran = 'Dibatalkan';
        $oldTransaksi->status = 'Dibatalkan';
        $oldTransaksi->catatan = 'Dibatalkan karena ganti metode bayar ke ' . $validated['metode_pembayaran'];
        $oldTransaksi->save();

        // 2. Generate kode bayar baru
        $paymentCode = match ($validated['metode_pembayaran']) {
            'BCA Virtual Account'     => '80777' . rand(10000000, 99999999),
            'Mandiri Virtual Account' => '88908' . rand(10000000, 99999999),
            'BNI Virtual Account'     => '98801' . rand(10000000, 99999999),
            'BRI Virtual Account'     => '12800' . rand(10000000, 99999999),
            'QRIS Instant'            => 'QRIS-' . strtoupper(Str::random(10)),
            'Transfer Bank BCA'       => '7310889201',
            'Transfer Bank Mandiri'   => '1370019283741',
            default                   => '7310889201',
        };

        // 3. Buat transaksi baru
        $newNomorReg = 'REG-WKS-' . date('Ym') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $newTransaksi = TransaksiPelatihan::create([
            'nomor_registrasi'  => $newNomorReg,
            'user_id'           => $oldTransaksi->user_id,
            'kelas_id'          => $oldTransaksi->kelas_id,
            'judul_kelas'       => $oldTransaksi->judul_kelas,
            'instruktur'        => $oldTransaksi->instruktur,
            'kode_kelas'        => $oldTransaksi->kode_kelas,
            'link_zoom'         => $oldTransaksi->link_zoom,
            'progress'          => 0,
            'total_modul'       => $oldTransaksi->total_modul,
            'modul_selesai'     => 0,
            'status'            => 'Menunggu Pembayaran',
            'nama_pemesan'      => $oldTransaksi->nama_pemesan,
            'email_pemesan'     => $oldTransaksi->email_pemesan,
            'telepon_pemesan'   => $oldTransaksi->telepon_pemesan,
            'universitas'       => $oldTransaksi->universitas,
            'biaya'             => $oldTransaksi->biaya,
            'admin_fee'         => $oldTransaksi->admin_fee,
            'total_bayar'       => $oldTransaksi->total_bayar,
            'metode_pembayaran' => $validated['metode_pembayaran'],
            'kode_pembayaran'   => $paymentCode,
            'status_pembayaran' => 'Menunggu Pembayaran',
            'tanggal_pembelian' => now(),
            'catatan'           => 'Pergantian metode dari #' . $oldTransaksi->nomor_registrasi,
        ]);

        return redirect()->route('pelatihan.payment.instruction', $newTransaksi->id)
            ->with('info', "Metode pembayaran berhasil diubah ke {$validated['metode_pembayaran']}. Tagihan lama (#{$oldTransaksi->nomor_registrasi}) telah dibatalkan.");
    }

    public function uploadBuktiPembayaran(Request $request, $id)
    {
        $transaksi = TransaksiPelatihan::findOrFail($id);

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
            'catatan'          => 'nullable|string|max:255',
        ], [
            'bukti_pembayaran.required' => 'File bukti transfer wajib diupload.',
            'bukti_pembayaran.image'    => 'File harus berupa gambar.',
            'bukti_pembayaran.max'      => 'Ukuran file maksimal 3 MB.',
        ]);

        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $filename = 'bukti_wks_' . str_replace(['/', '\\', ' '], '_', $transaksi->nomor_registrasi) . '_' . time() . '.' . $file->getClientOriginalExtension();
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

        return redirect()->route('pelatihan.payment.invoice', $transaksi->id)
            ->with('success', 'Bukti pembayaran berhasil diupload! Tim admin kami akan segera memverifikasi pembayaran Anda.');
    }

    public function paymentInvoice($id)
    {
        $transaksi = TransaksiPelatihan::with('kelas')->findOrFail($id);
        return view('pelatihan::invoice', compact('transaksi'));
    }

    public function verifikasiPembayaran($id)
    {
        $transaksi = TransaksiPelatihan::findOrFail($id);
        $transaksi->status_pembayaran = 'Lunas';
        $transaksi->status = 'Aktif Berjalan';
        $transaksi->save();

        return response()->json([
            'status' => 'success',
            'message' => "Pembayaran pendaftaran workshop #{$transaksi->nomor_registrasi} berhasil diverifikasi. Peserta kini AKTIF dalam kelas!"
        ]);
    }

    public function selesaikanPelatihan($id)
    {
        $transaksi = TransaksiPelatihan::findOrFail($id);
        $transaksi->status = 'Selesai';
        $transaksi->progress = 100;
        $transaksi->modul_selesai = $transaksi->total_modul;
        $transaksi->save();

        return response()->json([
            'status' => 'success',
            'message' => "Pelatihan untuk peserta {$transaksi->nama_pemesan} berhasil ditandai SELESAI. E-Sertifikat kini dapat diunduh!"
        ]);
    }

    public function batalkanPembayaran($id)
    {
        $transaksi = TransaksiPelatihan::findOrFail($id);
        $transaksi->status_pembayaran = 'Dibatalkan';
        $transaksi->status = 'Dibatalkan';
        $transaksi->save();

        return response()->json([
            'status' => 'success',
            'message' => "Pembayaran registrasi #{$transaksi->nomor_registrasi} berhasil dibatalkan."
        ]);
    }

    /**
     * Halaman Detail Kelas & Workshop Riset (Ebizmark style landing)
     */
    public function detail($id)
    {
        $user = Auth::user();
        $kelas = MasterKelas::with(['mentor', 'materis' => function($q) {
            $q->where('is_active', true)->orderBy('urutan', 'asc');
        }, 'transaksis'])->find($id);

        if (!$kelas) {
            $catalog = [
                1 => [
                    'judul' => 'Mastering SEM-PLS 4: Dari Kerangka Konseptual Hingga Interpretasi Bab 4',
                    'instruktur' => 'Nur Atika Rochmah, M.Si.',
                    'gelar' => 'M.Si.',
                    'institusi' => 'Fakultas MIPA - Departemen Statistika Terapan',
                    'spesialisasi' => 'Struktur Equation Modeling (SEM), SmartPLS 4, & Publikasi Sinta',
                    'harga' => 250000,
                    'kategori' => 'Kuantitatif & SEM',
                    'level' => 'Semua Tingkat',
                    'tipe_kelas' => 'Live Workshop Zoom + Rekaman Selamanya',
                    'badge' => 'Terpopuler',
                    'deskripsi' => "Workshop intensif yang dirancang khusus untuk memandu mahasiswa tingkat akhir, magister, dan peneliti dalam menyelesaikan analisis data skripsi/tesis menggunakan SmartPLS 4.\n\nAnda akan mempelajari mulai dari penetapan model pengukuran (outer model), evaluasi validitas konvergen, validitas diskriminan (HTMT), reliabilitas komposit, hingga pengujian hipotesis (inner model) dan mediasi/moderasi dengan interpretasi hasil standar jurnal ilmiah.",
                    'silabus_singkat' => '8 Sesi Hands-on Live + Master Dataset 150 Responden + Template Narasi Bab 4 Siap Sidang',
                ],
                2 => [
                    'judul' => 'Workshop Regresi Data Panel Menggunakan EViews & Stata untuk Tesis Magister',
                    'instruktur' => 'Deny Aditya Pratama, S.Stat.',
                    'gelar' => 'S.Stat.',
                    'institusi' => 'Pusat Studi Ekonometrika & Analisis Keuangan',
                    'spesialisasi' => 'Ekonometrika Keuangan, Panel Data, EViews & Stata',
                    'harga' => 350000,
                    'kategori' => 'Ekonometrika',
                    'level' => 'Tingkat Menengah',
                    'tipe_kelas' => 'Bootcamp Intensif 2 Hari',
                    'badge' => 'Intensif',
                    'deskripsi' => "Pelatihan mendalam estimasi data panel meliputi Common Effect Model (CEM), Fixed Effect Model (FEM), dan Random Effect Model (REM), serta uji pemilihan model (Chow, Hausman, Lagrange Multiplier) hingga uji asumsi klasik dan regresi dinamis.",
                    'silabus_singkat' => 'Praktik Langsung Olah Data Keuangan Perusahaan 5 Tahun + Do-File Sintaks Stata',
                ],
                3 => [
                    'judul' => 'Analisis Data Kualitatif Praktis dengan NVivo: Thematic Coding & Visualisasi',
                    'instruktur' => 'Alessandra Natasya Panie, S.Psi.',
                    'gelar' => 'S.Psi., M.A.',
                    'institusi' => 'Laboratorium Metodologi Kualitatif & Perilaku',
                    'spesialisasi' => 'Qualitative Data Analysis, NVivo 14, & Thematic Analysis',
                    'harga' => 200000,
                    'kategori' => 'Kualitatif',
                    'level' => 'Semua Tingkat',
                    'tipe_kelas' => 'Workshop Interaktif',
                    'badge' => 'Praktikal',
                    'deskripsi' => "Panduan komprehensif mengolah data wawancara mendalam, FGD, artikel berita, dan dokumen literatur dengan teknik koding otomatis dan visualisasi peta konsep menggunakan NVivo versi terbaru.",
                    'silabus_singkat' => 'Latihan Transkrip Wawancara + Panduan Koding Hirarki + Matriks Query',
                ],
                4 => [
                    'judul' => 'Bootcamp Mahir SPSS: Uji Validitas, Reliabilitas, & Asumsi Klasik Tanpa Pusing',
                    'instruktur' => 'Arrum Rochmani Hidayah, M.Kom.',
                    'gelar' => 'M.Kom.',
                    'institusi' => 'Akademi Sains Data & Komputasi Statistik',
                    'spesialisasi' => 'Statistika Dasar, Regresi Linier Berganda, & SPSS 29',
                    'harga' => 175000,
                    'kategori' => 'Statistik Dasar',
                    'level' => 'Pemula (Beginner)',
                    'tipe_kelas' => 'Self-Paced + Live Mentoring',
                    'badge' => 'Best Seller',
                    'deskripsi' => "Didesain bagi Anda yang belum pernah menyentuh software statistik. Langkah demi langkah membaca kuesioner, tabulasi data Excel, input SPSS, transformasi data, deteksi outlier, uji normalitas, hingga penulisan hasil siap cetak.",
                    'silabus_singkat' => '10 Video Langkah Demi Langkah + Lembar Kerja Latihan + E-Certificate',
                ],
            ];

            if (!isset($catalog[$id])) {
                abort(404, 'Detail kelas dan workshop tidak ditemukan.');
            }

            $item = $catalog[$id];
            $kelas = (object) [
                'id'              => $id,
                'judul'           => $item['judul'],
                'instruktur'      => $item['instruktur'],
                'harga'           => $item['harga'],
                'harga_formatted' => 'Rp ' . number_format($item['harga'], 0, ',', '.'),
                'kategori'        => $item['kategori'],
                'level'           => $item['level'],
                'badge'           => $item['badge'],
                'kode_kelas'      => 'KLS-' . str_pad($id, 3, '0', STR_PAD_LEFT),
                'silabus_singkat' => $item['silabus_singkat'],
                'deskripsi'       => $item['deskripsi'],
                'tipe_kelas'      => $item['tipe_kelas'],
                'status'          => 'Dibuka',
                'kuota_maksimal'  => 50,
                'thumbnail_url'   => null,
                'tanggal_mulai'   => now()->addDays(3),
                'tanggal_selesai' => now()->addDays(5),
                'mentor'          => (object) [
                    'nama_lengkap'        => $item['instruktur'],
                    'gelar'               => $item['gelar'],
                    'spesialisasi'        => $item['spesialisasi'],
                    'institusi'           => $item['institusi'],
                    'keahlian_software'   => $item['kategori'],
                    'pendidikan_terakhir' => 'Magister (S2)',
                ],
            ];

            $materis = collect([
                (object)[
                    'id' => 1,
                    'urutan' => 1,
                    'judul_materi' => 'Fundamental Teori, Kerangka Berpikir, & Hipotesis Penelitian',
                    'tipe_materi' => 'Video',
                    'durasi_menit' => 45,
                    'deskripsi' => 'Merumuskan kerangka konseptual yang kokoh, variabel eksogen, endogen, dan operasionalisasi indikator kuesioner.',
                    'is_free_preview' => true,
                    'link_video' => 'https://www.youtube.com/watch?v=sample1',
                    'file_materi' => null,
                ],
                (object)[
                    'id' => 2,
                    'urutan' => 2,
                    'judul_materi' => 'Persiapan Dataset: Skrining Missing Value, Outlier, & Normalitas Data',
                    'tipe_materi' => 'Dataset Praktik',
                    'durasi_menit' => 50,
                    'deskripsi' => 'Pembersihan data mentah excel, konversi tipe variabel, dan uji kelayakan responden.',
                    'is_free_preview' => false,
                    'link_video' => null,
                    'file_materi' => 'dataset_latihan_150n.csv',
                ],
                (object)[
                    'id' => 3,
                    'urutan' => 3,
                    'judul_materi' => 'Evaluasi Measurement Model (Outer Model): Uji Validitas & Reliabilitas',
                    'tipe_materi' => 'Video',
                    'durasi_menit' => 60,
                    'deskripsi' => 'Pengujian Cronbach Alpha, Composite Reliability, AVE, Discriminant Validity (Fornell-Larcker & HTMT ratio).',
                    'is_free_preview' => false,
                    'link_video' => 'https://www.youtube.com/watch?v=sample2',
                    'file_materi' => null,
                ],
                (object)[
                    'id' => 4,
                    'urutan' => 4,
                    'judul_materi' => 'Evaluasi Structural Model (Inner Model), Uji Hipotesis, & Efek Mediasi/Moderasi',
                    'tipe_materi' => 'Video',
                    'durasi_menit' => 60,
                    'deskripsi' => 'Analisis R-Square, f-Square, Q-Square, p-value, t-statistic, dan pengujian efek mediasi bootstrapping.',
                    'is_free_preview' => false,
                    'link_video' => 'https://www.youtube.com/watch?v=sample3',
                    'file_materi' => null,
                ],
                (object)[
                    'id' => 5,
                    'urutan' => 5,
                    'judul_materi' => 'Master Template & Panduan Penulisan Narasi Hasil Penelitian Bab 4 Standar Sidang',
                    'tipe_materi' => 'Dokumen PDF',
                    'durasi_menit' => 30,
                    'deskripsi' => 'Template Word lengkap format tata letak tabel baku dan narasi penolakan/penerimaan hipotesis siap dikonsultasikan ke dosen pembimbing.',
                    'is_free_preview' => false,
                    'link_video' => null,
                    'file_materi' => 'template_narasi_bab4.docx',
                ],
            ]);
            $terdaftarCount = 18;
            $sisaKuota = 32;
        } else {
            $materis = $kelas->materis ?? collect();
            $terdaftarCount = $kelas->transaksis ? $kelas->transaksis->whereIn('status_pembayaran', ['Lunas', 'Menunggu Verifikasi'])->count() : 0;
            $kuotaMaksimal = $kelas->kuota_maksimal ?: 50;
            $sisaKuota = max(0, $kuotaMaksimal - $terdaftarCount);
            if ($sisaKuota === 0 && $kelas->status === 'Dibuka') {
                $sisaKuota = 5;
            }
        }

        $userId = $user ? ($user->id_user ?? $user->id) : null;
        $alreadyPurchased = false;
        if ($userId) {
            $alreadyPurchased = TransaksiPelatihan::where('user_id', $userId)
                ->where('kelas_id', $id)
                ->where('status_pembayaran', 'Lunas')
                ->exists();
        }

        $kuotaMaksimal = $kelas->kuota_maksimal ?: 50;
        $totalDurasiMenit = $materis->sum('durasi_menit');
        $totalSesi = $materis->count();

        return view('pelatihan::detail', compact(
            'kelas',
            'materis',
            'user',
            'alreadyPurchased',
            'kuotaMaksimal',
            'terdaftarCount',
            'sisaKuota',
            'totalDurasiMenit',
            'totalSesi'
        ));
    }

    public function daftar($id)
    {
        return redirect()->route('pelatihan.detail', $id);
    }
}

