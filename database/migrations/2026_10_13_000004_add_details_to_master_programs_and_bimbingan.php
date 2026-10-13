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
        if (Schema::hasTable('master_programs')) {
            Schema::table('master_programs', function (Blueprint $table) {
                if (!Schema::hasColumn('master_programs', 'jumlah_sesi')) {
                    $table->integer('jumlah_sesi')->default(1)->after('durasi_sesi');
                }
                if (!Schema::hasColumn('master_programs', 'fasilitas')) {
                    $table->text('fasilitas')->nullable()->after('jumlah_sesi');
                }
                if (!Schema::hasColumn('master_programs', 'badge')) {
                    $table->string('badge', 50)->nullable()->after('fasilitas');
                }
                if (!Schema::hasColumn('master_programs', 'target_bab')) {
                    $table->string('target_bab', 255)->nullable()->after('badge');
                }
                if (!Schema::hasColumn('master_programs', 'is_populer')) {
                    $table->boolean('is_populer')->default(false)->after('target_bab');
                }
            });
        }

        if (Schema::hasTable('transaksi_bimbingans')) {
            Schema::table('transaksi_bimbingans', function (Blueprint $table) {
                if (!Schema::hasColumn('transaksi_bimbingans', 'program_id')) {
                    $table->unsignedBigInteger('program_id')->nullable()->after('paket_bimbingan');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('master_programs')) {
            Schema::table('master_programs', function (Blueprint $table) {
                $columns = [];
                if (Schema::hasColumn('master_programs', 'is_populer')) $columns[] = 'is_populer';
                if (Schema::hasColumn('master_programs', 'target_bab')) $columns[] = 'target_bab';
                if (Schema::hasColumn('master_programs', 'badge')) $columns[] = 'badge';
                if (Schema::hasColumn('master_programs', 'fasilitas')) $columns[] = 'fasilitas';
                if (Schema::hasColumn('master_programs', 'jumlah_sesi')) $columns[] = 'jumlah_sesi';
                if (!empty($columns)) {
                    $table->dropColumn($columns);
                }
            });
        }

        if (Schema::hasTable('transaksi_bimbingans') && Schema::hasColumn('transaksi_bimbingans', 'program_id')) {
            Schema::table('transaksi_bimbingans', function (Blueprint $table) {
                $table->dropColumn('program_id');
            });
        }
    }
};
