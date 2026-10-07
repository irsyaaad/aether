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
        if (Schema::hasTable('tasks') && !Schema::hasColumn('tasks', 'custom_fields')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->json('custom_fields')->nullable()->after('list_columns');
            });
        }

        if (Schema::hasTable('task_submissions') && !Schema::hasColumn('task_submissions', 'custom_data')) {
            Schema::table('task_submissions', function (Blueprint $table) {
                $table->json('custom_data')->nullable()->after('completion_notes');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('tasks') && Schema::hasColumn('tasks', 'custom_fields')) {
            Schema::table('tasks', function (Blueprint $table) {
                $table->dropColumn('custom_fields');
            });
        }

        if (Schema::hasTable('task_submissions') && Schema::hasColumn('task_submissions', 'custom_data')) {
            Schema::table('task_submissions', function (Blueprint $table) {
                $table->dropColumn('custom_data');
            });
        }
    }
};
