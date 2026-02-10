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
        Schema::table('book_package_products', function (Blueprint $table) {
            if (!Schema::hasColumn('book_package_products', 'id_unit')) {
                $table->foreignUuid('id_unit')->nullable()->after('id_product')->constrained('units')->onDelete('set null');
            }
            if (!Schema::hasColumn('book_package_products', 'is_packed')) {
                $table->boolean('is_packed')->default(false)->after('qty')->comment('Flag untuk packing checklist');
            }
            if (!Schema::hasColumn('book_package_products', 'packed_at')) {
                $table->timestamp('packed_at')->nullable()->after('is_packed');
            }
            if (!Schema::hasColumn('book_package_products', 'packed_by')) {
                $table->foreignUuid('packed_by')->nullable()->after('packed_at')->comment('Officer yang melakukan packing');
            }
            
            $table->index('id_unit');
            $table->index('is_packed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_package_products', function (Blueprint $table) {
            $table->dropForeign(['id_unit']);
            $table->dropForeign(['packed_by']);
            $table->dropColumn(['id_unit', 'is_packed', 'packed_at', 'packed_by']);
        });
    }
};
