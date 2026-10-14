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
                'bimbingan.invoices.index'          => 'Rekap Invoice Program - List & Rekap',
                'bimbingan.invoices.show'           => 'Rekap Invoice Program - Detail',
                'bimbingan.pembayaran.index'        => 'Pembayaran Program - List & Verifikasi',
                'bimbingan.pembayaran.update-status' => 'Pembayaran Program - Update Status',
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
                    // Role 1 (Administrator/Superadmin) gets all permissions
                    $existsRole1 = DB::table('role_menu')
                        ->where('id_role', 1)
                        ->where('permission', $name)
                        ->exists();

                    if (!$existsRole1) {
                        DB::table('role_menu')->insert([
                            'id_role'    => 1,
                            'permission' => $name,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Role 99 (Karyawan / Mahasiswa) gets view access to their invoices & payments
                    if (in_array($name, ['bimbingan.invoices.index', 'bimbingan.invoices.show', 'bimbingan.pembayaran.index'])) {
                        $existsRole99 = DB::table('role_menu')
                            ->where('id_role', 99)
                            ->where('permission', $name)
                            ->exists();

                        if (!$existsRole99) {
                            DB::table('role_menu')->insert([
                                'id_role'    => 99,
                                'permission' => $name,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
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
                'bimbingan.invoices.index',
                'bimbingan.invoices.show',
                'bimbingan.pembayaran.index',
                'bimbingan.pembayaran.update-status',
            ];

            DB::table('permissions')->whereIn('name', $perms)->delete();

            if (Schema::hasTable('role_menu')) {
                DB::table('role_menu')->whereIn('permission', $perms)->delete();
            }
        }
    }
};
