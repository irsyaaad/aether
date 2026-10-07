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
        if (Schema::hasTable('task_omsets')) {
            Schema::table('task_omsets', function (Blueprint $table) {
                if (!Schema::hasColumn('task_omsets', 'total_omset_bersih')) {
                    $table->decimal('total_omset_bersih', 15, 2)->default(0)->after('total_omset');
                }
            });
        }

        if (Schema::hasTable('task_omset_details')) {
            Schema::table('task_omset_details', function (Blueprint $table) {
                if (!Schema::hasColumn('task_omset_details', 'fee_percent')) {
                    $table->decimal('fee_percent', 5, 2)->default(0)->after('nominal_omset');
                }
                if (!Schema::hasColumn('task_omset_details', 'omset_bersih')) {
                    $table->decimal('omset_bersih', 15, 2)->default(0)->after('fee_percent');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('task_omsets')) {
            Schema::table('task_omsets', function (Blueprint $table) {
                if (Schema::hasColumn('task_omsets', 'total_omset_bersih')) {
                    $table->dropColumn('total_omset_bersih');
                }
            });
        }

        if (Schema::hasTable('task_omset_details')) {
            Schema::table('task_omset_details', function (Blueprint $table) {
                $columnsToDrop = [];
                if (Schema::hasColumn('task_omset_details', 'fee_percent')) {
                    $columnsToDrop[] = 'fee_percent';
                }
                if (Schema::hasColumn('task_omset_details', 'omset_bersih')) {
                    $columnsToDrop[] = 'omset_bersih';
                }
                if (!empty($columnsToDrop)) {
                    $table->dropColumn($columnsToDrop);
                }
            });
        }
    }
};
