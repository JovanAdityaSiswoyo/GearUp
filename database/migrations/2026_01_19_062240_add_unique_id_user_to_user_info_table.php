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
        Schema::table('user_info', function (Blueprint $table) {
            $table->unique('id_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_info', function (Blueprint $table) {
            // Drop the foreign key first if it exists
            try {
                $table->dropForeign(['id_user']);
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            $table->dropUnique(['id_user']);
        });
    }
};
