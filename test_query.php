<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\BookProduct;
use App\Models\Book;
use App\Enums\OrderStatus;

echo "Testing different query methods:\n\n";

// Method 1: whereIn with enum objects
$result1 = BookProduct::whereIn('order_status', [
    OrderStatus::CONFIRMED,
    OrderStatus::READY_FOR_PICKUP,
])->count();
echo "1. whereIn([enum, enum]): $result1 records\n";

// Method 2: whereIn with enum values (strings)
$result2 = BookProduct::whereIn('order_status', [
    OrderStatus::CONFIRMED->value,
    OrderStatus::READY_FOR_PICKUP->value,
])->count();
echo "2. whereIn([enum->value, enum->value]): $result2 records\n";

// Method 3: where with single enum
$result3 = BookProduct::where('order_status', OrderStatus::CONFIRMED)->count();
echo "3. where(status, enum): $result3 records\n";

// Method 4: where with string value
$result4 = BookProduct::where('order_status', OrderStatus::CONFIRMED->value)->count();
echo "4. where(status, enum->value): $result4 records\n";

echo "\nEnum CONFIRMED value: '" . OrderStatus::CONFIRMED->value . "'\n";
echo "Enum READY_FOR_PICKUP value: '" . OrderStatus::READY_FOR_PICKUP->value . "'\n";

echo "\nActual records in database with Confirmed status:\n";
$records = BookProduct::where('order_status', 'Confirmed')->get();
foreach ($records as $record) {
    echo "- {$record->book_code}: stored as '" . $record->getRawOriginal('order_status') . "'\n";
}
