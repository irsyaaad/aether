<?php

use Illuminate\Database\Seeder;
use Modules\Master\Database\Seeders\MasterDatabaseSeeder;
use Modules\Task\Database\Seeders\TaskDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Modul Master (Role, Company, Departemen, Position, Karyawan, User, Toko, Produk)
        $this->call(MasterDatabaseSeeder::class);

        // 2. Modul Task (Priorities, Categories, Semua Template Tugas di-assign ke 2 Karyawan)
        $this->call(TaskDatabaseSeeder::class);
    }
}
