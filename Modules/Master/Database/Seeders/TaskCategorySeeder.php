<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Master\Entities\Company;
use Modules\Task\Entities\TaskCategory;

class TaskCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = Company::all();

        $defaultCategories = [
            [
                'name'        => 'General',
                'code'        => 'GEN',
                'description' => 'Tugas umum operasional',
            ],
            [
                'name'        => 'Operasional',
                'code'        => 'OPS',
                'description' => 'Tugas harian terkait operasional toko dan kantor',
            ],
            [
                'name'        => 'Maintenance',
                'code'        => 'MAINT',
                'description' => 'Tugas pemeliharaan dan perbaikan fasilitas kantor & toko',
            ],
            [
                'name'        => 'Administrasi',
                'code'        => 'ADM',
                'description' => 'Tugas administrasi, rekap data, dan pengarsipan',
            ],
            [
                'name'        => 'Marketing & Sosmed',
                'code'        => 'MKT',
                'description' => 'Tugas pemasaran, promosi, dan konten media sosial',
            ],
            [
                'name'        => 'Customer Service',
                'code'        => 'CS',
                'description' => 'Tugas penanganan chat, komplain, dan layanan pelanggan',
            ],
        ];

        foreach ($companies as $company) {
            foreach ($defaultCategories as $c) {
                TaskCategory::firstOrCreate(
                    [
                        'company_id' => $company->id,
                        'code'       => $c['code'],
                    ],
                    [
                        'name'        => $c['name'],
                        'description' => $c['description'],
                        'is_active'   => true,
                    ]
                );
            }
        }
    }
}
