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
            $table->string('full_name');
            $table->string('instagram_handle')->nullable();
            $table->string('other_socials')->nullable();
            $table->string('phone_number');
            $table->string('emergency_phone_number');
            $table->string('shipping_method');
            $table->text('renter_address');
            $table->date('shipping_date');
            $table->dateTime('rental_start_at');
            $table->dateTime('rental_end_at');
            $table->string('identity_document_path');
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
