<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
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
            'name' => fake()->name(),
            'description' => fake()->text(),
            'image' => fake()->imageUrl(),
            'price' => fake()->randomNumber(4),
            'weight' => fake()->randomNumber(4),
            // Stock / Quantity Product
            'stock' => fake()->randomNumber(2),

            // Random Range 1 - 4
            'category_id' => fake()->numberBetween(1, 4),
        ];
    }
}
