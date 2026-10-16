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
        if (Schema::hasTable('master_kelas') && !Schema::hasColumn('master_kelas', 'thumbnail')) {
            Schema::table('master_kelas', function (Blueprint $table) {
                $table->string('thumbnail', 255)->nullable()->after('judul');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('master_kelas') && Schema::hasColumn('master_kelas', 'thumbnail')) {
            Schema::table('master_kelas', function (Blueprint $table) {
                $table->dropColumn('thumbnail');
            });
        }
    }
};
