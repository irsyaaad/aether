<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Master\Entities\MasterProgram;

class MasterProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $programs = [
            [
                'kode_program' => 'PRG-SKR-FULL',
                'nama_program' => 'Paket Bimbingan Skripsi Komprehensif (Full Bab 1 s/d Bab 5)',
                'kategori' => 'Bimbingan Skripsi',
                'jenjang' => 'S1 (Sarjana)',
                'deskripsi' => 'Paket bimbingan terlengkap all-in-one dari perumusan judul, proposal Bab 1-3, olah data Bab 4, hingga kesimpulan Bab 5 dan simulasi sidang ujian sarjana.',
                'biaya_standar' => 1500000,
                'durasi_sesi' => 90,
                'jumlah_sesi' => 8,
                'target_bab' => 'Full Bab 1 s/d Bab 5 & Sidang',
                'badge' => 'Paling Populer',
                'is_populer' => true,
                'fasilitas' => "8x Sesi Bimbingan Intensif 1-on-1 (Google Meet / Zoom)\nPendampingan bedah Bab 1 s.d. Bab 5 hingga ACC Dosen\nBantuan perumusan hipotesis & instrumen kuesioner\nPengolahan data statistik (SPSS / SmartPLS / AMOS)\nFree pengecekan Turnitin (similarity index aman)\nFree konsultasi via WhatsApp selama masa bimbingan\nSimulasi tanya-jawab persiapan ujian sidang skripsi",
                'is_active' => true,
            ],
            [
                'kode_program' => 'PRG-SKR-PROP',
                'nama_program' => 'Paket Bimbingan Proposal Skripsi (Bab 1, 2, & 3)',
                'kategori' => 'Bimbingan Skripsi',
                'jenjang' => 'S1 (Sarjana)',
                'deskripsi' => 'Fokus mendalam pada pemantapan latar belakang, kajian teori, dan metodologi riset agar cepat lolos seminar proposal (Sempro) tanpa revisi mayor.',
                'biaya_standar' => 750000,
                'durasi_sesi' => 90,
                'jumlah_sesi' => 4,
                'target_bab' => 'Bab 1 s/d Bab 3 (Proposal)',
                'badge' => 'Best Value',
                'is_populer' => false,
                'fasilitas' => "4x Sesi Konsultasi 1-on-1 Terfokus\nBedah research gap & novelty penelitian\nKajian literatur & pencarian jurnal rujukan bereputasi\nPenyusunan kerangka konseptual & instrumen riset\nReview pra-seminar proposal (Sempro)",
                'is_active' => true,
            ],
            [
                'kode_program' => 'PRG-SKR-HASIL',
                'nama_program' => 'Paket Bimbingan Hasil & Pembahasan (Bab 4 & Bab 5)',
                'kategori' => 'Bimbingan Skripsi',
                'jenjang' => 'S1 (Sarjana)',
                'deskripsi' => 'Solusi bagi mahasiswa yang datanya sudah siap namun bingung merangkai kata pembahasan hasil dan menjawab catatan revisi dosen pembimbing.',
                'biaya_standar' => 850000,
                'durasi_sesi' => 90,
                'jumlah_sesi' => 4,
                'target_bab' => 'Bab 4 & Bab 5 (Hasil & Pembahasan)',
                'badge' => 'Rekomendasi',
                'is_populer' => false,
                'fasilitas' => "4x Sesi Pembahasan Hasil Analisis Data\nBimbingan penulisan narasi interpretasi output statistik\nSinkronisasi hasil uji hipotesis dengan teori Bab 2\nPembahasan implikasi manajerial & keterbatasan riset\nFree template format penyusunan Bab 4 standar universitas",
                'is_active' => true,
            ],
            [
                'kode_program' => 'PRG-SKR-SIDANG',
                'nama_program' => 'Paket Simulasi & Defense Ujian Sidang Skripsi',
                'kategori' => 'Bimbingan Skripsi',
                'jenjang' => 'S1 (Sarjana)',
                'deskripsi' => 'Persiapan matang sebelum hari H sidang skripsi. Latihan presentasi profesional dan strategi cerdas menjawab pertanyaan kritis dosen penguji.',
                'biaya_standar' => 450000,
                'durasi_sesi' => 90,
                'jumlah_sesi' => 2,
                'target_bab' => 'Presentasi PPT & Tanya Jawab Sidang',
                'badge' => 'Kilat',
                'is_populer' => false,
                'fasilitas' => "2x Sesi Simulasi Sidang Ujian (Mock Defense)\nReview dan pembenahan slide presentasi PPT\nLatihan prediksi 25+ pertanyaan jebakan dosen penguji\nBedah poin kritis metodologi & interpretasi temuan\nUmpan balik langsung teknik komunikasi & mental defense",
                'is_active' => true,
            ],
            [
                'kode_program' => 'PRG-TSS-01',
                'nama_program' => 'Bimbingan Tesis Magister (S2 SEM-PLS & Ekonometrika)',
                'kategori' => 'Bimbingan Tesis',
                'jenjang' => 'S2 (Magister)',
                'deskripsi' => 'Pendampingan tesis model kompleks mediasi-moderasi, data panel, dan publikasi jurnal bereputasi untuk mahasiswa program pascasarjana magister.',
                'biaya_standar' => 1850000,
                'durasi_sesi' => 90,
                'jumlah_sesi' => 8,
                'target_bab' => 'Full Tesis S2 & Manuskrip Jurnal',
                'badge' => 'Tesis S2',
                'is_populer' => false,
                'fasilitas' => "8x Sesi Bimbingan bersama Mentor Ahli Magister/Doktor\nModel riset lanjutan (SEM-PLS, Data Panel Eviews/Stata, NVivo)\nAsistensi penulisan manuskrip artikel jurnal bereputasi\nPendampingan revisi tim dewan penguji tesis",
                'is_active' => true,
            ],
            [
                'kode_program' => 'PRG-OD-PLS',
                'nama_program' => 'Paket Olah Data Skripsi SEM-PLS / SPSS Full Output',
                'kategori' => 'Olah Data',
                'jenjang' => 'S1 / S2',
                'deskripsi' => 'Pemrosesan data SmartPLS/SPSS lengkap dengan uji validitas, reliabilitas, inner model, dan narasi pembahasan siap pakai.',
                'biaya_standar' => 450000,
                'durasi_sesi' => 60,
                'jumlah_sesi' => 2,
                'target_bab' => 'Output Software & Interpretasi Bab 4',
                'badge' => 'Hemat',
                'is_populer' => false,
                'fasilitas' => "2x Sesi Diskusi Hasil Running Software\nRunning data uji validitas, reliabilitas, & hipotesis\nFile output lengkap + rangkuman tabel Word\nNarasi penjelasan arti angka p-value & R-square",
                'is_active' => true,
            ],
        ];

        foreach ($programs as $prog) {
            MasterProgram::updateOrCreate(
                ['kode_program' => $prog['kode_program']],
                $prog
            );
        }
    }
}
