<?php

namespace Modules\Pelatihan\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
                'badge' => 'Best Seller'
            ]
        ];

        // Load dinamis dari MasterKelas jika tabel ada di database
        if (\Illuminate\Support\Facades\Schema::hasTable('master_kelas')) {
            $dbCourses = \Modules\Master\Entities\MasterKelas::with(['mentor', 'materis'])->where('is_active', true)->get();
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
                    'badge' => $c->badge ?? 'Riset'
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
        return view('pelatihan::kelas-saya');
    }

    public function sertifikat()
    {
        return view('pelatihan::sertifikat');
    }

    public function show($id)
    {
        return view('pelatihan::show', compact('id'));
    }

    public function daftar($id)
    {
        return redirect()->route('pelatihan.kelas-saya')->with('success', 'Selamat, pendaftaran kelas pelatihan berhasil! Materi dan tautan sesi live telah tersedia.');
    }
}
