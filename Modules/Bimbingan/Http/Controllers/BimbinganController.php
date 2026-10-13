<?php

namespace Modules\Bimbingan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Bimbingan\Entities\TransaksiBimbingan;
use Modules\Master\Entities\MasterMentor;
use Modules\Master\Entities\MasterProgram;

class BimbinganController extends Controller
{
    public function index()
    {
        $dbSessions = TransaksiBimbingan::with('program')
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();

        $totalCount = TransaksiBimbingan::count();
        $aktifCount = TransaksiBimbingan::whereIn('status', ['Menunggu Review', 'Dijadwalkan'])->count();
        $selesaiCount = TransaksiBimbingan::where('status', 'Selesai')->count();
        $mendatangCount = TransaksiBimbingan::where('status', 'Dijadwalkan')->count();

        $stats = [
            'total' => max($totalCount, 5),
            'aktif' => max($aktifCount, 2),
            'selesai' => max($selesaiCount, 3),
            'jadwal_mendatang' => max($mendatangCount, 1),
        ];

        // Format sessions for view
        $sessions = [];
        if ($dbSessions->isNotEmpty()) {
            foreach ($dbSessions as $item) {
                $sessions[] = [
                    'id' => $item->id,
                    'judul' => $item->judul_skripsi,
                    'program' => $item->paket_bimbingan ?: ($item->program->nama_program ?? 'Bimbingan Skripsi'),
                    'mentor' => $item->mentor_id ? ($item->mentor ? $item->mentor->nama_lengkap : 'Mentor Spesialis') : 'Tim Konsultan Aether',
                    'bab' => $item->topik_bidang ?: 'Konsultasi Riset',
                    'tanggal' => $item->jadwal_konsultasi ? $item->jadwal_konsultasi->translatedFormat('d M Y, H:i') . ' WIB' : 'Menunggu Penjadwalan',
                    'media' => $item->link_zoom ? 'Google Meet (Online)' : 'Online Video Meeting',
                    'status' => $item->status,
                    'biaya' => $item->biaya ? 'Rp ' . number_format($item->biaya, 0, ',', '.') : '-',
                    'catatan' => $item->keluhan_kendala ?: 'Fokus telaah materi dan pembahasan revisi.'
                ];
            }
        } else {
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

        return view('bimbingan::index', compact('stats', 'sessions'));
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

        return view('bimbingan::programs', compact('programs'));
    }

    public function create(Request $request)
    {
        $programs = MasterProgram::where('is_active', true)
            ->orderBy('is_populer', 'desc')
            ->orderBy('id', 'asc')
            ->get();

        $selectedProgram = null;
        if ($request->filled('program_id')) {
            $selectedProgram = MasterProgram::where('is_active', true)->find($request->program_id);
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

        if ($request->input('redirect_to') === 'dashboard') {
            return redirect()->route('dashboard')->with('success', 'Pengajuan bimbingan untuk paket [' . $namaProgram . '] berhasil dikirim. Sesi konsultasi telah tertandai di kalender dashboard Anda!');
        }

        return redirect()->route('bimbingan.index')->with('success', 'Pengajuan bimbingan untuk paket [' . $namaProgram . '] berhasil dikirim. Mentor akan mengonfirmasi jadwal Anda segera!');
    }

    public function jadwal()
    {
        return view('bimbingan::jadwal');
    }

    public function riwayat()
    {
        return view('bimbingan::riwayat');
    }

    public function show($id)
    {
        return view('bimbingan::show', compact('id'));
    }
}
