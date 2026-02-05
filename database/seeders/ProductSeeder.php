<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Admin;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::query()->first();
        $categories = Category::query()->get();
        $brands = Brand::query()->get()->keyBy('name');

        if (!$admin || $categories->isEmpty() || $brands->isEmpty()) {
            return;
        }

        $products = [
            ['brand' => "Arc'teryx", 'name' => "Jaket Arc'teryx Beta", 'desc' => 'Jaket tahan angin dan hujan untuk pendakian', 'description' => 'Jaket teknis ringan dengan perlindungan maksimal untuk kegiatan outdoor.'],
            ['brand' => 'Consina', 'name' => 'Tenda Consina Alpine 2P', 'desc' => 'Tenda 2 orang untuk camping', 'description' => 'Tenda ringkas dan stabil untuk cuaca berangin dan hujan.'],
            ['brand' => 'Eiger', 'name' => 'Carrier Eiger 60L', 'desc' => 'Tas carrier kapasitas besar', 'description' => 'Carrier nyaman dengan sistem back support untuk perjalanan jauh.'],
            ['brand' => 'Mammut', 'name' => 'Sepatu Hiking Mammut Mercury', 'desc' => 'Sepatu hiking tahan air', 'description' => 'Sepatu hiking dengan grip kuat untuk medan berbatu.'],
            ['brand' => 'Montbell', 'name' => 'Sleeping Bag Montbell Down', 'desc' => 'Sleeping bag hangat dan ringan', 'description' => 'Cocok untuk suhu dingin dengan kompresi mudah.'],
            ['brand' => 'Osprey', 'name' => 'Daypack Osprey Talon 22', 'desc' => 'Tas harian untuk trekking', 'description' => 'Daypack ergonomis dengan ventilasi punggung.'],
            ['brand' => 'Patagonia', 'name' => 'Fleece Patagonia Micro D', 'desc' => 'Jaket fleece hangat', 'description' => 'Fleece ringan untuk layer tambahan saat dingin.'],
            ['brand' => 'Quechua', 'name' => 'Matras Quechua Comfort', 'desc' => 'Matras tidur nyaman', 'description' => 'Matras ringan dengan isolasi yang baik.'],
            ['brand' => 'Arei Outdoor Gear', 'name' => 'Jaket Arei Storm Pro', 'desc' => 'Jaket hujan outdoor', 'description' => 'Jaket tahan air dengan ventilasi yang baik.'],
            ['brand' => 'The North Face', 'name' => 'Jaket The North Face Resolve', 'desc' => 'Jaket waterproof serbaguna', 'description' => 'Jaket ringan untuk aktivitas harian dan hiking.'],
            ['brand' => "Arc'teryx", 'name' => "Celana Arc'teryx Gamma", 'desc' => 'Celana softshell trekking', 'description' => 'Celana fleksibel dan tahan abrasi untuk pendakian.'],
            ['brand' => 'Consina', 'name' => 'Kompor Consina Portable', 'desc' => 'Kompor gas portable', 'description' => 'Kompor ringkas untuk memasak di alam.'],
            ['brand' => 'Eiger', 'name' => 'Jaket Eiger Raptor', 'desc' => 'Jaket outdoor multifungsi', 'description' => 'Jaket nyaman untuk cuaca sejuk dan angin.'],
            ['brand' => 'Mammut', 'name' => 'Helm Mammut Skywalker', 'desc' => 'Helm climbing ringan', 'description' => 'Helm dengan ventilasi baik dan perlindungan optimal.'],
            ['brand' => 'Montbell', 'name' => 'Raincover Montbell 35L', 'desc' => 'Cover tas anti hujan', 'description' => 'Raincover praktis untuk melindungi tas dari hujan.'],
            ['brand' => 'Osprey', 'name' => 'Carrier Osprey Atmos 65', 'desc' => 'Carrier premium untuk trekking', 'description' => 'Carrier dengan ventilasi dan suspensi nyaman.'],
            ['brand' => 'Patagonia', 'name' => 'Kaos Patagonia Capilene', 'desc' => 'Kaos base layer', 'description' => 'Base layer cepat kering dan nyaman.'],
            ['brand' => 'Quechua', 'name' => 'Trekking Pole Quechua', 'desc' => 'Tongkat trekking ringan', 'description' => 'Trekking pole adjustable untuk stabilitas di tanjakan.'],
            ['brand' => 'Arei Outdoor Gear', 'name' => 'Tas Arei 45L', 'desc' => 'Tas hiking kapasitas sedang', 'description' => 'Tas hiking dengan kompartemen rapi dan nyaman.'],
            ['brand' => 'The North Face', 'name' => 'Sleeping Bag The North Face Eco', 'desc' => 'Sleeping bag untuk cuaca dingin', 'description' => 'Sleeping bag hangat dengan bahan ramah lingkungan.'],
        ];

        $categoryIndex = 0;
        $categoryCount = $categories->count();

        foreach ($products as $product) {
            $brand = $brands->get($product['brand']);
            if (!$brand) {
                continue;
            }

            $category = $categories[$categoryIndex % $categoryCount];
            $categoryIndex++;

            Product::updateOrCreate(
                ['name' => $product['name']],
                [
                    'id_admins' => $admin->id,
                    'id_category' => $category->id,
                    'brand_id' => $brand->id,
                    'name' => $product['name'],
                    'desc' => $product['desc'],
                    'description' => $product['description'],
                    'status' => 'available',
                    'price' => 1500000,
                    'price_per_day' => 75000,
                    'stock' => 10,
                    'image' => null,
                ]
            );
        }
    }
}
