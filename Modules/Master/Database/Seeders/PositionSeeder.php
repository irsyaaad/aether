<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Master\Entities\Company;
use Modules\Master\Entities\Position;

class PositionSeeder extends Seeder
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

        $positions = [
            ['name' => 'Direktur Operasional', 'code' => 'DIR-OPS', 'level' => 1, 'description' => 'Pimpinan operasional perusahaan'],
            ['name' => 'Manager Operasional', 'code' => 'MGR-OPS', 'level' => 2, 'description' => 'Manager operasional harian'],
            ['name' => 'Supervisor Toko & Marketplace', 'code' => 'SPV-MKT', 'level' => 3, 'description' => 'Supervisor toko dan marketplace'],
            ['name' => 'Staff Operasional', 'code' => 'STF-OPS', 'level' => 4, 'description' => 'Staff pelaksana operasional'],
            ['name' => 'Staff Marketplace & CS', 'code' => 'STF-MKT', 'level' => 4, 'description' => 'Staff pengelola marketplace dan customer service'],
            ['name' => 'Staff Gudang & Inventaris', 'code' => 'STF-INV', 'level' => 4, 'description' => 'Staff gudang dan inventaris toko'],
            ['name' => 'Staff Keuangan', 'code' => 'STF-FIN', 'level' => 4, 'description' => 'Staff administrasi keuangan'],
        ];

        foreach ($positions as $pos) {
            Position::firstOrCreate(
                ['code' => $pos['code']],
                [
                    'company_id'  => $company->id,
                    'name'        => $pos['name'],
                    'level'       => $pos['level'],
                    'description' => $pos['description'],
                    'is_active'   => true,
                ]
            );
        }
    }
}
