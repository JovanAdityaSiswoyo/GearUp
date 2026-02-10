<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\OrderStatus;

// Update some book_products to Confirmed status
$updated = BookProduct::limit(3)->update([
    'order_status' => OrderStatus::CONFIRMED->value
]);

echo "Updated $updated book_products to Confirmed status\n";

// Update some books to Confirmed status  
$updated2 = Book::limit(2)->update([
    'order_status' => OrderStatus::CONFIRMED->value
]);

echo "Updated $updated2 books to Confirmed status\n";

// Verify
$confirmedProducts = BookProduct::where('order_status', OrderStatus::CONFIRMED->value)->count();
$confirmedBooks = Book::where('order_status', OrderStatus::CONFIRMED->value)->count();

echo "\nNow have:\n";
echo "- $confirmedProducts book_products with Confirmed status\n";
echo "- $confirmedBooks books with Confirmed status\n";
