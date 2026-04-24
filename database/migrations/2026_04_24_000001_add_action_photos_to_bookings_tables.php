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
        foreach (['book_products', 'books'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'pickup_photo')) {
                    $table->string('pickup_photo')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'return_photo')) {
                    $table->string('return_photo')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'issue_photo')) {
                    $table->string('issue_photo')->nullable();
                }

                if (!Schema::hasColumn($tableName, 'issue_notes')) {
                    $table->text('issue_notes')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['book_products', 'books'] as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $columns = [];

                foreach (['pickup_photo', 'return_photo', 'issue_photo', 'issue_notes'] as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $columns[] = $column;
                    }
                }

                if ($columns !== []) {
                    $table->dropColumn($columns);
                }
            });
        }
    }
};
