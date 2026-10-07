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
            $table->string('output_type')->default('checklist')->after('task_type');
            $table->string('target_type')->nullable()->after('output_type');
            $table->decimal('target_amount', 15, 2)->nullable()->after('target_type');
            $table->string('target_unit')->nullable()->after('target_amount');
            $table->integer('target_count')->nullable()->after('target_unit');
            $table->json('list_columns')->nullable()->after('target_count');
            $table->decimal('achieved_amount', 15, 2)->default(0)->after('list_columns');

            $table->index('output_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['output_type']);
            $table->dropColumn([
                'output_type',
                'target_type',
                'target_amount',
                'target_unit',
                'target_count',
                'list_columns',
                'achieved_amount',
            ]);
        });
    }
};
