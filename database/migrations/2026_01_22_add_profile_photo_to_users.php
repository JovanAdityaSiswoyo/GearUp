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
        Schema::table('users', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('phone');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('password');
        });

        Schema::table('officers', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('password');
        });

        Schema::table('couriers', function (Blueprint $table) {
            $table->string('profile_photo')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
        });

        Schema::table('admins', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
        });

        Schema::table('officers', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
        });

        Schema::table('couriers', function (Blueprint $table) {
            $table->dropColumn('profile_photo');
        });
    }
};
