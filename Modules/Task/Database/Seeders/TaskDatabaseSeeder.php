<?php

namespace Modules\Task\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Master\Database\Seeders\TaskCategorySeeder;
use Modules\Master\Database\Seeders\TaskPrioritySeeder;
use Modules\Master\Entities\Company;
use Modules\Master\Entities\Produk;
use Modules\Master\Entities\Toko;

class TaskDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan Company default sudah ada agar Prioritas & Kategori bisa terhubung
        Company::firstOrCreate(
            ['code' => 'PAP'],
            [
                'name'      => 'PT Putera Adhi Perkasa',
                'email'     => 'admin@paphardware.com',
                'timezone'  => 'Asia/Jakarta',
                'is_active' => true,
            ]
        );

        // 2. Pastikan Master Toko & Produk sudah ada jika tabel masih kosong
        if (Toko::count() === 0 && class_exists(\Modules\Master\Database\Seeders\TokoSeeder::class)) {
            $this->call(\Modules\Master\Database\Seeders\TokoSeeder::class);
        }
        if (Produk::count() === 0 && class_exists(\Modules\Master\Database\Seeders\ProdukSeeder::class)) {
            $this->call(\Modules\Master\Database\Seeders\ProdukSeeder::class);
        }

        // 3. Seed Master Prioritas & Kategori Tugas
        $this->call([
            TaskPrioritySeeder::class,
            TaskCategorySeeder::class,
        ]);

        // 4. Seed Master Task Templates & Sinkronisasi Recurring Instances
        $this->call(TaskSeeder::class);
    }
}
