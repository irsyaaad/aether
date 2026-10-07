<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;

class MasterDatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            CompanySeeder::class,
            DepartemenSeeder::class,
            PositionSeeder::class,
            KaryawanSeeder::class,
            UserSeeder::class,
            TokoSeeder::class,
            ProdukSeeder::class,
            PlatformSeeder::class,
            TaskPrioritySeeder::class,
            TaskCategorySeeder::class,
        ]);
    }
}
