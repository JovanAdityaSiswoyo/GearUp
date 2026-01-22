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
        // Drop existing foreign key and recreate with cascade delete for book_products
        Schema::table('book_products', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
        });
        
        Schema::table('book_products', function (Blueprint $table) {
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });

        // Drop existing foreign key and recreate with cascade delete for books
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
        });
        
        Schema::table('books', function (Blueprint $table) {
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });

        // Drop existing foreign key and recreate with cascade delete for user_info
        Schema::table('user_info', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
        });
        
        Schema::table('user_info', function (Blueprint $table) {
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });

        // Drop existing foreign key and recreate with cascade delete for detail_book_products
        Schema::table('detail_book_products', function (Blueprint $table) {
            $table->dropForeign(['id_book_product']);
        });
        
        Schema::table('detail_book_products', function (Blueprint $table) {
            $table->foreign('id_book_product')->references('id')->on('book_products')->onDelete('cascade');
        });

        // Drop existing foreign key and recreate with cascade delete for products in book_products
        Schema::table('book_products', function (Blueprint $table) {
            $table->dropForeign(['id_product']);
        });
        
        Schema::table('book_products', function (Blueprint $table) {
            $table->foreign('id_product')->references('id')->on('products')->onDelete('cascade');
        });

        // Drop existing foreign key and recreate with cascade delete for categories in products
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['id_category']);
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('id_category')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to restrict delete for book_products
        Schema::table('book_products', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
        });
        
        Schema::table('book_products', function (Blueprint $table) {
            $table->foreign('id_user')->references('id')->on('users');
        });

        // Revert to restrict delete for books
        Schema::table('books', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
        });
        
        Schema::table('books', function (Blueprint $table) {
            $table->foreign('id_user')->references('id')->on('users');
        });

        // Revert to restrict delete for user_info
        Schema::table('user_info', function (Blueprint $table) {
            $table->dropForeign(['id_user']);
        });
        
        Schema::table('user_info', function (Blueprint $table) {
            $table->foreign('id_user')->references('id')->on('users');
        });

        // Revert to restrict delete for detail_book_products
        Schema::table('detail_book_products', function (Blueprint $table) {
            $table->dropForeign(['id_book_product']);
        });
        
        Schema::table('detail_book_products', function (Blueprint $table) {
            $table->foreign('id_book_product')->references('id')->on('book_products');
        });

        // Revert products foreign key in book_products
        Schema::table('book_products', function (Blueprint $table) {
            $table->dropForeign(['id_product']);
        });
        
        Schema::table('book_products', function (Blueprint $table) {
            $table->foreign('id_product')->references('id')->on('products');
        });

        // Revert categories foreign key in products
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['id_category']);
        });
        
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('id_category')->references('id')->on('categories');
        });
    }
};
