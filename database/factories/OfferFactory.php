<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfferFactory extends Factory
{
    private static $offers = [
        // Percentage Offers
        [
            'title' => 'Weekend Special - 20% Off Momos',
            'type' => 'product',
            'product_id' => 1, // Veg Steam Momo
            'offer_kind' => 'percentage',
            'value' => 20,
            'buy_quantity' => null,
            'get_quantity' => null,
            'start_date' => '2025-11-15',
            'end_date' => '2025-12-15',
            'max_usage' => 100,
            'is_active' => true,
        ],
        [
            'title' => 'Pizza Lovers Deal - 15% Off',
            'type' => 'product',
            'product_id' => 4, // Margherita Pizza
            'offer_kind' => 'percentage',
            'value' => 15,
            'buy_quantity' => null,
            'get_quantity' => null,
            'start_date' => '2025-11-10',
            'end_date' => '2025-12-10',
            'max_usage' => 50,
            'is_active' => true,
        ],
        [
            'title' => 'Burger Bonanza - 25% Off',
            'type' => 'product',
            'product_id' => 8, // Classic Chicken Burger
            'offer_kind' => 'percentage',
            'value' => 25,
            'buy_quantity' => null,
            'get_quantity' => null,
            'start_date' => '2025-11-18',
            'end_date' => '2025-12-18',
            'max_usage' => 75,
            'is_active' => true,
        ],
        [
            'title' => 'Noodles Night - 10% Discount',
            'type' => 'product',
            'product_id' => 11, // Veg Chowmein
            'offer_kind' => 'percentage',
            'value' => 10,
            'buy_quantity' => null,
            'get_quantity' => null,
            'start_date' => '2025-11-12',
            'end_date' => '2025-12-12',
            'max_usage' => 60,
            'is_active' => true,
        ],
        [
            'title' => 'Fried Rice Festival - 30% Off',
            'type' => 'product',
            'product_id' => 15, // Veg Fried Rice
            'offer_kind' => 'percentage',
            'value' => 30,
            'buy_quantity' => null,
            'get_quantity' => null,
            'start_date' => '2025-11-16',
            'end_date' => '2025-12-16',
            'max_usage' => 80,
            'is_active' => true,
        ],

        // Buy X Get Y Offers
        [
            'title' => 'Buy 2 Get 1 Free - Chicken Momos',
            'type' => 'product',
            'product_id' => 2, // Chicken Steam Momo
            'offer_kind' => 'buy_x_get_y',
            'value' => null,
            'buy_quantity' => 2,
            'get_quantity' => 1,
            'start_date' => '2025-11-14',
            'end_date' => '2025-12-14',
            'max_usage' => 40,
            'is_active' => true,
        ],
        [
            'title' => 'Buy 1 Get 1 Free - BBQ Pizza',
            'type' => 'product',
            'product_id' => 5, // Chicken BBQ Pizza
            'offer_kind' => 'buy_x_get_y',
            'value' => null,
            'buy_quantity' => 1,
            'get_quantity' => 1,
            'start_date' => '2025-11-17',
            'end_date' => '2025-12-17',
            'max_usage' => 30,
            'is_active' => true,
        ],
        [
            'title' => 'Buy 3 Get 1 Free - Veg Burger',
            'type' => 'product',
            'product_id' => 9, // Veg Cheese Burger
            'offer_kind' => 'buy_x_get_y',
            'value' => null,
            'buy_quantity' => 3,
            'get_quantity' => 1,
            'start_date' => '2025-11-13',
            'end_date' => '2025-12-13',
            'max_usage' => 50,
            'is_active' => true,
        ],
        [
            'title' => 'Buy 2 Get 1 Free - Chicken Chowmein',
            'type' => 'product',
            'product_id' => 12, // Chicken Chowmein
            'offer_kind' => 'buy_x_get_y',
            'value' => null,
            'buy_quantity' => 2,
            'get_quantity' => 1,
            'start_date' => '2025-11-11',
            'end_date' => '2025-12-11',
            'max_usage' => 70,
            'is_active' => true,
        ],
        [
            'title' => 'Buy 1 Get 1 Free - Sandwiches',
            'type' => 'product',
            'product_id' => 18, // Chicken Sandwich
            'offer_kind' => 'buy_x_get_y',
            'value' => null,
            'buy_quantity' => 1,
            'get_quantity' => 1,
            'start_date' => '2025-11-19',
            'end_date' => '2025-12-19',
            'max_usage' => 90,
            'is_active' => true,
        ],
    ];

    private static $currentIndex = 0;

    public function definition(): array
    {
        $offer = self::$offers[self::$currentIndex % count(self::$offers)];
        self::$currentIndex++;

        return [
            'title' => $offer['title'],
            'type' => $offer['type'],
            'product_id' => $offer['product_id'],
            'offer_kind' => $offer['offer_kind'],
            'value' => $offer['value'],
            'buy_quantity' => $offer['buy_quantity'],
            'get_quantity' => $offer['get_quantity'],
            'start_date' => $offer['start_date'],
            'end_date' => $offer['end_date'],
            'max_usage' => $offer['max_usage'],
            'is_active' => $offer['is_active'],
        ];
    }
}
