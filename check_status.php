<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookProduct;
use App\Enums\OrderStatus;

// Check distinct order_status values
$distinct = BookProduct::select('order_status')->distinct()->pluck('order_status')->toArray();
echo "Distinct order_status values in book_products:\n";
print_r($distinct);

echo "\n\nEnum values:\n";
foreach (OrderStatus::cases() as $case) {
    echo "- {$case->name}: {$case->value}\n";
}

echo "\n\nCount by status:\n";
foreach ($distinct as $status) {
    $count = BookProduct::where('order_status', $status)->count();
    echo "Status '$status': $count records\n";
}

echo "\n\nTrying enum query:\n";
$confirmed = BookProduct::where('order_status', OrderStatus::CONFIRMED)->get();
echo "Found " . $confirmed->count() . " records with OrderStatus::CONFIRMED\n";

echo "\n\nActual first records:\n";
$samples = BookProduct::limit(5)->get();
foreach ($samples as $sample) {
    echo "Code: {$sample->book_code}, Status: '{$sample->order_status}'\n";
}
