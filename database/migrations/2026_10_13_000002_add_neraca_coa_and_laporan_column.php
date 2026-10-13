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
        // 1. Add 'laporan' column if it does not exist
        if (Schema::hasTable('master_coas')) {
            if (!Schema::hasColumn('master_coas', 'laporan')) {
                Schema::table('master_coas', function (Blueprint $table) {
                    $table->string('laporan', 50)->default('Neraca')->after('tipe'); // Neraca, Laba Rugi
                });
            }

            // Update existing Laba Rugi accounts
            DB::table('master_coas')
                ->whereIn('tipe', ['Pendapatan', 'Beban'])
                ->orWhereIn('kategori', ['PENDAPATAN JASA', 'BEBAN OPERASIONAL'])
                ->update(['laporan' => 'Laba Rugi']);

            // 2. Seed Neraca CoA accounts for bimbingan skripsi management
            $neracaCoas = [
                // === ASET LANCAR ===
                [
                    'kode_akun' => '1101',
                    'nama_akun' => 'Kas Operasional (Kas Kecil)',
                    'kategori' => 'ASET LANCAR',
                    'tipe' => 'Aset',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Kas tunai operasional harian kantor bimbingan skripsi',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '1102',
                    'nama_akun' => 'Bank BCA (Rekening Utama)',
                    'kategori' => 'ASET LANCAR',
                    'tipe' => 'Aset',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Rekening utama penampung transfer pembayaran klien & paket bimbingan',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '1103',
                    'nama_akun' => 'Bank Mandiri (Rekening Operasional)',
                    'kategori' => 'ASET LANCAR',
                    'tipe' => 'Aset',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Rekening operasional sekunder untuk belanja dan transaksi digital',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '1104',
                    'nama_akun' => 'Piutang Klien / Mahasiswa',
                    'kategori' => 'ASET LANCAR',
                    'tipe' => 'Aset',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Tagihan termin paket bimbingan skripsi yang belum dilunasi mahasiswa',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '1105',
                    'nama_akun' => 'Uang Muka Biaya & Perlengkapan',
                    'kategori' => 'ASET LANCAR',
                    'tipe' => 'Aset',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Uang muka pengadaan perlengkapan kantor & studio bimbingan',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '1106',
                    'nama_akun' => 'Sewa Kantor Dibayar Dimuka',
                    'kategori' => 'ASET LANCAR',
                    'tipe' => 'Aset',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Biaya sewa ruang kantor / konsultasi bimbingan yang dibayar di muka',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                // === ASET TETAP ===
                [
                    'kode_akun' => '1201',
                    'nama_akun' => 'Peralatan Komputer & Laptop Studio',
                    'kategori' => 'ASET TETAP',
                    'tipe' => 'Aset',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Perangkat komputer olah data (SmartPLS/SPSS/EViews), laptop mentor & monitor',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '1202',
                    'nama_akun' => 'Akumulasi Penyusutan Komputer & Laptop',
                    'kategori' => 'ASET TETAP',
                    'tipe' => 'Aset',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Akumulasi penyusutan nilai perangkat komputer dan laptop studio (Kontra Aset)',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '1203',
                    'nama_akun' => 'Perlengkapan Studio & Ruang Konsultasi',
                    'kategori' => 'ASET TETAP',
                    'tipe' => 'Aset',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Meja konsultasi 1-on-1, kursi ergonomis, pendingin AC, proyektor, whiteboard',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '1204',
                    'nama_akun' => 'Akumulasi Penyusutan Perlengkapan Studio',
                    'kategori' => 'ASET TETAP',
                    'tipe' => 'Aset',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Akumulasi penyusutan perabotan dan sarana fisik studio konsultasi (Kontra Aset)',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                // === KEWAJIBAN LANCAR ===
                [
                    'kode_akun' => '2101',
                    'nama_akun' => 'Utang Usaha / Vendor',
                    'kategori' => 'KEWAJIBAN LANCAR',
                    'tipe' => 'Kewajiban',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Kewajiban kepada vendor percetakan, mitra konsultan, dan penyedia lisensi',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '2102',
                    'nama_akun' => 'Pendapatan Diterima Dimuka (DP Bimbingan)',
                    'kategori' => 'KEWAJIBAN LANCAR',
                    'tipe' => 'Kewajiban',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Uang muka / DP mahasiswa sebelum sesi bimbingan atau olah data selesai (Unearned Revenue)',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '2103',
                    'nama_akun' => 'Utang Honor Mentor & Konsultan',
                    'kategori' => 'KEWAJIBAN LANCAR',
                    'tipe' => 'Kewajiban',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Fee bimbingan mentor yang belum dicairkan pada periode berjalan',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '2104',
                    'nama_akun' => 'Utang Biaya Operasional & Utilitas',
                    'kategori' => 'KEWAJIBAN LANCAR',
                    'tipe' => 'Kewajiban',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Tagihan listrik, internet, dan kebersihan yang belum jatuh tempo pembayaran',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                // === KEWAJIBAN JANGKA PANJANG ===
                [
                    'kode_akun' => '2201',
                    'nama_akun' => 'Utang Bank / Pembiayaan Modal',
                    'kategori' => 'KEWAJIBAN JANGKA PANJANG',
                    'tipe' => 'Kewajiban',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Pinjaman jangka panjang perbankan untuk ekspansi fasilitas kantor bimbingan',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                // === EKUITAS ===
                [
                    'kode_akun' => '3101',
                    'nama_akun' => 'Modal Disetor Pemilik',
                    'kategori' => 'EKUITAS',
                    'tipe' => 'Ekuitas',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Modal awal pendirian dan pengembangan platform bimbingan skripsi',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '3102',
                    'nama_akun' => 'Prive Pemilik (Pengambilan Pribadi)',
                    'kategori' => 'EKUITAS',
                    'tipe' => 'Ekuitas',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Penarikan dana pribadi oleh pemilik usaha (Kontra Ekuitas)',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '3201',
                    'nama_akun' => 'Laba Ditahan (Retained Earnings)',
                    'kategori' => 'EKUITAS',
                    'tipe' => 'Ekuitas',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Akumulasi laba bersih dari tahun-tahun buku sebelumnya yang ditahan',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '3202',
                    'nama_akun' => 'Laba Periode Berjalan',
                    'kategori' => 'EKUITAS',
                    'tipe' => 'Ekuitas',
                    'laporan' => 'Neraca',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Laba bersih berjalan tahun aktif (Selisih Pendapatan - Beban)',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ];

            foreach ($neracaCoas as $item) {
                DB::table('master_coas')->updateOrInsert(
                    ['kode_akun' => $item['kode_akun']],
                    $item
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('master_coas') && Schema::hasColumn('master_coas', 'laporan')) {
            // Delete seeded neraca accounts
            DB::table('master_coas')
                ->whereIn('kode_akun', [
                    '1101', '1102', '1103', '1104', '1105', '1106',
                    '1201', '1202', '1203', '1204',
                    '2101', '2102', '2103', '2104', '2201',
                    '3101', '3102', '3201', '3202'
                ])
                ->delete();

            Schema::table('master_coas', function (Blueprint $table) {
                $table->dropColumn('laporan');
            });
        }
    }
};
