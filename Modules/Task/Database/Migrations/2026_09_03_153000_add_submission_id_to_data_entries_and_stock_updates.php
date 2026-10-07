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
            $table->unsignedBigInteger('task_submission_id')->nullable()->after('id');
            $table->unsignedBigInteger('task_assignee_id')->nullable()->after('task_submission_id');

            $table->foreign('task_submission_id')->references('id')->on('task_submissions')->onDelete('cascade');
            $table->foreign('task_assignee_id')->references('id')->on('task_assignees')->onDelete('cascade');
            $table->index('task_submission_id');
            $table->index('task_assignee_id');
        });

        Schema::table('task_stock_updates', function (Blueprint $table) {
            $table->unsignedBigInteger('task_submission_id')->nullable()->after('id');
            $table->unsignedBigInteger('task_assignee_id')->nullable()->after('task_submission_id');

            $table->foreign('task_submission_id')->references('id')->on('task_submissions')->onDelete('cascade');
            $table->foreign('task_assignee_id')->references('id')->on('task_assignees')->onDelete('cascade');
            $table->index('task_submission_id');
            $table->index('task_assignee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_data_entries', function (Blueprint $table) {
            $table->dropForeign(['task_submission_id']);
            $table->dropForeign(['task_assignee_id']);
            $table->dropIndex(['task_submission_id']);
            $table->dropIndex(['task_assignee_id']);
            $table->dropColumn(['task_submission_id', 'task_assignee_id']);
        });

        Schema::table('task_stock_updates', function (Blueprint $table) {
            $table->dropForeign(['task_submission_id']);
            $table->dropForeign(['task_assignee_id']);
            $table->dropIndex(['task_submission_id']);
            $table->dropIndex(['task_assignee_id']);
            $table->dropColumn(['task_submission_id', 'task_assignee_id']);
        });
    }
};
