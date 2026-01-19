<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'id_package' => \App\Models\Package::factory(),
            'id_user' => \App\Models\User::factory(),
            'book_code' => 'BK-' . strtoupper(\Illuminate\Support\Str::random(8)),
            'status' => fake()->randomElement(['pending', 'confirmed', 'completed', 'cancelled']),
            'checkin_appointment_start' => fake()->dateTimeBetween('now', '+30 days'),
            'checkout_appointment_end' => fake()->dateTimeBetween('+30 days', '+60 days'),
            'amount' => fake()->numberBetween(1, 10),
            'booker_name' => fake()->name(),
            'booker_email' => fake()->email(),
            'booker_telp' => fake()->numerify('62###########'),
        ];
    }
}
