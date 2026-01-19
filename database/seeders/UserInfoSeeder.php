<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserInfoSeeder extends Seeder
{
    public function run(): void
    {
        // Create user_info for all existing users
        User::all()->each(function ($user) {
            $user->userInfo()->create([
                'id' => Str::uuid(),
                'phone' => fake()->numerify('08##########'),
                'birthday' => fake()->date(),
            ]);
        });
    }
}
