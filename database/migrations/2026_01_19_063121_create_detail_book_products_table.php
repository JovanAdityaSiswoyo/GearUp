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
        Schema::create('detail_book_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_book_product')->constrained('book_products');
            $table->string('name');
            $table->string('email');
            $table->string('telp');
            $table->integer('price');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_book_products');
    }
};
