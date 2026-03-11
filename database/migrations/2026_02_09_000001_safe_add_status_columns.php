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
        // Add status columns to book_products table if not exists
        Schema::table('book_products', function (Blueprint $table) {
            if (!Schema::hasColumn('book_products', 'item_status')) {
                $table->string('item_status')->default('Available')->after('status');
            }
            
            if (!Schema::hasColumn('book_products', 'order_status')) {
                $table->string('order_status')->default('Awaiting Validation')->after('item_status');
            }
            
            if (!Schema::hasColumn('book_products', 'id_courier')) {
                $table->foreignUuid('id_courier')->nullable()->constrained('couriers')->after('order_status');
            }
            
            if (!Schema::hasColumn('book_products', 'delivery_at')) {
                $table->dateTime('delivery_at')->nullable()->after('id_courier');
            }
            
            if (!Schema::hasColumn('book_products', 'returned_at')) {
                $table->dateTime('returned_at')->nullable()->after('delivery_at');
            }

            // Add indexes if not exists
            if (!Schema::hasIndex('book_products', 'idx_item_status')) {
                $table->index('item_status', 'idx_item_status');
            }
            
            if (!Schema::hasIndex('book_products', 'idx_order_status')) {
                $table->index('order_status', 'idx_order_status');
            }
        });

        // Add status columns to books table if not exists
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'item_status')) {
                $table->string('item_status')->default('Available')->after('status');
            }
            
            if (!Schema::hasColumn('books', 'order_status')) {
                $table->string('order_status')->default('Awaiting Validation')->after('item_status');
            }
            
            if (!Schema::hasColumn('books', 'id_courier')) {
                $table->foreignUuid('id_courier')->nullable()->constrained('couriers')->after('order_status');
            }
            
            if (!Schema::hasColumn('books', 'delivery_at')) {
                $table->dateTime('delivery_at')->nullable()->after('id_courier');
            }
            
            if (!Schema::hasColumn('books', 'returned_at')) {
                $table->dateTime('returned_at')->nullable()->after('delivery_at');
            }

            // Add indexes if not exists
            if (!Schema::hasIndex('books', 'idx_item_status')) {
                $table->index('item_status', 'idx_item_status');
            }
            
            if (!Schema::hasIndex('books', 'idx_order_status')) {
                $table->index('order_status', 'idx_order_status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('book_products', function (Blueprint $table) {
            if (Schema::hasColumn('book_products', 'id_courier')) {
                $table->dropForeign(['id_courier']);
            }
            if (Schema::hasIndex('book_products', 'idx_item_status')) {
                $table->dropIndex('idx_item_status');
            }
            if (Schema::hasIndex('book_products', 'idx_order_status')) {
                $table->dropIndex('idx_order_status');
            }
            $table->dropColumnIfExists(['item_status', 'order_status', 'id_courier', 'delivery_at', 'returned_at']);
        });

        Schema::table('books', function (Blueprint $table) {
            if (Schema::hasColumn('books', 'id_courier')) {
                $table->dropForeign(['id_courier']);
            }
            if (Schema::hasIndex('books', 'idx_item_status')) {
                $table->dropIndex('idx_item_status');
            }
            if (Schema::hasIndex('books', 'idx_order_status')) {
                $table->dropIndex('idx_order_status');
            }
            $table->dropColumnIfExists(['item_status', 'order_status', 'id_courier', 'delivery_at', 'returned_at']);
        });
    }
};
