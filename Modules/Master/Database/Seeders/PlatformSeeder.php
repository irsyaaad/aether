<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Master\Entities\Company;
use Modules\Master\Entities\Platform;

class PlatformSeeder extends Seeder
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

        $platforms = [
            [
                'name'           => 'TikTok Shop',
                'code'           => 'TIKTOK',
                'url'            => 'https://seller-id.tiktok.com',
                'fee_percentage' => 6.00,
                'description'    => 'Platform Marketplace & Social Commerce TikTok Shop',
            ],
            [
                'name'           => 'Shopee',
                'code'           => 'SHOPEE',
                'url'            => 'https://seller.shopee.co.id',
                'fee_percentage' => 6.50,
                'description'    => 'Platform Marketplace Shopee Indonesia',
            ],
            [
                'name'           => 'Tokopedia',
                'code'           => 'TOKOPEDIA',
                'url'            => 'https://seller.tokopedia.com',
                'fee_percentage' => 5.50,
                'description'    => 'Platform Marketplace Tokopedia',
            ],
            [
                'name'           => 'Lazada',
                'code'           => 'LAZADA',
                'url'            => 'https://sellercenter.lazada.co.id',
                'fee_percentage' => 5.00,
                'description'    => 'Platform Marketplace Lazada Seller Center',
            ],
            [
                'name'           => 'Blibli',
                'code'           => 'BLIBLI',
                'url'            => 'https://seller.blibli.com',
                'fee_percentage' => 4.50,
                'description'    => 'Platform Marketplace Blibli Merchant Portal',
            ],
            [
                'name'           => 'Bukalapak',
                'code'           => 'BUKALAPAK',
                'url'            => 'https://seller.bukalapak.com',
                'fee_percentage' => 4.00,
                'description'    => 'Platform Marketplace Bukalapak',
            ],
            [
                'name'           => 'Toko Offline / Kasir POS',
                'code'           => 'OFFLINE',
                'url'            => null,
                'fee_percentage' => 0.00,
                'description'    => 'Penjualan Langsung di Toko Fisik / Point of Sale',
            ],
        ];

        foreach ($platforms as $item) {
            Platform::firstOrCreate(
                ['code' => $item['code']],
                [
                    'company_id'     => $company->id,
                    'name'           => $item['name'],
                    'url'            => $item['url'],
                    'fee_percentage' => $item['fee_percentage'],
                    'description'    => $item['description'],
                    'is_active'      => true,
                ]
            );
        }

        // Seed permissions into role_menu table for Administrator (1) and Admin (6)
        if (\Illuminate\Support\Facades\Schema::hasTable('role_menu')) {
            $permissions = ['platforms.index', 'platforms.create', 'platforms.update', 'platforms.delete'];
            foreach ([1, 6] as $roleId) {
                foreach ($permissions as $perm) {
                    \Illuminate\Support\Facades\DB::table('role_menu')->updateOrInsert(
                        ['id_role' => $roleId, 'permission' => $perm],
                        ['updated_at' => now()]
                    );
                }
            }
        }
    }
}
