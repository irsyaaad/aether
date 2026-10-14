<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('transaksi_programs')) {
            Schema::create('transaksi_programs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nomor_transaksi', 50)->unique();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('program_id');
                $table->string('nama_program', 150);
                $table->decimal('biaya', 15, 2)->default(0);
                $table->integer('total_sesi')->default(1);
                $table->integer('sisa_sesi')->default(1);
                $table->integer('sesi_digunakan')->default(0);
                $table->string('status', 50)->default('Aktif'); // Aktif, Selesai, Kedaluwarsa
                $table->string('status_pembayaran', 50)->default('Lunas'); // Lunas, Menunggu Pembayaran
                $table->dateTime('tanggal_pembelian')->nullable();
                $table->dateTime('tanggal_berakhir')->nullable();
                $table->text('catatan')->nullable();
                $table->softDeletes();
                $table->timestamps();

                $table->index('user_id');
                $table->index('program_id');
                $table->index('status');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_programs');
    }
};
