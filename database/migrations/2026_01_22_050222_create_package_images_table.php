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
        Schema::create('package_images', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('package_id')->constrained('packages')->cascadeOnDelete();
            $table->string('image');
            $table->integer('order')->default(0);
            $table->timestamps();
            
            $table->index('package_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('package_images');
    }
};
