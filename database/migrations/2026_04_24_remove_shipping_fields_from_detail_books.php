<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detail_books', function (Blueprint $table) {
            $table->dropColumn(['shipping_method', 'shipping_date']);
        });

        Schema::table('detail_book_products', function (Blueprint $table) {
            $table->dropColumn(['shipping_method', 'shipping_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detail_books', function (Blueprint $table) {
            $table->string('shipping_method');
            $table->date('shipping_date');
        });

        Schema::table('detail_book_products', function (Blueprint $table) {
            $table->string('shipping_method');
            $table->date('shipping_date');
        });
    }
};
