<?php

namespace Modules\Bimbingan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;

class BimbinganController extends Controller
{
    public function index()
    {
        $stats = [
            'total' => 5,
            'aktif' => 2,
            'selesai' => 3,
            'jadwal_mendatang' => 1,
        ];

        $sessions = [
            [
                'id' => 1,
                'judul' => 'Pengaruh E-WOM dan Brand Image Terhadap Keputusan Pembelian Generasi Z',
                'mentor' => 'Nur Atika Rochmah, M.Si.',
                'bab' => 'Bab 4 - Hasil & Pembahasan',
                'tanggal' => '10 Okt 2026, 14:00 WIB',
                'media' => 'Google Meet (Online)',
                'status' => 'Dijadwalkan',
                'catatan' => 'Fokus pembahasan pada interpretasi koefisien jalur dan uji efek mediasi.'
            ],
            [
                'id' => 2,
                'judul' => 'Analisis Volatilitas Saham Sektor Perbankan Menggunakan Metode GARCH',
                'mentor' => 'Deny Aditya Pratama, S.Stat.',
                'bab' => 'Bab 3 - Metodologi Riset',
                'tanggal' => '12 Okt 2026, 19:30 WIB',
                'media' => 'Zoom Meeting',
                'status' => 'Menunggu Konfirmasi',
                'catatan' => 'Pemeriksaan stasioneritas data runtut waktu dan spesifikasi model estimasi.'
            ],
            [
                'id' => 3,
                'judul' => 'Studi Fenomenologi Pengalaman UMKM dalam Digitalisasi Keuangan',
                'mentor' => 'Alessandra Natasya Panie, S.Psi.',
                'bab' => 'Bab 4 & Coding NVivo',
                'tanggal' => '05 Okt 2026, 10:00 WIB',
                'media' => 'Google Meet',
                'status' => 'Selesai',
                'catatan' => 'Triangulasi data dan validasi coding tree antar informan tuntas.'
            ]
        ];

        return view('bimbingan::index', compact('stats', 'sessions'));
    }

    public function create()
    {
        $mentors = [
            'Nur Atika Rochmah, M.Si. (Spesialis Kuantitatif & SEM-PLS)',
            'Deny Aditya Pratama, S.Stat. (Spesialis Ekonometrika & Regresi Panel)',
            'Alessandra Natasya Panie, S.Psi. (Spesialis Kualitatif & NVivo)',
            'Arrum Rochmani Hidayah, M.Kom. (Spesialis Machine Learning & Big Data)',
        ];

        return view('bimbingan::create', compact('mentors'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul_penelitian' => 'required|string|max:255',
            'topik_bab' => 'required|string',
            'mentor' => 'required|string',
            'tanggal_preferensi' => 'required|date',
            'catatan' => 'nullable|string',
        ]);

        return redirect()->route('bimbingan.index')->with('success', 'Pengajuan bimbingan berhasil dikirim. Mentor akan mengonfirmasi jadwal Anda segera!');
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
