<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Master\Entities\Company;
use Modules\Master\Entities\Toko;

class TokoSeeder extends Seeder
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

        $tokos = [
            ['name' => 'griziehome', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'MATCH Official Store', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'Kadoku Indonesia', 'category' => Toko::CATEGORY_PACKAGING],
            ['name' => 'spartanhardware', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'Flusso Official Shop', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'tophome178', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'Bumi Putera Perkasa', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'Veloce Official Shop', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'BPP TIKTOK', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'GRANDHOME', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'FOKUS TIKTOK', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'GOOD TIKTOK', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'Prospero Hardware', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'KADOKU TIKTOK', 'category' => Toko::CATEGORY_PACKAGING],
            ['name' => 'GoodHardware', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'GOGOPACK', 'category' => Toko::CATEGORY_PACKAGING],
            ['name' => 'FLUSSO TIKTOK', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'SPARTAN TIKTOK', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'VINCI TIKTOK', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'PAP TIKTOK', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'comfyhome88', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'VELOCE TIKTOK', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => '68home', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'Fokus Bangunan', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'PAP-2', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'SPARTAN LAZADA', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'IPB-2', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'FASHION CHAPTER', 'category' => Toko::CATEGORY_PACKAGING],
            ['name' => 'UM-2', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'JM-2', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'Powerbangunan', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'trenhome', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'STELLARA', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'Tiktok Gogopack', 'category' => Toko::CATEGORY_PACKAGING],
            ['name' => 'ZOXXO', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'TIKTOK FASHION CHAPTER', 'category' => Toko::CATEGORY_PACKAGING],
            ['name' => 'MATCH LAZADA', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'Kadoku Indonesia_SG', 'category' => Toko::CATEGORY_PACKAGING],
            ['name' => 'GREATHOME', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'TIKTOK MATCH', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'HIGAZEUS', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'TIKTOK NOVAREIGN', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'OFFLINE MATCH SALES', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'OFFLINE GOOD SALES', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'CTH DISPLAY DEPAN', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'OFFLINE UM', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'OFFLINE PAP SALES', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'OFFLINE BPP SALES', 'category' => Toko::CATEGORY_BANGUNAN],
            ['name' => 'J40 WA KADOKU', 'category' => Toko::CATEGORY_PACKAGING],
        ];

        foreach ($tokos as $index => $item) {
            $tokoName = $item['name'];
            $category = $item['category'];

            $cleanCode = 'TK-' . Str::upper(Str::slug($tokoName, '-'));
            if (strlen($cleanCode) > 50) {
                $cleanCode = substr($cleanCode, 0, 50);
            }

            // Find existing by exact name or code
            $existing = Toko::where('name', $tokoName)->first()
                ?: Toko::where('code', $cleanCode)->first();

            if ($existing) {
                $existing->update([
                    'company_id' => $company->id,
                    'name'       => $tokoName,
                    'category'   => $category,
                    'is_active'  => true,
                ]);
            } else {
                Toko::create([
                    'company_id' => $company->id,
                    'name'       => $tokoName,
                    'code'       => $cleanCode,
                    'category'   => $category,
                    'phone'      => sprintf('08123456%04d', $index + 1),
                    'email'      => strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $tokoName)) . '@toko.paphardware.com',
                    'address'    => 'Komplek Pergudangan PAP Blok ' . chr(65 + ($index % 26)) . '-' . ($index + 1) . ', Surabaya',
                    'pic_name'   => 'PIC ' . $tokoName,
                    'is_active'  => true,
                ]);
            }
        }
    }
}
