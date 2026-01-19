<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
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
            'id_admins' => \App\Models\Admin::factory(),
            'id_category' => \App\Models\Category::factory(),
            'name' => fake()->words(3, true),
            'desc' => fake()->sentence(),
            'status' => fake()->randomElement(['active', 'inactive', 'coming']),
            'price' => fake()->numberBetween(50000, 500000),
        ];
    }
}
