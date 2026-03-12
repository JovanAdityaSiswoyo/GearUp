<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run data updates on schemas that already contain order_status.
        if (Schema::hasColumn('books', 'order_status')) {
            DB::table('books')
                ->whereIn('order_status', ['draft', 'Draft'])
                ->update(['order_status' => 'Awaiting Validation']);
        }

        if (Schema::hasColumn('book_products', 'order_status')) {
            DB::table('book_products')
                ->whereIn('order_status', ['draft', 'Draft'])
                ->update(['order_status' => 'Awaiting Validation']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback only when order_status exists.
        if (Schema::hasColumn('books', 'order_status')) {
            DB::table('books')
                ->where('order_status', 'Awaiting Validation')
                ->update(['order_status' => 'Draft']);
        }

        if (Schema::hasColumn('book_products', 'order_status')) {
            DB::table('book_products')
                ->where('order_status', 'Awaiting Validation')
                ->update(['order_status' => 'Draft']);
        }
    }
};
