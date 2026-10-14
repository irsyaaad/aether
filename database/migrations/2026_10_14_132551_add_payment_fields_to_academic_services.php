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
        Schema::table('transaksi_pelatihans', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_pelatihans', 'nama_pemesan')) {
                $table->string('nama_pemesan', 150)->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('transaksi_pelatihans', 'email_pemesan')) {
                $table->string('email_pemesan', 150)->nullable()->after('nama_pemesan');
            }
            if (!Schema::hasColumn('transaksi_pelatihans', 'telepon_pemesan')) {
                $table->string('telepon_pemesan', 30)->nullable()->after('email_pemesan');
            }
            if (!Schema::hasColumn('transaksi_pelatihans', 'universitas')) {
                $table->string('universitas', 150)->nullable()->after('telepon_pemesan');
            }
            if (!Schema::hasColumn('transaksi_pelatihans', 'biaya')) {
                $table->decimal('biaya', 15, 2)->default(0)->after('status');
            }
            if (!Schema::hasColumn('transaksi_pelatihans', 'admin_fee')) {
                $table->decimal('admin_fee', 15, 2)->default(5000)->after('biaya');
            }
            if (!Schema::hasColumn('transaksi_pelatihans', 'total_bayar')) {
                $table->decimal('total_bayar', 15, 2)->default(0)->after('admin_fee');
            }
            if (!Schema::hasColumn('transaksi_pelatihans', 'metode_pembayaran')) {
                $table->string('metode_pembayaran', 100)->nullable()->after('total_bayar');
            }
            if (!Schema::hasColumn('transaksi_pelatihans', 'kode_pembayaran')) {
                $table->string('kode_pembayaran', 100)->nullable()->after('metode_pembayaran');
            }
            if (!Schema::hasColumn('transaksi_pelatihans', 'bukti_pembayaran')) {
                $table->string('bukti_pembayaran', 255)->nullable()->after('kode_pembayaran');
            }
            if (!Schema::hasColumn('transaksi_pelatihans', 'status_pembayaran')) {
                $table->string('status_pembayaran', 50)->default('Menunggu Pembayaran')->after('bukti_pembayaran');
            }
            if (!Schema::hasColumn('transaksi_pelatihans', 'tanggal_pembelian')) {
                $table->dateTime('tanggal_pembelian')->nullable()->after('status_pembayaran');
            }
            if (!Schema::hasColumn('transaksi_pelatihans', 'catatan')) {
                $table->text('catatan')->nullable()->after('tanggal_pembelian');
            }
        });

        Schema::table('transaksi_olahdatas', function (Blueprint $table) {
            if (!Schema::hasColumn('transaksi_olahdatas', 'admin_fee')) {
                $table->decimal('admin_fee', 15, 2)->default(5000)->after('total_biaya');
            }
            if (!Schema::hasColumn('transaksi_olahdatas', 'total_bayar')) {
                $table->decimal('total_bayar', 15, 2)->default(0)->after('admin_fee');
            }
            if (!Schema::hasColumn('transaksi_olahdatas', 'metode_pembayaran')) {
                $table->string('metode_pembayaran', 100)->nullable()->after('total_bayar');
            }
            if (!Schema::hasColumn('transaksi_olahdatas', 'kode_pembayaran')) {
                $table->string('kode_pembayaran', 100)->nullable()->after('metode_pembayaran');
            }
            if (!Schema::hasColumn('transaksi_olahdatas', 'bukti_pembayaran')) {
                $table->string('bukti_pembayaran', 255)->nullable()->after('kode_pembayaran');
            }
            if (!Schema::hasColumn('transaksi_olahdatas', 'tanggal_pembelian')) {
                $table->dateTime('tanggal_pembelian')->nullable()->after('status_pembayaran');
            }
            if (!Schema::hasColumn('transaksi_olahdatas', 'catatan_pembayaran')) {
                $table->text('catatan_pembayaran')->nullable()->after('tanggal_pembelian');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksi_pelatihans', function (Blueprint $table) {
            $table->dropColumn([
                'nama_pemesan',
                'email_pemesan',
                'telepon_pemesan',
                'universitas',
                'biaya',
                'admin_fee',
                'total_bayar',
                'metode_pembayaran',
                'kode_pembayaran',
                'bukti_pembayaran',
                'status_pembayaran',
                'tanggal_pembelian',
                'catatan',
            ]);
        });

        Schema::table('transaksi_olahdatas', function (Blueprint $table) {
            $table->dropColumn([
                'admin_fee',
                'total_bayar',
                'metode_pembayaran',
                'kode_pembayaran',
                'bukti_pembayaran',
                'tanggal_pembelian',
                'catatan_pembayaran',
            ]);
        });
    }
};
