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
        if (Schema::hasTable('master_programs') && !Schema::hasColumn('master_programs', 'banner')) {
            Schema::table('master_programs', function (Blueprint $table) {
                $table->string('banner', 255)->nullable()->after('nama_program');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('master_programs') && Schema::hasColumn('master_programs', 'banner')) {
            Schema::table('master_programs', function (Blueprint $table) {
                $table->dropColumn('banner');
            });
        }
    }
};
