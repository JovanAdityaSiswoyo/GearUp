<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Change price_publish to decimal and add upsell
        Schema::table('packages', function (Blueprint $table) {
            if (!Schema::hasColumn('packages', 'upsell')) {
                $table->decimal('upsell', 12, 2)->default(0)->after('price');
            }
        });

        // Clean existing datetime values to prevent overflow when converting
        DB::statement('UPDATE packages SET price_publish = NULL');

        // Use raw statements to alter existing column type with wider precision
        DB::statement('ALTER TABLE packages MODIFY price_publish DECIMAL(18,2) NULL DEFAULT 0');
    }

    public function down(): void
    {
        // Revert price_publish to DATETIME
        DB::statement('ALTER TABLE packages MODIFY price_publish DATETIME NULL');

        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'upsell')) {
                $table->dropColumn('upsell');
            }
        });
    }
};
