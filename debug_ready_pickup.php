<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check all bookings with READY_FOR_PICKUP status
$bookProducts = App\Models\BookProduct::where('order_status', App\Enums\OrderStatus::READY_FOR_PICKUP)->get();

echo "BookProducts with READY_FOR_PICKUP: " . $bookProducts->count() . "\n\n";

foreach ($bookProducts as $bp) {
    echo "BookProduct Code: {$bp->book_code}\n";
    echo "Status: {$bp->order_status->value}\n";
    echo "Courier ID: " . ($bp->id_courier ?? 'NULL') . "\n";
    echo "---\n";
}

$books = App\Models\Book::where('order_status', App\Enums\OrderStatus::READY_FOR_PICKUP)->get();

echo "\nBooks with READY_FOR_PICKUP: " . $books->count() . "\n\n";

foreach ($books as $book) {
    echo "Book Code: {$book->book_code}\n";
    echo "Status: {$book->order_status->value}\n";
    echo "Courier ID: " . ($book->id_courier ?? 'NULL') . "\n";
    echo "---\n";
}
