<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('permissions')) {
            $permissions = [
                'jurnal.index' => 'Jurnal Umum - List & Index',
                'jurnal.cetak' => 'Jurnal Umum - Cetak Laporan',
                'jurnal.export' => 'Jurnal Umum - Export Excel',
            ];

            foreach ($permissions as $name => $label) {
                DB::table('permissions')->updateOrInsert(
                    ['name' => $name],
                    [
                        'label' => $label,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );

                if (Schema::hasTable('role_menu')) {
                    $exists = DB::table('role_menu')
                        ->where('id_role', 1)
                        ->where('permission', $name)
                        ->exists();

                    if (!$exists) {
                        DB::table('role_menu')->insert([
                            'id_role' => 1,
                            'permission' => $name,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('permissions')) {
            $perms = ['jurnal.index', 'jurnal.cetak', 'jurnal.export'];
            DB::table('permissions')->whereIn('name', $perms)->delete();

            if (Schema::hasTable('role_menu')) {
                DB::table('role_menu')->whereIn('permission', $perms)->delete();
            }
        }
    }
};
