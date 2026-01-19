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
        Schema::create('books', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_package')->constrained('packages');
            $table->foreignUuid('id_user')->constrained('users');
            $table->string('book_code');
            $table->string('status');
            $table->dateTime('checkin_appointment_start');
            $table->dateTime('checkout_appointment_end');
            $table->integer('amount');
            $table->string('booker_name');
            $table->string('booker_email');
            $table->string('booker_telp');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
