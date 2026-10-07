<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // $permissions = [
        //     // PURCHASE
        //     'pembelian.index',
        //     'pembelian.create',
        //     'pembelian.store',
        //     'pembelian.show',
        //     'pembelian.edit',
        //     'pembelian.update',
        //     'pembelian.delete',
        //     'pembelian.approve',
        //     'pembelian.bayar',
        //     'pembelian.print',
        // ];

        // foreach ($permissions as $permission) {
        //     Permission::firstOrCreate([
        //         'name' => $permission,
        //         'guard_name' => 'web',
        //     ]);
        // }

        // // ROLE
        // $administrator = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        // $direktur = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        // $keuangan = Role::firstOrCreate(['name' => 'keuangan', 'guard_name' => 'web']);
        // $sopir = Role::firstOrCreate(['name' => 'sopir', 'guard_name' => 'web']);

        // // ADMIN AKSES SEMUA
        // $administrator->syncPermissions(
        //     Permission::whereIn('name', $permissions)->get()
        // );

        // // STAFF AKSES TERBATAS
        // $direktur->syncPermissions([
        //     'pembelian.index',
        //     'pembelian.show',
        //     'pembelian.print',
        // ]);

        // $keuangan->syncPermissions([
        //     'pembelian.index',
        //     'pembelian.show',
        //     'pembelian.print',
        // ]);

        // $sopir->syncPermissions([
        //     'pembelian.index',
        //     'pembelian.show',
        // ]);

        // $user = User::firstOrCreate([
        //     'nm_user' => 'Admin',
        //     'username' => 'username',
        //     'email' => 'admin@example.com',
        //     'password' => bcrypt('password'),
        // ]);
        // $user->assignRole('admin');
        // DB::table('role_user')->insert(array(
        //     array(
        //         'id_user' => $user->id_user,
        //         'id_role' => 1,
        //     ),
        // ));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
