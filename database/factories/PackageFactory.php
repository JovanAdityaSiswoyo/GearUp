<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory
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
            'name_package' => fake()->words(3, true),
            'start_publish' => now(),
            'end_publish' => now()->addMonth(),
            'price' => fake()->numberBetween(50000, 500000),
        ];
    }
}
