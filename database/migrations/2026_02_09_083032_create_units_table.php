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
        Schema::create('units', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('id_product')->constrained('products')->onDelete('cascade');
            $table->string('serial_number')->unique()->comment('Nomor seri/QR code unit');
            $table->enum('status', ['available', 'booked', 'deployed', 'returning', 'in_inspection', 'maintenance', 'lost_scrapped'])->default('available');
            $table->text('notes')->nullable()->comment('Catatan kondisi/maintenance');
            $table->timestamp('last_maintenance_at')->nullable();
            $table->timestamps();
            
            $table->index(['id_product', 'status']);
            $table->index('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
