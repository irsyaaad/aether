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
        if (!Schema::hasTable('task_omsets')) {
            Schema::create('task_omsets', function (Blueprint $table) {
                $table->id();
                $table->date('tanggal_input');
                $table->integer('user_id')->nullable();
                $table->text('catatan')->nullable();
                $table->decimal('total_omset', 15, 2)->default(0);
                $table->decimal('total_iklan', 15, 2)->default(0);
                $table->timestamps();
                $table->softDeletes();

                $table->index('tanggal_input');
                $table->index('user_id');
                $table->foreign('user_id')->references('id_user')->on('users')->onDelete('set null');
            });
        }

        if (!Schema::hasTable('task_omset_details')) {
            Schema::create('task_omset_details', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('task_omset_id');
                $table->string('platform', 50); // shopee, tiktok, lazada, tokopedia, blibli, dll
                $table->unsignedBigInteger('toko_id')->nullable();
                $table->string('toko_nama', 255);
                $table->decimal('nominal_omset', 15, 2)->default(0);
                $table->decimal('total_iklan', 15, 2)->default(0);
                $table->timestamps();

                $table->index('task_omset_id');
                $table->index('platform');
                $table->index('toko_id');
                $table->foreign('task_omset_id')->references('id')->on('task_omsets')->onDelete('cascade');
                $table->foreign('toko_id')->references('id')->on('tokos')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_omset_details');
        Schema::dropIfExists('task_omsets');
    }
};
