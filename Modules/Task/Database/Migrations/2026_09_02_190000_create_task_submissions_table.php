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
        Schema::create('task_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id');
            $table->unsignedBigInteger('employee_id');
            $table->integer('created_by')->nullable();
            $table->date('work_date')->nullable();
            $table->string('status', 50)->default('in_progress');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->string('completion_url', 2000)->nullable();
            $table->string('completion_file')->nullable();
            $table->text('completion_notes')->nullable();
            $table->decimal('achieved_amount', 15, 2)->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('karyawan')->onDelete('cascade');
            $table->foreign('created_by')->references('id_user')->on('users')->onDelete('set null');

            $table->index(['task_id', 'employee_id']);
            $table->index('work_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_submissions');
    }
};
