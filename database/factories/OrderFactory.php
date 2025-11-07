<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition()
    {
        return [
            'table_number' => $this->faker->numberBetween(1, 20),
            'status' => $this->faker->randomElement(['queued', 'processing', 'ready', 'completed']),
            'total_amount' => $this->faker->randomFloat(2, 100, 2000),
        ];
    }
}
