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
            // Check and add missing columns
            if (!Schema::hasColumn('packages', 'name')) {
                $table->string('name')->nullable()->after('name_package');
            }
            
            if (!Schema::hasColumn('packages', 'description')) {
                $table->text('description')->nullable()->after('name');
            }
            
            if (!Schema::hasColumn('packages', 'duration_days')) {
                $table->integer('duration_days')->default(0)->after('description');
            }
            
            if (!Schema::hasColumn('packages', 'price')) {
                $table->bigInteger('price')->default(0)->after('duration_days');
            }
            
            if (!Schema::hasColumn('packages', 'upsell')) {
                $table->bigInteger('upsell')->default(0)->after('price');
            }
            
            if (!Schema::hasColumn('packages', 'start_publish')) {
                $table->dateTime('start_publish')->nullable()->after('upsell');
            }
            
            if (!Schema::hasColumn('packages', 'end_publish')) {
                $table->dateTime('end_publish')->nullable()->after('start_publish');
            }

            if (!Schema::hasColumn('packages', 'rental_duration')) {
                $table->integer('rental_duration')->default(1)->after('duration_days');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not reversible since it only adds if not exists
    }
};
