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
        Schema::table('package_products', function (Blueprint $table) {
            // Drop existing table and recreate with proper structure
        });

        // Drop and recreate the table
        Schema::dropIfExists('package_products');

        Schema::create('package_products', function (Blueprint $table) {
            $table->id();
            $table->uuid('id_package');
            $table->uuid('id_product');
            $table->timestamps();

            // Foreign keys
            $table->foreign('id_package')
                ->references('id')
                ->on('packages')
                ->onDelete('cascade');

            $table->foreign('id_product')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            // Unique constraint to prevent duplicates
            $table->unique(['id_package', 'id_product']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_products');
    }
};
