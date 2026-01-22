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
        // Disable foreign key checks
        Schema::disableForeignKeyConstraints();

        // Fix model_has_roles
        if (Schema::hasTable('model_has_roles')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                // Check the current type and change if needed
                DB::statement('ALTER TABLE model_has_roles MODIFY model_id VARCHAR(36)');
            });
        }

        // Fix model_has_permissions
        if (Schema::hasTable('model_has_permissions')) {
            Schema::table('model_has_permissions', function (Blueprint $table) {
                // Check the current type and change if needed
                DB::statement('ALTER TABLE model_has_permissions MODIFY model_id VARCHAR(36)');
            });
        }

        // Re-enable foreign key checks
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Do nothing - we don't want to revert this as it would break existing UUID data
        // This is a one-way migration to support UUID-based models
    }
};
