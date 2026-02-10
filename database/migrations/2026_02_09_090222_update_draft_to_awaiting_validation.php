<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update semua record dengan order_status 'draft' atau 'Draft' menjadi 'Awaiting Validation'
        DB::table('books')
            ->whereIn('order_status', ['draft', 'Draft'])
            ->update(['order_status' => 'Awaiting Validation']);

        DB::table('book_products')
            ->whereIn('order_status', ['draft', 'Draft'])
            ->update(['order_status' => 'Awaiting Validation']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rollback: kembalikan ke Draft (jika diperlukan)
        DB::table('books')
            ->where('order_status', 'Awaiting Validation')
            ->update(['order_status' => 'Draft']);

        DB::table('book_products')
            ->where('order_status', 'Awaiting Validation')
            ->update(['order_status' => 'Draft']);
    }
};
