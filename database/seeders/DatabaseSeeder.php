<?php

namespace Database\Seeders;

use App\Models\User;
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
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

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
        ]);
    }
}
