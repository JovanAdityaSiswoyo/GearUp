<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => 'Arc\'teryx',
                'description' => 'Premium outdoor and technical apparel brand',
            ],
            [
                'name' => 'Consina',
                'description' => 'Indonesian outdoor gear and equipment brand',
            ],
            [
                'name' => 'Eiger',
                'description' => 'Indonesian adventure gear and outdoor apparel',
            ],
            [
                'name' => 'Mammut',
                'description' => 'Swiss quality mountaineering and outdoor gear',
            ],
            [
                'name' => 'Montbell',
                'description' => 'Japanese outdoor gear and camping equipment',
            ],
            [
                'name' => 'Osprey',
                'description' => 'Premium backpacks and travel gear',
            ],
            [
                'name' => 'Patagonia',
                'description' => 'High-quality outdoor clothing and equipment',
            ],
            [
                'name' => 'Quechua',
                'description' => 'Affordable outdoor and hiking gear',
            ],
            [
                'name' => 'Arei Outdoor Gear',
                'description' => 'Indonesian outdoor gear and adventure equipment',
            ],
            [
                'name' => 'The North Face',
                'description' => 'Premium outdoor gear and apparel brand',
            ],
        ];

        foreach ($brands as $brand) {
            Brand::updateOrCreate(['name' => $brand['name']], $brand);
        }
    }
}

