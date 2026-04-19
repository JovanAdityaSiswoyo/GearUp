<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('book_products') && Schema::hasColumn('book_products', 'id_courier')) {
            Schema::table('book_products', function (Blueprint $table) {
                try {
                    $table->dropForeign(['id_courier']);
                } catch (\Throwable $e) {
                    // Foreign key might not exist on some environments.
                }

                $table->dropColumn('id_courier');
            });
        }

        if (Schema::hasTable('books') && Schema::hasColumn('books', 'id_courier')) {
            Schema::table('books', function (Blueprint $table) {
                try {
                    $table->dropForeign(['id_courier']);
                } catch (\Throwable $e) {
                    // Foreign key might not exist on some environments.
                }

                $table->dropColumn('id_courier');
            });
        }

        if (Schema::hasTable('couriers')) {
            Schema::drop('couriers');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('couriers')) {
            Schema::create('couriers', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('password');
                $table->string('profile_photo')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('book_products') && !Schema::hasColumn('book_products', 'id_courier')) {
            Schema::table('book_products', function (Blueprint $table) {
                $table->foreignUuid('id_courier')->nullable()->constrained('couriers');
            });
        }

        if (Schema::hasTable('books') && !Schema::hasColumn('books', 'id_courier')) {
            Schema::table('books', function (Blueprint $table) {
                $table->foreignUuid('id_courier')->nullable()->constrained('couriers');
            });
        }
    }
};
