<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'id_role' => 1,
                'nm_role' => 'Administrator',
            ],
            [
                'id_role' => 6,
                'nm_role' => 'Admin',
            ],
            [
                'id_role' => 7,
                'nm_role' => 'Manager',
            ],
            [
                'id_role' => 8,
                'nm_role' => 'Employee',
            ],
        ];

        // 1. Custom 'role' table
        if (Schema::hasTable('role')) {
            foreach ($roles as $roleData) {
                $existing = DB::table('role')
                    ->where('id_role', $roleData['id_role'])
                    ->orWhere(DB::raw('LOWER(nm_role)'), strtolower($roleData['nm_role']))
                    ->first();

                if ($existing) {
                    DB::table('role')
                        ->where('id_role', $existing->id_role)
                        ->update([
                            'nm_role'    => $roleData['nm_role'],
                            'app_id'     => $existing->app_id ?: 'saas',
                            'updated_at' => now(),
                        ]);
                } else {
                    DB::table('role')->insert([
                        'id_role'    => $roleData['id_role'],
                        'nm_role'    => $roleData['nm_role'],
                        'app_id'     => 'saas',
                        'id_creator' => 1,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 2. Spatie 'roles' table if present
        if (Schema::hasTable('roles')) {
            foreach ($roles as $roleData) {
                DB::table('roles')->updateOrInsert(
                    ['name' => $roleData['nm_role'], 'guard_name' => 'web'],
                    ['updated_at' => now()]
                );
            }
        }
    }
}
