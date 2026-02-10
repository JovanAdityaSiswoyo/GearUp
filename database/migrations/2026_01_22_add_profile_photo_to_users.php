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
            if (!Schema::hasColumn('users', 'profile_photo')) {
                $table->string('profile_photo')->nullable()->after('phone');
            }
        });

        if (Schema::hasTable('admins')) {
            Schema::table('admins', function (Blueprint $table) {
                if (!Schema::hasColumn('admins', 'profile_photo')) {
                    $table->string('profile_photo')->nullable()->after('password');
                }
            });
        }

        if (Schema::hasTable('officers')) {
            Schema::table('officers', function (Blueprint $table) {
                if (!Schema::hasColumn('officers', 'profile_photo')) {
                    $table->string('profile_photo')->nullable()->after('password');
                }
            });
        }

        if (Schema::hasTable('couriers')) {
            Schema::table('couriers', function (Blueprint $table) {
                if (!Schema::hasColumn('couriers', 'profile_photo')) {
                    $table->string('profile_photo')->nullable()->after('address');
                }
            });
        }
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
