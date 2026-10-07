<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Master\Entities\Company;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        Company::firstOrCreate(
            ['code' => 'PAP'],
            [
                'name'      => 'PT Putera Adhi Perkasa',
                'email'     => 'admin@paphardware.com',
                'phone'     => '081234567890',
                'address'   => 'Surabaya, Jawa Timur, Indonesia',
                'website'   => 'https://paphardware.com',
                'timezone'  => 'Asia/Jakarta',
                'is_active' => true,
            ]
        );
    }
}
