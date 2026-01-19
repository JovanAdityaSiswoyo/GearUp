<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        Book::factory(10)->create();
        
        // Create book_products
        BookProduct::factory(20)->create()->each(function ($bookProduct) {
            // Create detail_book_products for each book_product
            for ($i = 0; $i < rand(2, 4); $i++) {
                $bookProduct->detailBookProducts()->create([
                    'id' => Str::uuid(),
                    'name' => fake()->name(),
                    'email' => fake()->email(),
                    'telp' => fake()->numerify('08##########'),
                    'price' => fake()->numberBetween(50000, 500000),
                ]);
            }
        });
    }
}
