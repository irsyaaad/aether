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
        if (!Schema::hasTable('master_coas')) {
            Schema::create('master_coas', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('kode_akun', 50)->unique();
                $table->string('nama_akun', 150);
                $table->string('kategori', 100); // PENDAPATAN JASA, BEBAN OPERASIONAL, dll
                $table->string('tipe', 50)->default('Pendapatan'); // Pendapatan, Beban, Aset, Kewajiban, Ekuitas
                $table->enum('posisi_normal', ['Debit', 'Kredit'])->default('Debit');
                $table->decimal('saldo_awal', 15, 2)->default(0);
                $table->text('deskripsi')->nullable();
                $table->boolean('is_active')->default(true);
                $table->softDeletes();
                $table->timestamps();
            });

            // Seed Master CoA data for manajemen bimbingan skripsi
            DB::table('master_coas')->insert([
                // PENDAPATAN JASA
                [
                    'kode_akun' => '4101',
                    'nama_akun' => 'Pendapatan Jasa',
                    'kategori' => 'PENDAPATAN JASA',
                    'tipe' => 'Pendapatan',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Pendapatan utama dari layanan bimbingan skripsi & konsultasi akademik',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '4102',
                    'nama_akun' => 'Pembayaran Klien',
                    'kategori' => 'PENDAPATAN JASA',
                    'tipe' => 'Pendapatan',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Penerimaan pembayaran dari klien / mahasiswa bimbingan skripsi',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '4199',
                    'nama_akun' => 'Lainnya',
                    'kategori' => 'PENDAPATAN JASA',
                    'tipe' => 'Pendapatan',
                    'posisi_normal' => 'Kredit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Pendapatan jasa lainnya penunjang akademik non-reguler',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],

                // BEBAN OPERASIONAL
                [
                    'kode_akun' => '5101',
                    'nama_akun' => 'Operasional',
                    'kategori' => 'BEBAN OPERASIONAL',
                    'tipe' => 'Beban',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Beban operasional harian kantor bimbingan skripsi',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '5102',
                    'nama_akun' => 'Transportasi',
                    'kategori' => 'BEBAN OPERASIONAL',
                    'tipe' => 'Beban',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Biaya transportasi, perjalanan dinas, dan bimbingan tatap muka',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '5103',
                    'nama_akun' => 'Aplikasi/Software',
                    'kategori' => 'BEBAN OPERASIONAL',
                    'tipe' => 'Beban',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Biaya lisensi software olah data & riset (SmartPLS, SPSS, AMOS, NVivo, Mendeley)',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '5104',
                    'nama_akun' => 'Perlengkapan',
                    'kategori' => 'BEBAN OPERASIONAL',
                    'tipe' => 'Beban',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Perlengkapan fasilitas konsultasi & sarana studio bimbingan',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '5105',
                    'nama_akun' => 'Administrasi',
                    'kategori' => 'BEBAN OPERASIONAL',
                    'tipe' => 'Beban',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Biaya administrasi kantor, legalitas, materai, dan administrasi perbankan',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '5106',
                    'nama_akun' => 'Komunikasi',
                    'kategori' => 'BEBAN OPERASIONAL',
                    'tipe' => 'Beban',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Biaya internet kantor, pulsa, dan saluran komunikasi bimbingan klien',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '5107',
                    'nama_akun' => 'Jasa',
                    'kategori' => 'BEBAN OPERASIONAL',
                    'tipe' => 'Beban',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Fee jasa pihak ketiga, reviewer ahli, proofreader, dan konsultan eksternal',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '5108',
                    'nama_akun' => 'Service',
                    'kategori' => 'BEBAN OPERASIONAL',
                    'tipe' => 'Beban',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Pemeliharaan dan servis laptop riset, printer, scanner, dan sarana kantor',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '5109',
                    'nama_akun' => 'Subscribe',
                    'kategori' => 'BEBAN OPERASIONAL',
                    'tipe' => 'Beban',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Biaya langganan cloud & platform meeting (Zoom Pro, Google Workspace, Canva, Notion)',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '5110',
                    'nama_akun' => 'Rumah Tangga',
                    'kategori' => 'BEBAN OPERASIONAL',
                    'tipe' => 'Beban',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Kebutuhan pantry kantor, air mineral, konsumsi mentor/klien, dan kebersihan',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '5111',
                    'nama_akun' => 'Lembur',
                    'kategori' => 'BEBAN OPERASIONAL',
                    'tipe' => 'Beban',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Kompensasi lembur tim penanganan deadline sidang mendesak',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'kode_akun' => '5112',
                    'nama_akun' => 'Alat Tulis',
                    'kategori' => 'BEBAN OPERASIONAL',
                    'tipe' => 'Beban',
                    'posisi_normal' => 'Debit',
                    'saldo_awal' => 0,
                    'deskripsi' => 'Alat tulis kantor (ATK), kertas print draft skripsi, tinta, binder, dan perlengkapan',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }

        if (Schema::hasTable('permissions')) {
            $perms = [
                'master.coa.index' => 'Master CoA - Index',
                'master.coa.create' => 'Master CoA - Create',
                'master.coa.store' => 'Master CoA - Store',
                'master.coa.show' => 'Master CoA - Show',
                'master.coa.edit' => 'Master CoA - Edit',
                'master.coa.update' => 'Master CoA - Update',
                'master.coa.destroy' => 'Master CoA - Destroy',
                'master.coa.get-data' => 'Master CoA - DataTables',
            ];
            foreach ($perms as $p => $label) {
                DB::table('permissions')->updateOrInsert(
                    ['name' => $p],
                    ['label' => $label, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_coas');
    }
};
