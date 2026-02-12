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
        Schema::table('units', function (Blueprint $table) {
            if (!Schema::hasColumn('units', 'last_scanned_by_id')) {
                $table->uuid('last_scanned_by_id')->nullable()->after('last_maintenance_at');
            }

            if (!Schema::hasColumn('units', 'last_scanned_by_type')) {
                $table->string('last_scanned_by_type')->nullable()->after('last_scanned_by_id');
            }

            $table->index(['last_scanned_by_type', 'last_scanned_by_id'], 'units_last_scanned_by_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            $table->dropIndex('units_last_scanned_by_index');
            $table->dropColumn(['last_scanned_by_id', 'last_scanned_by_type']);
        });
    }
};
