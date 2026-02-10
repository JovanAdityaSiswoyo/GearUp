<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Check courier
$couriers = App\Models\Courier::all();
echo "Total Couriers: " . $couriers->count() . "\n\n";

foreach ($couriers as $courier) {
    echo "Courier ID: {$courier->id}\n";
    echo "Courier Name: {$courier->name}\n";
    echo "Courier Email: {$courier->email}\n";
    
    // Check bookings for this courier
    $bookProducts = App\Models\BookProduct::where('id_courier', $courier->id)
        ->whereIn('order_status', [
            App\Enums\OrderStatus::READY_FOR_PICKUP,
            App\Enums\OrderStatus::OUT_FOR_DELIVERY
        ])
        ->get();
    
    echo "BookProducts assigned: " . $bookProducts->count() . "\n";
    
    foreach ($bookProducts as $bp) {
        echo "  - BookProduct Code: {$bp->book_code}, Status: {$bp->order_status->value}\n";
    }
    
    $books = App\Models\Book::where('id_courier', $courier->id)
        ->whereIn('order_status', [
            App\Enums\OrderStatus::READY_FOR_PICKUP,
            App\Enums\OrderStatus::OUT_FOR_DELIVERY
        ])
        ->get();
    
    echo "Books assigned: " . $books->count() . "\n";
    
    foreach ($books as $book) {
        echo "  - Book Code: {$book->book_code}, Status: {$book->order_status->value}\n";
    }
    
    echo "\n";
}
