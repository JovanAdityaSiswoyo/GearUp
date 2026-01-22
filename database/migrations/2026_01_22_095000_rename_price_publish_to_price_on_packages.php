<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Drop old price column
            if (Schema::hasColumn('packages', 'price')) {
                $table->dropColumn('price');
            }
        });

        // Rename price_publish to price and change type to int
        DB::statement('ALTER TABLE packages CHANGE COLUMN price_publish price INT DEFAULT 0');
    }

    public function down(): void
    {
        // Revert back
        DB::statement('ALTER TABLE packages CHANGE COLUMN price price_publish DECIMAL(18,2) NULL');
        
        Schema::table('packages', function (Blueprint $table) {
            $table->decimal('price', 12, 2)->default(0)->after('duration_days');
        });
    }
};
