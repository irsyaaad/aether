<?php

namespace Modules\Master\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Master\Entities\Company;
use Modules\Master\Entities\Produk;

class ProdukSeeder extends Seeder
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

        $rawProducts = [
            '2769-SIKU-L',
            '3283-SCRAPPER',
            '3215-1096U-HTM',
            '2053-SLDG.TK-3',
            '3211-1094U-HTM',
            'PJC03C-TIRAI-100X210COFFEE',
            '859-KAFLEX-PE4S',
            'AA408',
            'AA105',
            '2468',
            '2469',
            'PJC02D-TIRAI-90X220WHITE',
            '2901-SAMPAH-HJU',
            '2788',
            '3076-SAMPAH-MTF',
            'PJC03D-TIRAI-100X210WHITE',
            '2854-KDB-PE8KROM',
            '2798-E-SENDOK',
            '2585-FLS-AFUR',
            '2868-SKD-PE8SN',
            '2665-BAUTDRILL',
            '2808-SIKU-A10',
            '526-MM99-SNNP',
            '810-FITTING',
            '2369-GT-HDK30BL',
            '2470-GT-BAJU',
            '2678-KABELTIES',
            '2825-BLCKPREM41',
            '848-SHOWERTIANG',
            '2576-KATUPAFUR',
            '2209-KAWTFLXPB7',
            'AA308-WAJANPAN-34CM',
            '2771-SIKU-L',
            '599-KA-PB1',
            '2414-RBUMBU60BL',
            '3277-KAFLEX-BL',
            '905-FT005-BCGP',
            '3101-GERGAJI',
            '2857-KTB-PE8KROM',
            '3226-1110U-PTH',
            '2901-SAMPAH-HJU',
            '266-GT-BAJU',
            '902-FT002-BCGP',
            'AA29 PTH',
            '2993-H2 TAPLAKMEJA',
            'AA261-B49',
            '2897-SLG-IN2',
            '3051-RDAPUR40BL',
            '2083-KWT-ZE1',
            '2100-KA-SE5',
            '2413-RBUMBU50BL',
            '2008-SHOWER',
            '2187-SK-PE7GP',
            '455-RAKHANDUK',
            '934-SLGAYM-30BL',
            '3041-SRG TGN-BRU',
            '2218-KDB-PE7CR',
            '2837-804-BLCK41',
            '261-HM90-SNGP',
            '2523-GT-BAJU',
            'AA260-B48',
            '2584-FLS-AFUR',
            '780-SHOWER',
            '2203-AFUR',
            '2161-KAFLXPE7SN',
            '2781',
            '3243-PTOP-HTM',
            '3042-SRG TGN-BRU',
            'AA391 C-03',
            '3173-RACHETTIE',
            'AA259-B47',
            '857-KA-PE4',
            '792-SLG.FLEX-30',
            '2682-KABELTIES',
            '2807-SIKU-A8',
            '2471-GT-BAJU',
            '2464-GT-BAJU',
            '712-HM90-BNNB',
            '2475-TISU-K04B',
            '2298-KABELTIES',
            '2785',
            'AA267-B55',
            'AA269-B57',
            'PJC03B-TIRAI-100X210GREY',
            '605-SKD-PE3',
            '2039-TGG005BCSN',
            'PJC02C-TIRAI-90X220COFFEE',
            '2063-SIKUPTH-10',
            '2062-SIKUPTH-8',
            '3279-KAFLEX-CHRM',
            '3174-RACHETTIE',
            '2991-G3 TAPLAKMEJA',
            '2486-T.SABUN-BL',
            '655-KDB-PB3',
            '623-KA-SE1',
            '2148-BODYPELOR',
            '720-HL84-BNNB',
            '3001-HELMNEW',
            '344-NEPEL',
            'PJC01A-TIRAI-90X210BLACK',
            'PJC01B-TIRAI-90X210GREY',
            '2222-SKD-PE7CR',
            '2033-TGG001BCSN',
            '3038-SAMPAH-BRU',
            '872-BCP120',
            '2973-A3 TAPLAKMEJA',
            '2766-SIKU-L',
            '2806-SIKU-A6',
            '2793',
            '2054-SLDG.TK-4',
            'AA420-HOLDER-360',
            '2055-SLIDING-3',
            '3044-SRG TGN-HJU',
            '3217-1097U-HTM',
            '3283-SCRAPPER',
            '2215-KA-PE7CR',
            '2045-BCP-8245SS',
            '3282-SCRAPPER',
            '3045-SRG TGN-HJU',
            '306-VNC-ENGSEL5',
            '550-ML89-SNNP',
            '2955-KA-PE8SN',
            '457-RAKHANDUK',
            '2829-KNG-PREM39',
            '807-NEPEL',
            '2728-HELM',
            '2218-KDB-PE7CR',
            'PJC03A-TIRAI-100X210BLACK',
            '2902-SAMPAH-BRU',
            'KC48- KERANJANG GORENG',
            '520-SIFON-JUMBO',
            '2516-AFURTALANG',
        ];

        // Deduplicate items safely by code
        $uniqueProducts = [];
        foreach ($rawProducts as $p) {
            $code = trim($p);
            if (!empty($code)) {
                $uniqueProducts[$code] = $code;
            }
        }

        $categories = ['Hardware', 'Perkakas', 'Sanitary', 'Aksesoris Rumah', 'Dapur', 'Dekorasi'];

        $i = 1;
        foreach ($uniqueProducts as $code) {
            // Determine friendly name
            $name = $this->formatProductName($code);
            $category = $categories[$i % count($categories)];
            $price = 15000 + (($i * 3500) % 250000);
            $costPrice = round($price * 0.75, -2);
            $stock = 20 + (($i * 7) % 150);

            Produk::firstOrCreate(
                ['code' => $code],
                [
                    'company_id'  => $company->id,
                    'name'        => $name,
                    'category'    => $category,
                    'unit'        => 'Pcs',
                    'price'       => $price,
                    'cost_price'  => $costPrice,
                    'stock'       => $stock,
                    'min_stock'   => 10,
                    'description' => "Produk master {$name} (Kode: {$code})",
                    'is_active'   => true,
                ]
            );
            $i++;
        }
    }

    /**
     * Helper to make friendly product names
     */
    private function formatProductName(string $code): string
    {
        $cleaned = trim($code);
        
        // If code contains hyphen e.g. 2769-SIKU-L or KC48- KERANJANG GORENG
        if (str_contains($cleaned, '-')) {
            $parts = explode('-', $cleaned, 2);
            $title = trim($parts[1]);
            return ucwords(strtolower(str_replace(['_', '-'], ' ', $title))) . ' (' . $parts[0] . ')';
        }

        // If code is numeric or alphanumeric e.g. AA408 or 2468
        return 'Produk ' . $cleaned;
    }
}
