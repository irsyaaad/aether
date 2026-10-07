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
        Schema::create('setting_perusahaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan');
            $table->text('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('npwp')->nullable();
            $table->string('pemilik')->nullable();
            $table->text('visi_misi')->nullable();
            $table->string('logo')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
        });

        DB::table('setting_perusahaan')->insert([
            'nama_perusahaan' => 'PT Putera Adhi Perkasa',
            'alamat' => 'Jl. Kebon Jeruk No. 12',
            'kota' => 'Jakarta Barat',
            'telepon' => '021-123456',
            'email' => 'info@paphardware.com',
            'website' => 'www.paphardware.com',
            'npwp' => '01.234.567.8-901.000',
            'pemilik' => 'H. Ahmad Persada',
            'visi_misi' => 'Menjadi perusahaan retail dan distribusi perlengkapan hardware terdepan dan terpercaya di Indonesia.',
            'logo' => null,
            'keterangan' => 'Konfigurasi awal informasi umum perusahaan.',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('setting_perusahaan');
    }
};
