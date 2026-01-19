<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
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
                $shippingDate = fake()->dateTimeBetween('now', '+7 days');
                $rentalStart = Carbon::instance($shippingDate)->addHours(rand(1, 24));
                $rentalEnd = (clone $rentalStart)->addDays(rand(3, 14));

                $bookProduct->detailBookProducts()->create([
                    'id' => Str::uuid(),
                    'full_name' => fake()->name(),
                    'instagram_handle' => fake()->optional()->userName(),
                    'other_socials' => fake()->optional()->url(),
                    'phone_number' => fake()->numerify('08##########'),
                    'emergency_phone_number' => fake()->numerify('08##########'),
                    'shipping_method' => fake()->randomElement(['JNE', 'GRABSEND', 'GOSEND', 'COD', 'PAXEL']),
                    'renter_address' => fake()->address(),
                    'shipping_date' => $shippingDate,
                    'rental_start_at' => $rentalStart,
                    'rental_end_at' => $rentalEnd,
                    'identity_document_path' => 'identity_docs/' . Str::uuid() . '.jpg',
                ]);
            }
        });
    }
}
