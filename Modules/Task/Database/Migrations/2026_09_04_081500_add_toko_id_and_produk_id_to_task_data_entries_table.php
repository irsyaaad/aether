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
        Schema::table('task_data_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('task_data_entries', 'toko_id')) {
                $table->unsignedBigInteger('toko_id')->nullable()->after('employee_id');
                $table->foreign('toko_id')->references('id')->on('tokos')->onDelete('set null');
                $table->index('toko_id');
            }

            if (!Schema::hasColumn('task_data_entries', 'produk_id')) {
                $table->unsignedBigInteger('produk_id')->nullable()->after('toko_id');
                $table->foreign('produk_id')->references('id')->on('produks')->onDelete('set null');
                $table->index('produk_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_data_entries', function (Blueprint $table) {
            if (Schema::hasColumn('task_data_entries', 'toko_id')) {
                $table->dropForeign(['toko_id']);
                $table->dropIndex(['toko_id']);
                $table->dropColumn('toko_id');
            }
            if (Schema::hasColumn('task_data_entries', 'produk_id')) {
                $table->dropForeign(['produk_id']);
                $table->dropIndex(['produk_id']);
                $table->dropColumn('produk_id');
            }
        });
    }
};
