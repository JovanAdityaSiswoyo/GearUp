<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Normalize legacy status values in book_products.
        DB::statement("UPDATE book_products SET order_status = 'pending' WHERE order_status IN ('Draft', 'Awaiting Validation', 'Confirmed', 'Ready for Pickup')");
        DB::statement("UPDATE book_products SET order_status = 'dipinjam' WHERE order_status IN ('Out for Delivery', 'Delivered', 'Pickup Scheduled', 'On Process Return', 'Overdue')");
        DB::statement("UPDATE book_products SET order_status = 'selesai' WHERE order_status IN ('Pending Review', 'Completed', 'Issue Detected', 'Cancelled')");

        // Normalize legacy status values in books.
        DB::statement("UPDATE books SET order_status = 'pending' WHERE order_status IN ('Draft', 'Awaiting Validation', 'Confirmed', 'Ready for Pickup')");
        DB::statement("UPDATE books SET order_status = 'dipinjam' WHERE order_status IN ('Out for Delivery', 'Delivered', 'Pickup Scheduled', 'On Process Return', 'Overdue')");
        DB::statement("UPDATE books SET order_status = 'selesai' WHERE order_status IN ('Pending Review', 'Completed', 'Issue Detected', 'Cancelled')");

        // Enforce new default on both booking tables.
        DB::statement("ALTER TABLE book_products MODIFY order_status VARCHAR(255) NOT NULL DEFAULT 'pending'");
        DB::statement("ALTER TABLE books MODIFY order_status VARCHAR(255) NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE book_products MODIFY order_status VARCHAR(255) NOT NULL DEFAULT 'Awaiting Validation'");
        DB::statement("ALTER TABLE books MODIFY order_status VARCHAR(255) NOT NULL DEFAULT 'Awaiting Validation'");
    }
};
