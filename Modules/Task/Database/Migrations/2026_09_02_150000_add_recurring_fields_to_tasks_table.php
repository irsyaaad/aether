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
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('task_type')->default('insidentil')->after('priority_id');
            $table->boolean('is_template')->default(false)->after('task_type');
            $table->unsignedBigInteger('parent_task_id')->nullable()->after('is_template');
            $table->date('recurring_date')->nullable()->after('parent_task_id');

            $table->foreign('parent_task_id')->references('id')->on('tasks')->onDelete('cascade');

            $table->index('task_type');
            $table->index('is_template');
            $table->index('parent_task_id');
            $table->index('recurring_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['parent_task_id']);
            $table->dropColumn(['task_type', 'is_template', 'parent_task_id', 'recurring_date']);
        });
    }
};
