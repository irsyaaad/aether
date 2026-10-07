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
            if (Schema::hasColumn('tasks', 'completion_url')) {
                $table->dropColumn(['completion_url', 'completion_file', 'completion_notes', 'completed_by']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->string('completion_url', 2000)->nullable();
            $table->string('completion_file')->nullable();
            $table->text('completion_notes')->nullable();
            $table->unsignedBigInteger('completed_by')->nullable();
        });
    }
};
