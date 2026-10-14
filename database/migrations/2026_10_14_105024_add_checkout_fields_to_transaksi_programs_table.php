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
        Schema::table('transaksi_programs', function (Blueprint $table) {
            $table->string('nama_pemesan')->nullable()->after('nama_program');
            $table->string('email_pemesan')->nullable()->after('nama_pemesan');
            $table->string('telepon_pemesan')->nullable()->after('email_pemesan');
            $table->string('universitas')->nullable()->after('telepon_pemesan');
            $table->string('judul_skripsi')->nullable()->after('universitas');
            $table->string('metode_pembayaran')->nullable()->after('biaya');
            $table->string('kode_pembayaran')->nullable()->after('metode_pembayaran');
            $table->decimal('admin_fee', 10, 2)->default(0)->after('kode_pembayaran');
            $table->decimal('total_bayar', 10, 2)->default(0)->after('admin_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_programs', function (Blueprint $table) {
            $table->dropColumn([
                'nama_pemesan',
                'email_pemesan',
                'telepon_pemesan',
                'universitas',
                'judul_skripsi',
                'metode_pembayaran',
                'kode_pembayaran',
                'admin_fee',
                'total_bayar',
            ]);
        });
    }
};
