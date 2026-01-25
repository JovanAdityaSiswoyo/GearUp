<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('book_package_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('id_book'); // relasi ke tabel books (booking package)
            $table->uuid('id_product'); // relasi ke tabel products
            $table->integer('qty')->default(1);
            $table->timestamps();

            $table->foreign('id_book')->references('id')->on('books')->onDelete('cascade');
            $table->foreign('id_product')->references('id')->on('products')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_package_products');
    }
};
