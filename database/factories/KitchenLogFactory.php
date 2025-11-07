<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class KitchenLogFactory extends Factory
{
    public function definition()
    {
        return [
            'order_id' => Order::factory(),
            'employee_id' => Employee::factory(),
            'status' => $this->faker->randomElement(['queued', 'processing', 'ready']),
            'updated_at' => now(),
        ];
    }
}
