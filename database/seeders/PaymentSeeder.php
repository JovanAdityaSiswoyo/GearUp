<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\BookProduct;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        // Seed payments for books
        Book::all()->each(function ($book) {
            Payment::create([
                'id' => Str::uuid(),
                'payable_type' => Book::class,
                'payable_id' => $book->id,
                'amount' => $book->amount,
                'currency' => 'IDR',
                'status' => 'paid',
                'provider' => 'manual',
                'provider_ref' => 'BOOK-' . strtoupper(Str::random(6)),
                'method' => 'cash',
                'paid_at' => Carbon::now()->subDays(rand(0, 7)),
                'meta' => ['note' => 'Seed payment for booking'],
            ]);
        });

        // Seed payments for book_products
        BookProduct::all()->each(function ($bookProduct) {
            Payment::create([
                'id' => Str::uuid(),
                'payable_type' => BookProduct::class,
                'payable_id' => $bookProduct->id,
                'amount' => $bookProduct->amount,
                'currency' => 'IDR',
                'status' => 'paid',
                'provider' => 'manual',
                'provider_ref' => 'BPR-' . strtoupper(Str::random(6)),
                'method' => 'cash',
                'paid_at' => Carbon::now()->subDays(rand(0, 7)),
                'meta' => ['note' => 'Seed payment for product booking'],
            ]);
        });
    }
}
