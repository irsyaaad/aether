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
        Schema::table('task_submissions', function (Blueprint $table) {
            $table->unsignedBigInteger('task_assignee_id')->nullable()->after('id');

            $table->foreign('task_assignee_id')->references('id')->on('task_assignees')->onDelete('cascade');
            $table->index('task_assignee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_submissions', function (Blueprint $table) {
            $table->dropForeign(['task_assignee_id']);
            $table->dropIndex(['task_assignee_id']);
            $table->dropColumn('task_assignee_id');
        });
    }
};
