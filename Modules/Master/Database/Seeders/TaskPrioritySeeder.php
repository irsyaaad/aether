<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Master\Entities\Company;
use Modules\Task\Entities\TaskPriority;

class TaskPrioritySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        $defaultPriorities = [
            [
                'name'        => 'Low',
                'code'        => 'LOW',
                'level'       => 1,
                'description' => 'Prioritas rendah, dapat diselesaikan dengan santai',
            ],
            [
                'name'        => 'Medium',
                'code'        => 'MEDIUM',
                'level'       => 2,
                'description' => 'Prioritas normal',
            ],
            [
                'name'        => 'High',
                'code'        => 'HIGH',
                'level'       => 3,
                'description' => 'Prioritas tinggi, membutuhkan penanganan segera',
            ],
            [
                'name'        => 'Urgent',
                'code'        => 'URGENT',
                'level'       => 4,
                'description' => 'Sangat mendesak, harus segera diselesaikan',
            ],
        ];

        foreach ($companies as $company) {
            foreach ($defaultPriorities as $p) {
                TaskPriority::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'code'       => $p['code'],
                    ],
                    [
                        'name'        => $p['name'],
                        'level'       => $p['level'],
                        'description' => $p['description'],
                        'is_active'   => true,
                    ]
                );
            }
        }
    }
}
