<?php

namespace Database\Seeders;

use App\Models\Package;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        Package::factory(8)->create()->each(function ($package) {
            $products = Product::inRandomOrder()->limit(rand(2, 5))->get();
            foreach ($products as $product) {
                $package->products()->attach(
                    $product->id,
                    ['id' => Str::uuid()]
                );
            }
        });
    }
}
