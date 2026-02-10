<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Unit;
use Illuminate\Support\Str;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products
        $products = Product::all();

        if ($products->isEmpty()) {
            $this->command->warn('No products found. Please seed products first.');
            return;
        }

        $this->command->info('Creating units for products...');

        foreach ($products as $product) {
            // Create 5-10 units per product based on stock
            $unitsCount = max(5, min(10, $product->stock ?? 5));

            for ($i = 1; $i <= $unitsCount; $i++) {
                $serialPrefix = strtoupper(substr($product->name, 0, 3));
                $serialNumber = sprintf('%s-%03d-%s', $serialPrefix, $i, strtoupper(Str::random(4)));

                Unit::create([
                    'id' => Str::uuid(),
                    'id_product' => $product->id,
                    'serial_number' => $serialNumber,
                    'status' => 'available',
                    'notes' => 'Initial stock - Good condition',
                ]);
            }

            $this->command->info("Created {$unitsCount} units for product: {$product->name}");
        }

        $totalUnits = Unit::count();
        $this->command->info("Total units created: {$totalUnits}");
    }
}
