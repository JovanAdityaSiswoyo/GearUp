<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DetailBookSeeder extends Seeder
{
    public function run(): void
    {
        // Create detail_books for each book
        Book::all()->each(function ($book) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $book->detailBooks()->create([
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
