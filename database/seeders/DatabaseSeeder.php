<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Database\Seeders\PaymentSeeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create test user
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
            ]
        );

        // Call all seeders in order
        $this->call([
            UserSeeder::class,
            AdminSeeder::class,
            OfficerSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            PackageSeeder::class,
            BookSeeder::class,
            UserInfoSeeder::class,
            DetailBookSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
