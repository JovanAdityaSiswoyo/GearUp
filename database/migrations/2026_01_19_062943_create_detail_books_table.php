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
        Schema::create('detail_books', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_book')->constrained('books');
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
        Schema::dropIfExists('detail_books');
    }
};
