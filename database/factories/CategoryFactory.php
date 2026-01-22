<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $categories = [
            ['name' => 'Outdoor', 'desc' => 'Peralatan untuk aktivitas luar ruangan'],
            ['name' => 'Adventure', 'desc' => 'Perlengkapan petualangan dan hiking'],
            ['name' => 'Beach', 'desc' => 'Peralatan untuk aktivitas pantai'],
            ['name' => 'Mountain', 'desc' => 'Perlengkapan pendakian gunung'],
            ['name' => 'Water Sports', 'desc' => 'Peralatan olahraga air'],
            ['name' => 'Wildlife', 'desc' => 'Perlengkapan pengamatan satwa liar'],
            ['name' => 'Camping', 'desc' => 'Peralatan berkemah dan survival'],
            ['name' => 'Cycling', 'desc' => 'Perlengkapan bersepeda'],
        ];
        
        $selected = fake()->randomElement($categories);
        
        return [
            'id' => \Illuminate\Support\Str::uuid(),
            'categories' => $selected['name'],
            'description' => $selected['desc'],
        ];
    }
}
