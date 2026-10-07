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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->string('task_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('priority_id');
            $table->string('status')->default('draft');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->integer('created_by');
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('task_categories')->onDelete('set null');
            $table->foreign('priority_id')->references('id')->on('task_priorities')->onDelete('restrict');
            $table->foreign('created_by')->references('id_user')->on('users')->onDelete('restrict');

            $table->unique(['company_id', 'task_number']);
            $table->index('company_id');
            $table->index('status');
            $table->index('due_date');
            $table->index('priority_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
