<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Modules\Master\Entities\Karyawan;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch employees
        $empMeryana = Karyawan::where('employee_number', 'EMP-001')->orWhere('email', 'meryana@paphardware.com')->first();
        $empBudi    = Karyawan::where('employee_number', 'EMP-002')->orWhere('email', 'budi.santoso@paphardware.com')->first();
        $empCitra   = Karyawan::where('employee_number', 'EMP-003')->orWhere('email', 'citra.ayu@paphardware.com')->first();
        $empDedi    = Karyawan::where('employee_number', 'EMP-004')->orWhere('email', 'dedi.prakoso@paphardware.com')->first();
        $empEka     = Karyawan::where('employee_number', 'EMP-005')->orWhere('email', 'eka.sari@paphardware.com')->first();

        // Map roles by name
        $roleAdmin = DB::table('role')->where(DB::raw('LOWER(nm_role)'), 'administrator')->value('id_role') ?: 1;
        $roleStaffAdmin = DB::table('role')->where(DB::raw('LOWER(nm_role)'), 'admin')->value('id_role') ?: 6;
        $roleManager = DB::table('role')->where(DB::raw('LOWER(nm_role)'), 'manager')->value('id_role') ?: 7;
        $roleEmployee = DB::table('role')->where(DB::raw('LOWER(nm_role)'), 'employee')->value('id_role') 
            ?: (DB::table('role')->where(DB::raw('LOWER(nm_role)'), 'karyawan')->value('id_role') ?: 8);

        $userData = [
            [
                'email'       => 'admin@paphardware.com',
                'username'    => 'admin',
                'nm_user'     => 'Meryana (Administrator)',
                'password'    => Hash::make('password'),
                'telp'        => '081234567001',
                'karyawan_id' => $empMeryana?->id,
                'role_id'     => $roleAdmin,
            ],
            [
                'email'       => 'budi@paphardware.com',
                'username'    => 'budi',
                'nm_user'     => 'Budi Santoso',
                'password'    => Hash::make('password'),
                'telp'        => '081234567002',
                'karyawan_id' => $empBudi?->id,
                'role_id'     => $roleEmployee,
            ],
            [
                'email'       => 'citra@paphardware.com',
                'username'    => 'citra',
                'nm_user'     => 'Citra Ayu Lestari',
                'password'    => Hash::make('password'),
                'telp'        => '081234567003',
                'karyawan_id' => $empCitra?->id,
                'role_id'     => $roleStaffAdmin,
            ],
            [
                'email'       => 'dedi@paphardware.com',
                'username'    => 'dedi',
                'nm_user'     => 'Dedi Prakoso',
                'password'    => Hash::make('password'),
                'telp'        => '081234567004',
                'karyawan_id' => $empDedi?->id,
                'role_id'     => $roleManager,
            ],
            [
                'email'       => 'eka@paphardware.com',
                'username'    => 'eka',
                'nm_user'     => 'Eka Sari Putri',
                'password'    => Hash::make('password'),
                'telp'        => '081234567005',
                'karyawan_id' => $empEka?->id,
                'role_id'     => $roleEmployee,
            ],
        ];

        foreach ($userData as $u) {
            $user = DB::table('users')->where('email', $u['email'])->orWhere('username', $u['username'])->first();

            $insertOrUpdate = [
                'nm_user'     => $u['nm_user'],
                'username'    => $u['username'],
                'email'       => $u['email'],
                'password'    => $u['password'],
                'telp'        => $u['telp'],
                'karyawan_id' => $u['karyawan_id'],
                'is_aktif'    => true,
                'updated_at'  => now(),
            ];

            if ($user) {
                DB::table('users')->where('id_user', $user->id_user)->update($insertOrUpdate);
                $userId = $user->id_user;
            } else {
                $insertOrUpdate['created_at'] = now();
                $userId = DB::table('users')->insertGetId($insertOrUpdate);
            }

            // Relasikan ke role_user
            if (Schema::hasTable('role_user') && $userId) {
                $hasRole = DB::table('role_user')
                    ->where('id_user', $userId)
                    ->where('id_role', $u['role_id'])
                    ->first();

                if (!$hasRole) {
                    $existingRoleUser = DB::table('role_user')->where('id_user', $userId)->first();
                    if ($existingRoleUser) {
                        DB::table('role_user')->where('id_user', $userId)->update([
                            'id_role'    => $u['role_id'],
                            'updated_at' => now(),
                        ]);
                    } else {
                        DB::table('role_user')->insert([
                            'id_user'    => $userId,
                            'id_role'    => $u['role_id'],
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }
    }
}
