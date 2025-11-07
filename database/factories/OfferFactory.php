<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    public function definition(): array
    {
        $type = $this->faker->randomElement(['global', 'product']);
        $offerKind = $this->faker->randomElement(['percentage', 'fixed_amount', 'buy_x_get_y']);

        $value = null;
        $buyQty = null;
        $getQty = null;

        if ($offerKind === 'percentage') {
            $value = $this->faker->numberBetween(5, 50);
        } elseif ($offerKind === 'fixed_amount') {
            $value = $this->faker->randomFloat(2, 20, 200);
        } elseif ($offerKind === 'buy_x_get_y') {
            $buyQty = $this->faker->numberBetween(1, 3);
            $getQty = $this->faker->numberBetween(1, 2);
        }

        return [
            'title' => $this->faker->sentence(3),
            'type' => $type,
            'product_id' => $type === 'product' ? Product::inRandomOrder()->first()?->id : null,
            'offer_kind' => $offerKind,
            'value' => $value,
            'buy_quantity' => $buyQty,
            'get_quantity' => $getQty,
            'start_date' => $this->faker->dateTimeBetween('-10 days', 'now'),
            'end_date' => $this->faker->dateTimeBetween('now', '+15 days'),
            'max_usage' => $this->faker->numberBetween(0, 100),
            'is_active' => $this->faker->boolean(80), // 80% chance active
        ];
    }
}
