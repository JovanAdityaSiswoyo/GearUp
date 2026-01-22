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
            if (!Schema::hasColumn('packages', 'name')) {
                $table->string('name')->after('name_package');
            }
            if (!Schema::hasColumn('packages', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            if (!Schema::hasColumn('packages', 'duration_days')) {
                $table->integer('duration_days')->default(0)->after('description');
            }
            if (!Schema::hasColumn('packages', 'price')) {
                $table->decimal('price', 12, 2)->default(0)->after('duration_days');
            }
            if (!Schema::hasColumn('packages', 'discount')) {
                $table->decimal('discount', 5, 2)->default(0)->after('price');
            }
            if (!Schema::hasColumn('packages', 'image')) {
                $table->string('image')->nullable()->after('discount');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            if (Schema::hasColumn('packages', 'image')) {
                $table->dropColumn('image');
            }
            if (Schema::hasColumn('packages', 'discount')) {
                $table->dropColumn('discount');
            }
            if (Schema::hasColumn('packages', 'price')) {
                $table->dropColumn('price');
            }
            if (Schema::hasColumn('packages', 'duration_days')) {
                $table->dropColumn('duration_days');
            }
            if (Schema::hasColumn('packages', 'description')) {
                $table->dropColumn('description');
            }
            if (Schema::hasColumn('packages', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
