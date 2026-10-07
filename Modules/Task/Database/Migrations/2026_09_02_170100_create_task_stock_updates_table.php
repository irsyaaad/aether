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
        if (!Schema::hasTable('task_stock_updates')) {
            Schema::create('task_stock_updates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('task_id');
                $table->unsignedBigInteger('toko_id');
                $table->unsignedBigInteger('produk_id');
                $table->unsignedBigInteger('employee_id');
                $table->integer('created_by')->nullable();
                $table->string('stock_status')->default('kosong'); // kosong, menipis, tersedia
                $table->integer('current_stock')->default(0);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
                $table->foreign('toko_id')->references('id')->on('tokos')->onDelete('cascade');
                $table->foreign('produk_id')->references('id')->on('produks')->onDelete('cascade');
                $table->foreign('employee_id')->references('id')->on('karyawan')->onDelete('cascade');
                $table->foreign('created_by')->references('id_user')->on('users')->onDelete('set null');

                $table->index('task_id');
                $table->index('toko_id');
                $table->index('produk_id');
                $table->index('employee_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_stock_updates');
    }
};
