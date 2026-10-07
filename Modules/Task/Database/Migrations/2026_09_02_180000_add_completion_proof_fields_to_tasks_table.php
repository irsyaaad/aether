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
            $table->string('completion_url', 2000)->nullable()->after('completed_at');
            $table->string('completion_file')->nullable()->after('completion_url');
            $table->text('completion_notes')->nullable()->after('completion_file');
            $table->unsignedBigInteger('completed_by')->nullable()->after('completion_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn(['completion_url', 'completion_file', 'completion_notes', 'completed_by']);
        });
    }
};
