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
        if (!Schema::hasTable('transaksi_kas_banks')) {
            Schema::create('transaksi_kas_banks', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nomor_transaksi', 50)->unique();
                $table->enum('jenis', ['masuk', 'keluar'])->index(); // masuk = Kas/Bank Masuk, keluar = Kas/Bank Keluar
                $table->date('tanggal')->index();
                
                // Akun Kas / Bank (Ambil dari master_coas yang kas bank)
                $table->unsignedBigInteger('kas_bank_id')->index();
                $table->foreign('kas_bank_id')
                    ->references('id')
                    ->on('master_coas')
                    ->onDelete('restrict');

                // Akun Lawan (Ambil dari master_coas yang beban / pendapatan)
                $table->unsignedBigInteger('lawan_coa_id')->index();
                $table->foreign('lawan_coa_id')
                    ->references('id')
                    ->on('master_coas')
                    ->onDelete('restrict');

                $table->decimal('nominal', 15, 2)->default(0);
                $table->string('penerima_pembayar', 150)->nullable(); // Diterima dari (masuk) / Dibayarkan kepada (keluar)
                $table->string('nomor_referensi', 100)->nullable(); // No kuitansi / invoice / bukti transfer
                $table->string('metode_pembayaran', 50)->default('Transfer'); // Tunai, Transfer Bank, Giro, dll
                $table->text('keterangan')->nullable();
                $table->string('lampiran', 255)->nullable(); // File bukti transaksi
                $table->string('status', 30)->default('completed'); // completed, draft, void

                $table->integer('user_id')->nullable();
                $table->foreign('user_id')
                    ->references('id_user')
                    ->on('users')
                    ->onDelete('set null');

                $table->softDeletes();
                $table->timestamps();
            });
        }

        // Seed permissions for Kas/Bank Masuk & Keluar
        if (Schema::hasTable('permissions')) {
            $permissions = [
                'kas-bank-masuk.index'   => 'Kas/Bank Masuk - List & Index',
                'kas-bank-masuk.create'  => 'Kas/Bank Masuk - Create',
                'kas-bank-masuk.store'   => 'Kas/Bank Masuk - Store',
                'kas-bank-masuk.show'    => 'Kas/Bank Masuk - Detail',
                'kas-bank-masuk.edit'    => 'Kas/Bank Masuk - Edit',
                'kas-bank-masuk.update'  => 'Kas/Bank Masuk - Update',
                'kas-bank-masuk.destroy' => 'Kas/Bank Masuk - Delete',
                'kas-bank-keluar.index'   => 'Kas/Bank Keluar - List & Index',
                'kas-bank-keluar.create'  => 'Kas/Bank Keluar - Create',
                'kas-bank-keluar.store'   => 'Kas/Bank Keluar - Store',
                'kas-bank-keluar.show'    => 'Kas/Bank Keluar - Detail',
                'kas-bank-keluar.edit'    => 'Kas/Bank Keluar - Edit',
                'kas-bank-keluar.update'  => 'Kas/Bank Keluar - Update',
                'kas-bank-keluar.destroy' => 'Kas/Bank Keluar - Delete',
            ];

            foreach ($permissions as $name => $label) {
                DB::table('permissions')->updateOrInsert(
                    ['name' => $name],
                    [
                        'label'      => $label,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                // Assign to superadmin role (id_role = 1) if role_menu table exists
                if (Schema::hasTable('role_menu')) {
                    $exists = DB::table('role_menu')
                        ->where('id_role', 1)
                        ->where('permission', $name)
                        ->exists();

                    if (!$exists) {
                        DB::table('role_menu')->insert([
                            'id_role'    => 1,
                            'permission' => $name,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_kas_banks');
    }
};
