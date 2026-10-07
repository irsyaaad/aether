<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('karyawan', function (Blueprint $table) {
            $table->id();

            // Data Pribadi
            $table->string('nama_lengkap');
            $table->enum('jabatan', ['Direktur', 'Marketing', 'Staff Operasional', 'Staff Keuangan', 'Mekanik', 'Sopir'])->nullable();
            $table->string('nama_panggilan')->nullable();
            $table->string('nik_ktp')->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('agama')->nullable();
            $table->string('status_pernikahan')->nullable();
            $table->text('alamat_ktp')->nullable();
            $table->text('alamat_domisili')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->string('email')->nullable();

            // Dokumen Identitas
            $table->string('nomor_npwp')->nullable();
            $table->string('nomor_bpjs_kesehatan')->nullable();
            $table->string('nomor_bpjs_ketenagakerjaan')->nullable();
            $table->string('nomor_sim')->nullable();

            // Gaji & Perbankan
            $table->bigInteger('gaji_pokok')->nullable();
            $table->bigInteger('tunjangan_jabatan')->nullable();
            $table->bigInteger('tunjangan_makan')->nullable();
            $table->bigInteger('tunjangan_transport')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->string('nama_bank')->nullable();

            // Data Tambahan
            $table->string('golongan_darah')->nullable();
            $table->string('kontak_darurat_nama')->nullable();
            $table->string('kontak_darurat_nomor')->nullable();
            $table->string('kontak_darurat_hubungan')->nullable();

            // Upload Dokumen
            $table->string('dokumen_ktp')->nullable();
            $table->string('dokumen_kk')->nullable();
            $table->string('dokumen_sim')->nullable();
            $table->string('dokumen_npwp')->nullable();
            $table->string('dokumen_ijazah')->nullable();
            $table->string('foto_diri')->nullable();

            // Marketplace ID
            $table->string('id_spx')->nullable();

            // Rekap (boleh smallint atau integer)
            $table->integer('rekap_absen')->default(0);
            $table->integer('rekap_lembur')->default(0);
            $table->integer('saldo_cuti')->default(0);

            // Catatan
            $table->text('catatan_kinerja')->nullable();
            $table->text('training')->nullable();
            $table->enum('status', ['aktif', 'tidak_aktif'])->default('aktif');

            // Master & Relasi Organisasi (Baru)
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('employee_number')->nullable()->unique();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();
            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('position_id')->nullable();
            $table->unsignedBigInteger('manager_id')->nullable();
            $table->date('join_date')->nullable();
            $table->string('employment_status')->default('Tetap')->nullable();
            $table->string('avatar')->nullable();
            $table->boolean('is_active')->default(true);

            $table->index('company_id');
            $table->index('department_id');
            $table->index('position_id');
            $table->index('manager_id');
            $table->index('is_active');

            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });

        $employees = [
            [
                'nama_lengkap'               => 'Meryana',
                'nama_panggilan'             => 'Meryana',
                'jabatan'                    => 'Staff Operasional',
                'nik_ktp'                    => '3578266103770003',
                'tempat_lahir'               => 'Surabaya',
                'tanggal_lahir'              => '1977-03-21',
                'jenis_kelamin'              => 'Perempuan',
                'agama'                      => 'Kristen',
                'status_pernikahan'          => 'Menikah',
                'alamat_ktp'                 => 'Jl. Mawar No. 12, Surabaya',
                'alamat_domisili'            => 'Jl. Anggrek No. 4, Surabaya',
                'nomor_hp'                   => '081234567001',
                'email'                      => 'meryana@example.com',

                'nomor_npwp'                 => '12.345.678.9-012.345',
                'nomor_bpjs_kesehatan'       => '987654321001',
                'nomor_bpjs_ketenagakerjaan' => '123456789001',
                'nomor_sim'                  => 'SIM12345001',

                'gaji_pokok'                 => 6000000,
                'tunjangan_jabatan'          => 1500000,
                'tunjangan_makan'            => 800000,
                'tunjangan_transport'        => 500000,
                'nomor_rekening'             => '1234567890',
                'nama_bank'                  => 'BCA',

                'golongan_darah'             => 'O',
                'kontak_darurat_nama'        => 'Siti Nurhaliza',
                'kontak_darurat_nomor'       => '081234556677',
                'kontak_darurat_hubungan'    => 'Istri',

                'dokumen_ktp'                => 'ktp_andi.jpg',
                'dokumen_kk'                 => 'kk_andi.pdf',
                'dokumen_sim'                => 'sim_andi.jpg',
                'dokumen_npwp'               => 'npwp_andi.pdf',
                'dokumen_ijazah'             => 'ijazah_andi.pdf',
                'foto_diri'                  => 'foto_andi.jpg',

                'id_spx'                     => 'SPX-001',
                'rekap_absen'                => 5,
                'rekap_lembur'               => 2,
                'saldo_cuti'                 => 12,

                'catatan_kinerja'            => 'Kinerja sangat baik, disiplin, dan cepat belajar.',
                'training'                   => 'Pelatihan Dasar Keselamatan Kerja',
                'status'                     => 'aktif',

                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
            [
                'nama_lengkap'               => 'Budi Santoso',
                'nama_panggilan'             => 'Budi',
                'jabatan'                    => 'Sopir',
                'nik_ktp'                    => '3517121502890002',
                'tempat_lahir'               => 'Malang',
                'tanggal_lahir'              => '1989-02-15',
                'jenis_kelamin'              => 'Laki-laki',
                'agama'                      => 'Islam',
                'status_pernikahan'          => 'Belum Menikah',
                'alamat_ktp'                 => 'Jl. Kenanga No. 8, Malang',
                'alamat_domisili'            => 'Jl. Jati No. 9, Malang',
                'nomor_hp'                   => '081234567002',
                'email'                      => 'budi@example.com',

                'nomor_npwp'                 => '22.345.678.9-012.345',
                'nomor_bpjs_kesehatan'       => '987654321002',
                'nomor_bpjs_ketenagakerjaan' => '123456789002',
                'nomor_sim'                  => 'SIM12345002',

                'gaji_pokok'                 => 5500000,
                'tunjangan_jabatan'          => 1200000,
                'tunjangan_makan'            => 750000,
                'tunjangan_transport'        => 450000,
                'nomor_rekening'             => '2234567890',
                'nama_bank'                  => 'Mandiri',

                'golongan_darah'             => 'A',
                'kontak_darurat_nama'        => 'Dewi Lestari',
                'kontak_darurat_nomor'       => '081333445566',
                'kontak_darurat_hubungan'    => 'Kakak',

                'dokumen_ktp'                => 'ktp_budi.jpg',
                'dokumen_kk'                 => 'kk_budi.pdf',
                'dokumen_sim'                => 'sim_budi.jpg',
                'dokumen_npwp'               => 'npwp_budi.pdf',
                'dokumen_ijazah'             => 'ijazah_budi.pdf',
                'foto_diri'                  => 'foto_budi.jpg',

                'id_spx'                     => 'SPX-002',
                'rekap_absen'                => 3,
                'rekap_lembur'               => 1,
                'saldo_cuti'                 => 10,

                'catatan_kinerja'            => 'Kinerja cukup baik, perlu peningkatan komunikasi.',
                'training'                   => 'Training Administrasi Dasar',
                'status'                     => 'aktif',

                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
            [
                'nama_lengkap'               => 'Citra Ayu Lestari',
                'nama_panggilan'             => 'Citra',
                'jabatan'                    => 'Marketing',
                'nik_ktp'                    => '3517123003990003',
                'tempat_lahir'               => 'Sidoarjo',
                'tanggal_lahir'              => '1999-03-30',
                'jenis_kelamin'              => 'Perempuan',
                'agama'                      => 'Kristen',
                'status_pernikahan'          => 'Belum Menikah',
                'alamat_ktp'                 => 'Jl. Cemara No. 21, Sidoarjo',
                'alamat_domisili'            => 'Jl. Mangga No. 3, Sidoarjo',
                'nomor_hp'                   => '081234567003',
                'email'                      => 'citra@example.com',

                'nomor_npwp'                 => '32.345.678.9-012.345',
                'nomor_bpjs_kesehatan'       => '987654321003',
                'nomor_bpjs_ketenagakerjaan' => '123456789003',
                'nomor_sim'                  => 'SIM12345003',

                'gaji_pokok'                 => 5000000,
                'tunjangan_jabatan'          => 1000000,
                'tunjangan_makan'            => 700000,
                'tunjangan_transport'        => 400000,
                'nomor_rekening'             => '3234567890',
                'nama_bank'                  => 'BRI',

                'golongan_darah'             => 'B',
                'kontak_darurat_nama'        => 'Ayu Wijaya',
                'kontak_darurat_nomor'       => '081355667788',
                'kontak_darurat_hubungan'    => 'Ibu',

                'dokumen_ktp'                => 'ktp_citra.jpg',
                'dokumen_kk'                 => 'kk_citra.pdf',
                'dokumen_sim'                => 'sim_citra.jpg',
                'dokumen_npwp'               => 'npwp_citra.pdf',
                'dokumen_ijazah'             => 'ijazah_citra.pdf',
                'foto_diri'                  => 'foto_citra.jpg',

                'id_spx'                     => 'SPX-003',
                'rekap_absen'                => 4,
                'rekap_lembur'               => 3,
                'saldo_cuti'                 => 8,

                'catatan_kinerja'            => 'Cekatan, aktif bertanya, cepat beradaptasi.',
                'training'                   => 'Pelatihan Customer Service',
                'status'                     => 'aktif',

                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
            [
                'nama_lengkap'               => 'Dedi Prakoso',
                'nama_panggilan'             => 'Dedi',
                'jabatan'                    => 'Sopir',
                'nik_ktp'                    => '3517120404950004',
                'tempat_lahir'               => 'Gresik',
                'tanggal_lahir'              => '1995-04-04',
                'jenis_kelamin'              => 'Laki-laki',
                'agama'                      => 'Islam',
                'status_pernikahan'          => 'Menikah',
                'alamat_ktp'                 => 'Jl. Kyai Mojo No. 9, Gresik',
                'alamat_domisili'            => 'Jl. Melati No. 11, Gresik',
                'nomor_hp'                   => '081234567004',
                'email'                      => 'dedi@example.com',

                'nomor_npwp'                 => '42.345.678.9-012.345',
                'nomor_bpjs_kesehatan'       => '987654321004',
                'nomor_bpjs_ketenagakerjaan' => '123456789004',
                'nomor_sim'                  => 'SIM12345004',

                'gaji_pokok'                 => 6500000,
                'tunjangan_jabatan'          => 1800000,
                'tunjangan_makan'            => 850000,
                'tunjangan_transport'        => 600000,
                'nomor_rekening'             => '4234567890',
                'nama_bank'                  => 'BNI',

                'golongan_darah'             => 'AB',
                'kontak_darurat_nama'        => 'Rina Maharani',
                'kontak_darurat_nomor'       => '081355992200',
                'kontak_darurat_hubungan'    => 'Istri',

                'dokumen_ktp'                => 'ktp_dedi.jpg',
                'dokumen_kk'                 => 'kk_dedi.pdf',
                'dokumen_sim'                => 'sim_dedi.jpg',
                'dokumen_npwp'               => 'npwp_dedi.pdf',
                'dokumen_ijazah'             => 'ijazah_dedi.pdf',
                'foto_diri'                  => 'foto_dedi.jpg',

                'id_spx'                     => 'SPX-004',
                'rekap_absen'                => 7,
                'rekap_lembur'               => 4,
                'saldo_cuti'                 => 6,

                'catatan_kinerja'            => 'Sangat baik dalam memimpin tim kecil.',
                'training'                   => 'Leadership Fundamental',
                'status'                     => 'aktif',

                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
            [
                'nama_lengkap'               => 'Eka Sari Putri',
                'nama_panggilan'             => 'Eka',
                'jabatan'                    => 'Marketing',
                'nik_ktp'                    => '3517121212970005',
                'tempat_lahir'               => 'Pasuruan',
                'tanggal_lahir'              => '1997-12-12',
                'jenis_kelamin'              => 'Perempuan',
                'agama'                      => 'Islam',
                'status_pernikahan'          => 'Belum Menikah',
                'alamat_ktp'                 => 'Jl. Rajawali No. 5, Pasuruan',
                'alamat_domisili'            => 'Jl. Rajawali No. 5, Pasuruan',
                'nomor_hp'                   => '081234567005',
                'email'                      => 'eka@example.com',

                'nomor_npwp'                 => '52.345.678.9-012.345',
                'nomor_bpjs_kesehatan'       => '987654321005',
                'nomor_bpjs_ketenagakerjaan' => '123456789005',
                'nomor_sim'                  => 'SIM12345005',

                'gaji_pokok'                 => 4800000,
                'tunjangan_jabatan'          => 900000,
                'tunjangan_makan'            => 650000,
                'tunjangan_transport'        => 350000,
                'nomor_rekening'             => '5234567890',
                'nama_bank'                  => 'BTN',

                'golongan_darah'             => 'O',
                'kontak_darurat_nama'        => 'Putri Anggraeni',
                'kontak_darurat_nomor'       => '081355221133',
                'kontak_darurat_hubungan'    => 'Kakak',

                'dokumen_ktp'                => 'ktp_eka.jpg',
                'dokumen_kk'                 => 'kk_eka.pdf',
                'dokumen_sim'                => 'sim_eka.jpg',
                'dokumen_npwp'               => 'npwp_eka.pdf',
                'dokumen_ijazah'             => 'ijazah_eka.pdf',
                'foto_diri'                  => 'foto_eka.jpg',

                'id_spx'                     => 'SPX-005',
                'rekap_absen'                => 2,
                'rekap_lembur'               => 0,
                'saldo_cuti'                 => 14,

                'catatan_kinerja'            => 'Ramah dan cepat beradaptasi dalam lingkungan kerja.',
                'training'                   => 'Customer Handling Skills',
                'status'                     => 'aktif',

                'created_at'                 => now(),
                'updated_at'                 => now(),
            ],
        ];

        foreach ($employees as $idx => &$emp) {
            $emp['employee_number']   = sprintf('EMP-%03d', $idx + 1);
            $emp['name']              = $emp['nama_lengkap'];
            $emp['phone']             = $emp['nomor_hp'];
            $emp['gender']            = $emp['jenis_kelamin'];
            $emp['birth_date']        = $emp['tanggal_lahir'];
            $emp['address']           = $emp['alamat_ktp'];
            $emp['avatar']            = $emp['foto_diri'];
            $emp['is_active']         = $emp['status'] === 'aktif';
            $emp['employment_status'] = 'Tetap';
            $emp['join_date']         = '2023-01-01';
        }
        unset($emp);

        DB::table('karyawan')->insert($employees);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
    }
};
