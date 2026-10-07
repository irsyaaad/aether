<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. master_programs
        if (!Schema::hasTable('master_programs')) {
            Schema::create('master_programs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode_program', 50)->unique();
                $table->string('nama_program', 150);
                $table->string('kategori', 50)->nullable();
                $table->string('jenjang', 50)->nullable();
                $table->text('deskripsi')->nullable();
                $table->decimal('biaya_standar', 15, 2)->default(0);
                $table->integer('durasi_sesi')->default(60);
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();
            });

            DB::table('master_programs')->insert([
                [
                    'kode_program' => 'PRG-SKR-01',
                    'nama_program' => 'Pendampingan Skripsi S1 (Kuantitatif & Bab 4-5)',
                    'kategori' => 'Bimbingan Skripsi',
                    'jenjang' => 'S1 (Sarjana)',
                    'deskripsi' => 'Paket bimbingan intensif 1-on-1 analisis statistik skripsi sampai dinyatakan lulus sidang.',
                    'biaya_standar' => 650000,
                    'durasi_sesi' => 90,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_program' => 'PRG-TSS-01',
                    'nama_program' => 'Bimbingan Tesis S2 (SEM-PLS & Ekonometrika Lanjutan)',
                    'kategori' => 'Bimbingan Tesis',
                    'jenjang' => 'S2 (Magister)',
                    'deskripsi' => 'Pendampingan tesis model kompleks mediasi-moderasi, data panel, dan publikasi jurnal bereputasi.',
                    'biaya_standar' => 1250000,
                    'durasi_sesi' => 90,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_program' => 'PRG-OD-PLS',
                    'nama_program' => 'Olah Data SEM-PLS 4 Full Output & Interpretasi',
                    'kategori' => 'Olah Data',
                    'jenjang' => 'S1 / S2',
                    'deskripsi' => 'Pemrosesan data SmartPLS lengkap uji validitas konvergen, diskriminan, inner model, dan narasi pembahasan.',
                    'biaya_standar' => 450000,
                    'durasi_sesi' => 60,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 2. master_mentors
        if (!Schema::hasTable('master_mentors')) {
            Schema::create('master_mentors', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nama_lengkap', 150);
                $table->string('gelar', 50)->nullable();
                $table->string('spesialisasi', 100);
                $table->string('email', 100)->nullable();
                $table->string('no_hp', 30)->nullable();
                $table->string('pendidikan_terakhir', 50)->nullable();
                $table->string('institusi', 150)->nullable();
                $table->string('keahlian_software', 255)->nullable();
                $table->decimal('tarif_per_sesi', 15, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();
            });

            DB::table('master_mentors')->insert([
                [
                    'nama_lengkap' => 'Nur Atika Rochmah',
                    'gelar' => 'M.Si.',
                    'spesialisasi' => 'Statistika Kuantitatif & SEM-PLS',
                    'email' => 'nur.atika@aether.test',
                    'no_hp' => '081234567891',
                    'pendidikan_terakhir' => 'S2 Statistika Terapan',
                    'institusi' => 'Institut Teknologi Sepuluh Nopember',
                    'keahlian_software' => 'SmartPLS 4, SPSS 29, AMOS',
                    'tarif_per_sesi' => 150000,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_lengkap' => 'Deny Aditya Pratama',
                    'gelar' => 'S.Stat., M.E.',
                    'spesialisasi' => 'Ekonometrika & Regresi Data Panel',
                    'email' => 'deny.aditya@aether.test',
                    'no_hp' => '081234567892',
                    'pendidikan_terakhir' => 'S2 Ilmu Ekonomi',
                    'institusi' => 'Universitas Indonesia',
                    'keahlian_software' => 'EViews 12, Stata 17, R',
                    'tarif_per_sesi' => 175000,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'nama_lengkap' => 'Alessandra Natasya Panie',
                    'gelar' => 'S.Psi., M.Psi.',
                    'spesialisasi' => 'Riset Kualitatif & Psikometri',
                    'email' => 'alessandra@aether.test',
                    'no_hp' => '081234567893',
                    'pendidikan_terakhir' => 'S2 Psikologi',
                    'institusi' => 'Universitas Gadjah Mada',
                    'keahlian_software' => 'NVivo, ATLAS.ti, SPSS',
                    'tarif_per_sesi' => 150000,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // 3. master_kelas
        if (!Schema::hasTable('master_kelas')) {
            Schema::create('master_kelas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode_kelas', 50)->unique();
                $table->string('judul', 255);
                $table->unsignedBigInteger('program_id')->nullable();
                $table->unsignedBigInteger('mentor_id')->nullable();
                $table->string('level', 50)->default('Semua Level');
                $table->string('kategori', 100)->nullable();
                $table->text('deskripsi')->nullable();
                $table->text('silabus_singkat')->nullable();
                $table->decimal('harga', 15, 2)->default(0);
                $table->integer('kuota_maksimal')->default(50);
                $table->date('tanggal_mulai')->nullable();
                $table->date('tanggal_selesai')->nullable();
                $table->string('tipe_kelas', 50)->default('Online Webinar');
                $table->string('link_meeting', 255)->nullable();
                $table->string('badge', 50)->nullable();
                $table->string('status', 50)->default('Dibuka');
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();
            });

            $mentor = DB::table('master_mentors')->first();
            $mentorId = $mentor ? $mentor->id : null;
            $program = DB::table('master_programs')->first();
            $programId = $program ? $program->id : null;

            DB::table('master_kelas')->insert([
                [
                    'kode_kelas' => 'KLS-PLS-01',
                    'judul' => 'Mastering SEM-PLS 4: Dari Kerangka Konseptual Hingga Interpretasi Bab 4',
                    'program_id' => $programId,
                    'mentor_id' => $mentorId,
                    'level' => 'Intermediate',
                    'kategori' => 'Kuantitatif & SEM',
                    'deskripsi' => 'Panduan lengkap uji validitas, reliabilitas, inner model, f-square, R-square, hingga mediasi moderasi siap pakai sidang.',
                    'silabus_singkat' => '8 Sesi Video Praktik + Template Word Bab 4 + Live Q&A',
                    'harga' => 250000,
                    'kuota_maksimal' => 100,
                    'tanggal_mulai' => now()->addDays(5)->toDateString(),
                    'tanggal_selesai' => now()->addDays(8)->toDateString(),
                    'tipe_kelas' => 'Bootcamp Intensif',
                    'link_meeting' => 'https://meet.google.com/aether-pls',
                    'badge' => 'Terpopuler',
                    'status' => 'Dibuka',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_kelas' => 'KLS-EVW-02',
                    'judul' => 'Workshop Regresi Data Panel Menggunakan EViews & Stata untuk Tesis Magister',
                    'program_id' => $programId,
                    'mentor_id' => $mentorId,
                    'level' => 'Advanced',
                    'kategori' => 'Ekonometrika',
                    'deskripsi' => 'Bedah tuntas uji Chow, Hausman, Lagrange Multiplier (LM), estimasi FEM, REM, CEM, dan teknik penanganan heteroskedastisitas.',
                    'silabus_singkat' => '6 Sesi Praktik Data Panel + Do-file Stata & Workfile EViews',
                    'harga' => 350000,
                    'kuota_maksimal' => 50,
                    'tanggal_mulai' => now()->addDays(12)->toDateString(),
                    'tanggal_selesai' => now()->addDays(15)->toDateString(),
                    'tipe_kelas' => 'Online Webinar',
                    'link_meeting' => 'https://zoom.us/j/aether-eviews',
                    'badge' => 'Intensif',
                    'status' => 'Dibuka',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_kelas' => 'KLS-SPS-03',
                    'judul' => 'Bootcamp Mahir SPSS: Uji Validitas, Reliabilitas, & Asumsi Klasik Tanpa Pusing',
                    'program_id' => $programId,
                    'mentor_id' => $mentorId,
                    'level' => 'Pemula',
                    'kategori' => 'Statistik Dasar',
                    'deskripsi' => 'Pelatihan komprehensif mengolah data kuisioner skala likert, uji normalitas, multikolinearitas, heteroskedastisitas, dan regresi linier.',
                    'silabus_singkat' => '10 Sesi Interaktif + Master Dataset .SAV Latihan',
                    'harga' => 175000,
                    'kuota_maksimal' => 150,
                    'tanggal_mulai' => now()->addDays(20)->toDateString(),
                    'tanggal_selesai' => now()->addDays(25)->toDateString(),
                    'tipe_kelas' => 'Online Webinar',
                    'link_meeting' => 'https://meet.google.com/aether-spss',
                    'badge' => 'Best Seller',
                    'status' => 'Dibuka',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // 4. master_materis
        if (!Schema::hasTable('master_materis')) {
            Schema::create('master_materis', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('kelas_id');
                $table->integer('urutan')->default(1);
                $table->string('judul_materi', 255);
                $table->string('tipe_materi', 50)->default('Video');
                $table->integer('durasi_menit')->default(30);
                $table->string('link_video', 255)->nullable();
                $table->string('file_materi', 255)->nullable();
                $table->text('deskripsi')->nullable();
                $table->boolean('is_free_preview')->default(false);
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();
            });

            $kelas = DB::table('master_kelas')->first();
            $kelasId = $kelas ? $kelas->id : 1;

            DB::table('master_materis')->insert([
                [
                    'kelas_id' => $kelasId,
                    'urutan' => 1,
                    'judul_materi' => 'Pengenalan SmartPLS 4 & Format Dataset yang Benar',
                    'tipe_materi' => 'Video',
                    'durasi_menit' => 45,
                    'link_video' => 'https://youtube.com/watch?v=sample1',
                    'file_materi' => null,
                    'deskripsi' => 'Pengenalan antarmuka SmartPLS 4, import file csv/sav, dan pengecekan missing value.',
                    'is_free_preview' => true,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kelas_id' => $kelasId,
                    'urutan' => 2,
                    'judul_materi' => 'Evaluasi Measurement Model (Outer Model): Validitas & Reliabilitas',
                    'tipe_materi' => 'Video',
                    'durasi_menit' => 60,
                    'link_video' => 'https://youtube.com/watch?v=sample2',
                    'file_materi' => null,
                    'deskripsi' => 'Pengujian Cronbach Alpha, Composite Reliability, AVE, HTMT, dan Cross Loadings.',
                    'is_free_preview' => false,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kelas_id' => $kelasId,
                    'urutan' => 3,
                    'judul_materi' => 'Template & Panduan Narasi Pembahasan Bab 4 Siap Sidang',
                    'tipe_materi' => 'Dokumen PDF',
                    'durasi_menit' => 0,
                    'link_video' => null,
                    'file_materi' => 'template_bab4_pls.docx',
                    'deskripsi' => 'Template laporan Word format standar universitas dengan tata letak tabel dan penafsiran hipotesis.',
                    'is_free_preview' => false,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kelas_id' => $kelasId,
                    'urutan' => 4,
                    'judul_materi' => 'Master Dataset Praktik: 150 Responden Kuisioner Skala Likert',
                    'tipe_materi' => 'Dataset Praktik',
                    'durasi_menit' => 0,
                    'link_video' => null,
                    'file_materi' => 'dataset_latihan_150n.csv',
                    'deskripsi' => 'Dataset riil untuk latihan uji mediasi dan moderasi mandiri.',
                    'is_free_preview' => false,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 5. master_metode_risets
        if (!Schema::hasTable('master_metode_risets')) {
            Schema::create('master_metode_risets', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode_metode', 50)->unique();
                $table->string('nama_metode', 150);
                $table->string('kategori', 50)->default('Kuantitatif');
                $table->string('software_rekomendasi', 100)->nullable();
                $table->text('deskripsi')->nullable();
                $table->text('uji_statistik_terkait')->nullable();
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();
            });

            DB::table('master_metode_risets')->insert([
                [
                    'kode_metode' => 'MET-PLS',
                    'nama_metode' => 'Partial Least Squares Structural Equation Modeling (PLS-SEM)',
                    'kategori' => 'Kuantitatif',
                    'software_rekomendasi' => 'SmartPLS 3 / 4, WarpPLS',
                    'deskripsi' => 'Pendekatan SEM berbasis varian untuk model prediktif, indikator formatif & reflektif, serta sampel kecil-sedang.',
                    'uji_statistik_terkait' => 'Convergent Validity (AVE), Discriminant Validity (HTMT), Path Coefficients, R-Square, Q-Square Predict.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_metode' => 'MET-CBSEM',
                    'nama_metode' => 'Covariance-Based SEM (CB-SEM)',
                    'kategori' => 'Kuantitatif',
                    'software_rekomendasi' => 'IBM SPSS AMOS, LISREL',
                    'deskripsi' => 'Pengujian konfirmasi teori (theory testing) dengan asumsi normalitas multivariate yang ketat.',
                    'uji_statistik_terkait' => 'Goodness of Fit (Chi-Square, RMSEA, GFI, CFI, TLI), Normalitas Multivariat, Modifikasi Model.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_metode' => 'MET-PANEL',
                    'nama_metode' => 'Regresi Data Panel Dinamis & Statis',
                    'kategori' => 'Ekonometrika',
                    'software_rekomendasi' => 'EViews 12, Stata 17',
                    'deskripsi' => 'Analisis data gabungan cross-section dan time-series untuk riset perbankan, keuangan, dan makroekonomi.',
                    'uji_statistik_terkait' => 'Uji Chow (CEM vs FEM), Uji Hausman (FEM vs REM), Uji Lagrange Multiplier, Uji Asumsi Heteroskedastisitas & Autokorelasi.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_metode' => 'MET-KUALI',
                    'nama_metode' => 'Analisis Tematik Kualitatif & Fenomenologi',
                    'kategori' => 'Kualitatif',
                    'software_rekomendasi' => 'NVivo, ATLAS.ti',
                    'deskripsi' => 'Kodifikasi wawancara mendalam, transkrip FGD, dan triangulasi data tematik.',
                    'uji_statistik_terkait' => 'Thematic Coding, Word Frequency Query, Matrix Coding, Credibility & Dependability.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        // 6. transaksi_bimbingans (Layanan Bimbingan Mahasiswa)
        if (!Schema::hasTable('transaksi_bimbingans')) {
            Schema::create('transaksi_bimbingans', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nomor_tiket', 50)->unique();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('nama_mahasiswa', 150);
                $table->string('nim', 50)->nullable();
                $table->string('universitas', 150)->nullable();
                $table->string('jurusan', 100)->nullable();
                $table->string('jenjang', 50)->default('S1');
                $table->string('judul_skripsi', 255);
                $table->string('topik_bidang', 100)->nullable();
                $table->string('metode_riset', 100)->nullable();
                $table->string('software_analisis', 100)->nullable();
                $table->string('paket_bimbingan', 100)->default('Paket Standar Bab 4-5');
                $table->unsignedBigInteger('mentor_id')->nullable();
                $table->dateTime('jadwal_konsultasi')->nullable();
                $table->string('link_zoom', 255)->nullable();
                $table->string('file_draft_skripsi', 255)->nullable();
                $table->text('keluhan_kendala')->nullable();
                $table->text('catatan_mentor')->nullable();
                $table->string('status', 50)->default('Menunggu Review'); // Menunggu Review, Dijadwalkan, Selesai, Dibatalkan
                $table->decimal('biaya', 15, 2)->default(0);
                $table->string('status_pembayaran', 50)->default('Menunggu Pembayaran'); // Lunas, Belum Lunas
                $table->softDeletes();
                $table->timestamps();
            });

            DB::table('transaksi_bimbingans')->insert([
                [
                    'nomor_tiket' => 'BMB-202610-001',
                    'nama_mahasiswa' => 'Budi Santoso',
                    'nim' => '200810201045',
                    'universitas' => 'Universitas Indonesia',
                    'jurusan' => 'Manajemen Pemasaran',
                    'jenjang' => 'S1',
                    'judul_skripsi' => 'Pengaruh Green Marketing dan Brand Trust Terhadap Keputusan Pembelian dengan Kepuasan Pelanggan sebagai Variabel Mediasi',
                    'topik_bidang' => 'Pemasaran & Perilaku Konsumen',
                    'metode_riset' => 'Kuantitatif Eksplanatori',
                    'software_analisis' => 'SmartPLS 4',
                    'paket_bimbingan' => 'Pendampingan Skripsi S1 (Kuantitatif & Bab 4-5)',
                    'mentor_id' => 1,
                    'jadwal_konsultasi' => now()->addDays(2)->setTime(14, 0),
                    'link_zoom' => 'https://meet.google.com/aether-bmb-01',
                    'file_draft_skripsi' => 'draft_bab1_3_budi.pdf',
                    'keluhan_kendala' => 'Nilai Discriminant Validity HTMT di atas 0.90 dan bingung cara interpretasi efek mediasi di SmartPLS 4.',
                    'catatan_mentor' => 'Perlu pengecekan cross loadings dan outer loadings indikator yang rendah di bawah 0.708.',
                    'status' => 'Dijadwalkan',
                    'biaya' => 650000,
                    'status_pembayaran' => 'Lunas',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }

        // 7. transaksi_olahdatas (Layanan Olah Data Mahasiswa)
        if (!Schema::hasTable('transaksi_olahdatas')) {
            Schema::create('transaksi_olahdatas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nomor_order', 50)->unique();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('nama_mahasiswa', 150);
                $table->string('no_whatsapp', 30)->nullable();
                $table->string('email', 100)->nullable();
                $table->string('universitas', 150)->nullable();
                $table->string('jurusan', 100)->nullable();
                $table->string('judul_penelitian', 255);
                $table->string('software_digunakan', 100);
                $table->string('metode_analisis', 100);
                $table->integer('jumlah_sampel')->default(100);
                $table->integer('jumlah_variabel')->default(3);
                $table->string('file_dataset', 255)->nullable();
                $table->string('file_kuesioner', 255)->nullable();
                $table->string('file_output_hasil', 255)->nullable();
                $table->text('catatan_khusus')->nullable();
                $table->string('status_pengerjaan', 50)->default('Proses Running Data'); // Antrean, Proses Running Data, Review Hasil, Selesai
                $table->decimal('total_biaya', 15, 2)->default(0);
                $table->string('status_pembayaran', 50)->default('Lunas');
                $table->softDeletes();
                $table->timestamps();
            });

            DB::table('transaksi_olahdatas')->insert([
                [
                    'nomor_order' => 'OD-202610-001',
                    'nama_mahasiswa' => 'Rina Wijayanti',
                    'no_whatsapp' => '085712345678',
                    'email' => 'rina.wijayanti@mhs.unair.ac.id',
                    'universitas' => 'Universitas Airlangga',
                    'jurusan' => 'Akuntansi',
                    'judul_penelitian' => 'Analisis Pengaruh Good Corporate Governance dan Profitabilitas Terhadap Nilai Perusahaan dengan Kebijakan Dividen Sebagai Variabel Moderasi',
                    'software_digunakan' => 'EViews 12',
                    'metode_analisis' => 'Regresi Data Panel',
                    'jumlah_sampel' => 150,
                    'jumlah_variabel' => 4,
                    'file_dataset' => 'dataset_keuangan_idx.xlsx',
                    'file_kuesioner' => null,
                    'file_output_hasil' => 'output_eviews_bab4_rina.docx',
                    'catatan_khusus' => 'Mohon diuji uji Chow dan Hausman untuk menentukan model terbaik (FEM/REM) beserta interpretasi hipotesisnya.',
                    'status_pengerjaan' => 'Proses Running Data',
                    'total_biaya' => 450000,
                    'status_pembayaran' => 'Lunas',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_olahdatas');
        Schema::dropIfExists('transaksi_bimbingans');
        Schema::dropIfExists('master_metode_risets');
        Schema::dropIfExists('master_materis');
        Schema::dropIfExists('master_kelas');
        Schema::dropIfExists('master_mentors');
        Schema::dropIfExists('master_programs');
    }
};
