<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Product;

class BookProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'id_user' => User::factory(),
            'id_product' => Product::factory(),
            'book_code' => 'BP-' . strtoupper(Str::random(8)),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'checkin_appointment_start' => fake()->dateTimeBetween('now', '+30 days'),
            'checkout_appointment_end' => fake()->dateTimeBetween('+30 days', '+60 days'),
            'amount' => fake()->numberBetween(1, 10),
            'booker_name' => fake()->name(),
            'booker_email' => fake()->email(),
            'booker_telp' => fake()->numerify('08##########'),
        ];
    }
}
