<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\OrderStatus;

echo "Testing OfficerPackingController query logic:\n\n";

// Get individual product bookings (from the controller)
$productBookings = BookProduct::with(['product.category', 'user'])
    ->whereIn('order_status', [
        OrderStatus::CONFIRMED,
        OrderStatus::READY_FOR_PICKUP,
    ])
    ->get()
    ->map(function($booking) {
        return (object)[
            'id' => $booking->id,
            'book_code' => $booking->book_code,
            'booker_name' => $booking->booker_name,
            'item_name' => $booking->product->name ?? 'N/A',
            'item_type' => 'Product',
            'order_status' => $booking->order_status,
            'checkin_date' => $booking->checkin_appointment_start,
            'created_at' => $booking->created_at,
            'type' => 'book-product',
        ];
    });

echo "Product bookings found: " . $productBookings->count() . "\n";
foreach ($productBookings as $booking) {
    echo "  - {$booking->book_code} ({$booking->item_name})\n";
}

// Get package bookings
$packageBookings = Book::with(['package', 'user'])
    ->whereIn('order_status', [
        OrderStatus::CONFIRMED,
        OrderStatus::READY_FOR_PICKUP,
    ])
    ->get()
    ->map(function($booking) {
        return (object)[
            'id' => $booking->id,
            'book_code' => $booking->book_code,
            'booker_name' => $booking->booker_name,
            'item_name' => $booking->package->name_package ?? 'N/A',
            'item_type' => 'Package',
            'order_status' => $booking->order_status,
            'checkin_date' => $booking->checkin_appointment_start,
            'created_at' => $booking->created_at,
            'type' => 'book',
        ];
    });

echo "\nPackage bookings found: " . $packageBookings->count() . "\n";
foreach ($packageBookings as $booking) {
    echo "  - {$booking->book_code} ({$booking->item_name})\n";
}

// Merge
$bookings = $productBookings->merge($packageBookings)->sortByDesc('created_at');
echo "\nTotal bookings in packing queue: " . $bookings->count() . "\n";
