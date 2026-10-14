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
        if (!Schema::hasTable('transaksi_pelatihans')) {
            Schema::create('transaksi_pelatihans', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nomor_registrasi', 50)->unique();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('kelas_id')->nullable();
                $table->string('judul_kelas', 255);
                $table->string('instruktur', 150)->nullable();
                $table->string('kode_kelas', 50)->nullable();
                $table->string('link_zoom', 255)->nullable();
                $table->integer('progress')->default(0);
                $table->integer('total_modul')->default(8);
                $table->integer('modul_selesai')->default(0);
                $table->string('status', 50)->default('Aktif Berjalan'); // Aktif Berjalan, Jadwal Mendatang, Selesai
                $table->softDeletes();
                $table->timestamps();

                $table->index('user_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi_pelatihans');
    }
};
