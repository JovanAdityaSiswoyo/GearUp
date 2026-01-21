<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use App\Models\Officer;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Super Admin
        $admin = Admin::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
        ]);
        $admin->assignRole('super-admin');
        $this->command->info('Admin created: ' . $admin->email);

        // Create Officer
        $officer = Officer::create([
            'name' => 'Officer One',
            'email' => 'officer@example.com',
            'password' => Hash::make('password123'),
        ]);
        $officer->assignRole('officer');
        $this->command->info('Officer created: ' . $officer->email);

        // Create Regular User
        $user = User::create([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password123'),
        ]);
        $user->assignRole('user');
        $this->command->info('User created: ' . $user->email);
    }
}
