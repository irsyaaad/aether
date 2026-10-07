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
            $table->date('start_date')->nullable()->after('assigned_by');
            $table->date('due_date')->nullable()->after('start_date');
            $table->string('status', 50)->default('assigned')->after('due_date');
            $table->unsignedTinyInteger('progress')->default(0)->after('status');
            $table->timestamp('completed_at')->nullable()->after('progress');
            $table->text('notes')->nullable()->after('completed_at');

            $table->index('due_date');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('task_assignees', function (Blueprint $table) {
            $table->dropIndex(['due_date']);
            $table->dropIndex(['status']);
            $table->dropColumn(['start_date', 'due_date', 'status', 'progress', 'completed_at', 'notes']);
        });
    }
};
