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
        Schema::table('packages', function (Blueprint $table) {
            // Drop unused columns
            $table->dropColumn(['name', 'discount']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->string('name')->nullable()->after('name_package');
            $table->decimal('discount', 5, 2)->default(0)->after('upsell');
        });
    }
};
