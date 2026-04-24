<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class DetailBookSeeder extends Seeder
{
    public function run(): void
    {
        // Create detail_books for each book
        Book::all()->each(function ($book) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $rentalStart = fake()->dateTimeBetween('now', '+7 days');
                $rentalEnd = (clone $rentalStart)->addDays(rand(3, 14));

                $book->detailBooks()->create([
                    'id' => Str::uuid(),
                    'full_name' => fake()->name(),
                    'instagram_handle' => fake()->optional()->userName(),
                    'other_socials' => fake()->optional()->url(),
                    'phone_number' => fake()->numerify('08##########'),
                    'emergency_phone_number' => fake()->numerify('08##########'),
                    'renter_address' => fake()->address(),
                    'rental_start_at' => $rentalStart,
                    'rental_end_at' => $rentalEnd,
                    'identity_document_path' => 'identity_docs/' . Str::uuid() . '.jpg',
                ]);
            }
        });
    }
}
