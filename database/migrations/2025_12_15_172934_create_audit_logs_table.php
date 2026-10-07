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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            // siapa yang melakukan
            $table->unsignedBigInteger('user_id')->nullable();

            // aksi yang dilakukan
            $table->string('action', 20); // create, update, delete

            // target data
            $table->string('table_name', 100);
            $table->unsignedBigInteger('record_id')->nullable();

            // data perubahan
            $table->json('old_data')->nullable();
            $table->json('new_data')->nullable();

            // metadata
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamps();

            // index untuk performa
            $table->index(['table_name', 'record_id']);
            $table->index('user_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
