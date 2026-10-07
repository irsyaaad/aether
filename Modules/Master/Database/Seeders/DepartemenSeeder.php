<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Master\Entities\Company;
use Modules\Master\Entities\Departemen;

class DepartemenSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::firstOrCreate(
            ['code' => 'PAP'],
            [
                'name'      => 'PT Putera Adhi Perkasa',
                'email'     => 'admin@paphardware.com',
                'timezone'  => 'Asia/Jakarta',
                'is_active' => true,
            ]
        );

        $departments = [
            ['name' => 'Operasional', 'code' => 'OPS', 'description' => 'Departemen Operasional Toko dan Marketplace'],
            ['name' => 'IT & Digital Marketing', 'code' => 'IT-MKT', 'description' => 'Departemen IT, Konten, dan Digital Marketing'],
            ['name' => 'Keuangan & Akuntansi', 'code' => 'FIN', 'description' => 'Departemen Keuangan, Perpajakan, dan Akuntansi'],
            ['name' => 'Gudang & Inventaris', 'code' => 'INV', 'description' => 'Departemen Pengelolaan Stok dan Inventaris Toko'],
            ['name' => 'Human Resource', 'code' => 'HRD', 'description' => 'Departemen Personalia dan Manajemen Karyawan'],
        ];

        foreach ($departments as $dept) {
            Departemen::firstOrCreate(
                ['code' => $dept['code']],
                [
                    'company_id'  => $company->id,
                    'name'        => $dept['name'],
                    'description' => $dept['description'],
                    'is_active'   => true,
                ]
            );
        }
    }
}
