<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Get Ade Kurir
$courier = App\Models\Courier::where('email', 'ade.kurir@aplikasipinjam.com')->first();

if (!$courier) {
    echo "Courier not found!\n";
    exit;
}

// Get the booking
$book = App\Models\Book::where('book_code', 'BK-6975F0033CB72')->first();

if (!$book) {
    echo "Book not found!\n";
    exit;
}

echo "Assigning courier {$courier->name} to booking {$book->book_code}...\n";

$book->id_courier = $courier->id;
$book->save();

echo "Done! Courier assigned.\n";
