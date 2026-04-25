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
                if (! Schema::hasColumn($tableName, 'issue_condition')) {
                    $table->string('issue_condition', 50)->nullable()->after('issue_notes');
                }

                if (! Schema::hasColumn($tableName, 'fine_percentage')) {
                    $table->unsignedTinyInteger('fine_percentage')->nullable()->after('issue_condition');
                }

                if (! Schema::hasColumn($tableName, 'fine_amount')) {
                    $table->integer('fine_amount')->nullable()->after('fine_percentage');
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
                foreach (['issue_condition', 'fine_percentage', 'fine_amount'] as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }
    }
};
