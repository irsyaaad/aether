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
        if (Schema::hasTable('tokos') && !Schema::hasColumn('tokos', 'category')) {
            Schema::table('tokos', function (Blueprint $table) {
                $table->string('category', 50)->nullable()->after('code');
                $table->index('category');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tokos') && Schema::hasColumn('tokos', 'category')) {
            Schema::table('tokos', function (Blueprint $table) {
                $table->dropIndex(['category']);
                $table->dropColumn('category');
            });
        }
    }
};
