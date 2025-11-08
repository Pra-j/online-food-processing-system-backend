<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition()
    {
        $quantity = $this->faker->numberBetween(1, 5);
        $unitPrice = $this->faker->randomFloat(2, 50, 300);
        $totalProductPrice = $quantity * $unitPrice;
        $hasOffer = $this->faker->boolean(30);
        $discount = $hasOffer ? $this->faker->randomFloat(2, 5, 50) : 0;
        $grandTotal = max(0, $totalProductPrice - $discount);

        return [
            'order_id' => Order::factory(),
            'product_id' => Product::factory(),
            'offer_id' => $hasOffer ? Offer::factory() : null,

            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total_product_price' => $totalProductPrice,
            'discount' => $discount,
            'grand_total' => $grandTotal,
        ];
    }
}
