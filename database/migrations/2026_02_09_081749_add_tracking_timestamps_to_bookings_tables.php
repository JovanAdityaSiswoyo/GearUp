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
        Schema::table('book_products', function (Blueprint $table) {
            if (!Schema::hasColumn('book_products', 'picked_up_at')) {
                $table->timestamp('picked_up_at')->nullable();
            }
            if (!Schema::hasColumn('book_products', 'return_started_at')) {
                $table->timestamp('return_started_at')->nullable();
            }
            if (!Schema::hasColumn('book_products', 'inspection_started_at')) {
                $table->timestamp('inspection_started_at')->nullable();
            }
            if (!Schema::hasColumn('book_products', 'overdue_since')) {
                $table->timestamp('overdue_since')->nullable();
            }
        });

        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'picked_up_at')) {
                $table->timestamp('picked_up_at')->nullable();
            }
            if (!Schema::hasColumn('books', 'return_started_at')) {
                $table->timestamp('return_started_at')->nullable();
            }
            if (!Schema::hasColumn('books', 'inspection_started_at')) {
                $table->timestamp('inspection_started_at')->nullable();
            }
            if (!Schema::hasColumn('books', 'overdue_since')) {
                $table->timestamp('overdue_since')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_products', function (Blueprint $table) {
            $table->dropColumn(['picked_up_at', 'return_started_at', 'inspection_started_at', 'overdue_since']);
        });

        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['picked_up_at', 'return_started_at', 'inspection_started_at', 'overdue_since']);
        });
    }
};
