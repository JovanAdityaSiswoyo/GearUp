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
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('price_per_day', 12, 2)->nullable()->after('price');
            $table->integer('stock')->nullable()->after('price_per_day');
            $table->text('description')->nullable()->after('stock');
            $table->uuid('brand_id')->nullable()->after('id_category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['brand_id', 'price_per_day', 'stock', 'description']);
        });
    }
};
