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
                // Neraca
                'neraca.index'  => 'Neraca Keuangan - List & Index',
                'neraca.cetak'  => 'Neraca Keuangan - Cetak Laporan',
                'neraca.export' => 'Neraca Keuangan - Export Excel',

                // Laba Rugi
                'rugi-laba.index'  => 'Laba Rugi - List & Index',
                'rugi-laba.cetak'  => 'Laba Rugi - Cetak Laporan',
                'rugi-laba.export' => 'Laba Rugi - Export Excel',

                // Cash Flow (Arus Kas)
                'cashflow.index'  => 'Arus Kas (Cash Flow) - List & Index',
                'cashflow.cetak'  => 'Arus Kas (Cash Flow) - Cetak Laporan',
                'cashflow.export' => 'Arus Kas (Cash Flow) - Export Excel',
            ];

            foreach ($permissions as $name => $label) {
                DB::table('permissions')->updateOrInsert(
                    ['name' => $name],
                    [
                        'label'      => $label,
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
                            'id_role'    => 1,
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
            $perms = [
                'neraca.index', 'neraca.cetak', 'neraca.export',
                'rugi-laba.index', 'rugi-laba.cetak', 'rugi-laba.export',
                'cashflow.index', 'cashflow.cetak', 'cashflow.export',
            ];
            DB::table('permissions')->whereIn('name', $perms)->delete();

            if (Schema::hasTable('role_menu')) {
                DB::table('role_menu')->whereIn('permission', $perms)->delete();
            }
        }
    }
};
