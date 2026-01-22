<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Make publish-related columns nullable so package creation doesn't fail when values are not provided.
        Schema::table('packages', function (Blueprint $table) {
            // Use raw statements for broader DB support when changing nullability.
        });

        DB::statement('ALTER TABLE packages MODIFY start_publish DATETIME NULL');
        DB::statement('ALTER TABLE packages MODIFY end_publish DATETIME NULL');
        DB::statement('ALTER TABLE packages MODIFY price_publish DATETIME NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to NOT NULL with current timestamp defaults to preserve previous behavior.
        DB::statement("ALTER TABLE packages MODIFY start_publish DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
        DB::statement("ALTER TABLE packages MODIFY end_publish DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
        DB::statement("ALTER TABLE packages MODIFY price_publish DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
    }
};
