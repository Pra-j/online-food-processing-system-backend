<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;


class EmployeeFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'position' => $this->faker->randomElement(['cook', 'waiter', 'cashier', 'manager']),
        ];
    }
}
