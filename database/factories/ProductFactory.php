<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition()
    {
        return [
            'category_id' => Category::factory(),
            'name' => $this->faker->unique()->words(2, true),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 50, 500),
            'stock' => $this->faker->numberBetween(5, 50),
            'food_type' => $this->faker->randomElement(['veg', 'non-veg', 'drinks']),
            'course_type' => $this->faker->randomElement(['appetizer', 'main', 'dessert']),
            'is_active' => true,
        ];
    }
}
