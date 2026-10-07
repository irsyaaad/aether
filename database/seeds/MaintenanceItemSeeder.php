<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class MaintenanceItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            // Daily Checks
            ['name' => 'Oli Mesin', 'category' => 'daily', 'order' => 1],
            ['name' => 'Air Radiator', 'category' => 'daily', 'order' => 2],
            ['name' => 'Ban', 'category' => 'daily', 'order' => 3],
            ['name' => 'Lampu', 'category' => 'daily', 'order' => 4],
            ['name' => 'Rem', 'category' => 'daily', 'order' => 5],
            ['name' => 'Kebocoran', 'category' => 'daily', 'order' => 6],
            ['name' => 'Kelengkapan (dongkrak, segitiga, dll)', 'category' => 'daily', 'order' => 7],

            // Preventive Maintenance (Combined example)
            ['name' => 'Greasing', 'category' => 'preventive', 'order' => 8],
            ['name' => 'Cek Baut Roda', 'category' => 'preventive', 'order' => 9],
            ['name' => 'Cek Suspensi', 'category' => 'preventive', 'order' => 10],
            ['name' => 'Filter Udara', 'category' => 'preventive', 'order' => 11],
            ['name' => 'Sistem Bahan Bakar', 'category' => 'preventive', 'order' => 12],
            ['name' => 'Sistem Pendingin', 'category' => 'preventive', 'order' => 13],
            ['name' => 'Cek Transmisi', 'category' => 'preventive', 'order' => 14],
        ];

        foreach ($items as $item) {
            \Modules\Operasional\Entities\MaintenanceItem::create($item);
        }
    }
}
