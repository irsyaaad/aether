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
        Schema::table('task_assignees', function (Blueprint $table) {
            // Drop unique constraint to allow master tasks to be plotted repeatedly across dates
            $table->dropUnique(['task_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_assignees', function (Blueprint $table) {
            $table->unique(['task_id', 'employee_id']);
        });
    }
};
