<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create user role if not exists
        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        
        // Create users and assign role
        User::factory(10)->create()->each(function ($user) use ($userRole) {
            $user->assignRole($userRole);
        });
    }
}
