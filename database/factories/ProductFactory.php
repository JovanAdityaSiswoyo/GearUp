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
        // Get first admin or create one if needed
        $admin = \App\Models\Admin::first();
        if (!$admin) {
            $admin = \App\Models\Admin::create([
                'name' => 'Default Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }
        
        // Get first category or create one if needed
        $category = \App\Models\Category::first();
        if (!$category) {
            $category = \App\Models\Category::create([
                'categories' => 'Uncategorized',
                'description' => 'Default category',
            ]);
        }
        
        // Get random brand if exists
        $brand = \App\Models\Brand::inRandomOrder()->first();

        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'id_admins' => $admin->id,
            'id_category' => $category->id,
            'name' => fake()->words(3, true),
            'desc' => fake()->sentence(),
            'status' => fake()->randomElement(['active', 'inactive', 'coming']),
            'price' => fake()->numberBetween(50000, 500000),
            'price_per_day' => fake()->numberBetween(50000, 500000),
            'stock' => fake()->numberBetween(1, 50),
            'description' => fake()->paragraph(),
            'brand_id' => $brand?->id ?? null,
            'image' => null,
        ];
    }
}
