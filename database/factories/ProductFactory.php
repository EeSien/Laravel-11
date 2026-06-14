<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id'    => Category::factory(),
            'name'           => fake()->words(3, true),
            'sku'            => strtoupper(fake()->unique()->bothify('??-####-??')),
            'description'    => fake()->paragraph(),
            'price'          => fake()->randomFloat(2, 1, 999),
            'stock_quantity' => fake()->numberBetween(0, 200),
            'is_active'      => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function lowStock(): static
    {
        return $this->state(fn () => ['stock_quantity' => fake()->numberBetween(0, 10)]);
    }
}
