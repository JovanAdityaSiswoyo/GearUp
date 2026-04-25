<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payment_id')->nullable();
            $table->string('provider')->default('midtrans');
            $table->string('payment_type')->default('bank_transfer');
            $table->string('bank')->nullable();
            $table->string('status')->default('pending');
            $table->string('transaction_id')->nullable();
            $table->string('order_id')->nullable();
            $table->integer('amount');
            $table->string('currency', 10)->default('IDR');
            $table->dateTime('expires_at')->nullable();
            $table->json('request_payload')->nullable();
            $table->json('response_payload')->nullable();
            $table->timestamps();

            $table->foreign('payment_id')->references('id')->on('payments')->cascadeOnDelete();
            $table->index('status');
            $table->index('transaction_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
