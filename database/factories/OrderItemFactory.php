<?php

namespace Database\Factories;

use App\Models\Offer;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    private static $orderItems = [
        // Order 1 (Table 5 - Completed) - Total: 670.00
        [
            'order_id' => 1,
            'product_id' => 4, // Margherita Pizza
            'offer_id' => 2, // 15% off
            'quantity' => 1,
            'unit_price' => 450.00,
            'total_product_price' => 450.00,
            'discount' => 67.50, // 15% discount
            'grand_total' => 382.50,
        ],
        [
            'order_id' => 1,
            'product_id' => 1, // Veg Steam Momo
            'offer_id' => 1, // 20% off
            'quantity' => 2,
            'unit_price' => 120.00,
            'total_product_price' => 240.00,
            'discount' => 48.00, // 20% discount
            'grand_total' => 192.00,
        ],
        [
            'order_id' => 1,
            'product_id' => 18, // Chicken Sandwich
            'offer_id' => null,
            'quantity' => 1,
            'unit_price' => 150.00,
            'total_product_price' => 150.00,
            'discount' => 0,
            'grand_total' => 150.00,
        ],

        // Order 2 (Table 12 - Ready) - Total: 890.00
        [
            'order_id' => 2,
            'product_id' => 5, // Chicken BBQ Pizza
            'offer_id' => 7, // Buy 1 Get 1
            'quantity' => 2,
            'unit_price' => 550.00,
            'total_product_price' => 1100.00,
            'discount' => 550.00, // Free pizza
            'grand_total' => 550.00,
        ],
        [
            'order_id' => 2,
            'product_id' => 12, // Chicken Chowmein
            'offer_id' => 9, // Buy 2 Get 1
            'quantity' => 3,
            'unit_price' => 160.00,
            'total_product_price' => 480.00,
            'discount' => 160.00, // Free chowmein
            'grand_total' => 320.00,
        ],
        [
            'order_id' => 2,
            'product_id' => 20, // Cheese Grilled Sandwich
            'offer_id' => null,
            'quantity' => 1,
            'unit_price' => 130.00,
            'total_product_price' => 130.00,
            'discount' => 0,
            'grand_total' => 130.00,
        ],

        // Order 3 (Table 3 - Processing) - Total: 450.00
        [
            'order_id' => 3,
            'product_id' => 8, // Classic Chicken Burger
            'offer_id' => 3, // 25% off
            'quantity' => 2,
            'unit_price' => 220.00,
            'total_product_price' => 440.00,
            'discount' => 110.00, // 25% discount
            'grand_total' => 330.00,
        ],
        [
            'order_id' => 3,
            'product_id' => 11, // Veg Chowmein
            'offer_id' => null,
            'quantity' => 1,
            'unit_price' => 130.00,
            'total_product_price' => 130.00,
            'discount' => 0,
            'grand_total' => 130.00,
        ],

        // Order 4 (Table 8 - Queued) - Total: 320.00
        [
            'order_id' => 4,
            'product_id' => 2, // Chicken Steam Momo
            'offer_id' => 6, // Buy 2 Get 1
            'quantity' => 3,
            'unit_price' => 150.00,
            'total_product_price' => 450.00,
            'discount' => 150.00, // Free momo
            'grand_total' => 300.00,
        ],
        [
            'order_id' => 4,
            'product_id' => 17, // Egg Fried Rice
            'offer_id' => null,
            'quantity' => 1,
            'unit_price' => 120.00,
            'total_product_price' => 120.00,
            'discount' => 0,
            'grand_total' => 120.00,
        ],

        // Order 5 (Table 15 - Completed) - Total: 1250.00
        [
            'order_id' => 5,
            'product_id' => 7, // Pepperoni Pizza
            'offer_id' => null,
            'quantity' => 2,
            'unit_price' => 520.00,
            'total_product_price' => 1040.00,
            'discount' => 0,
            'grand_total' => 1040.00,
        ],
        [
            'order_id' => 5,
            'product_id' => 3, // Buff Fried Momo
            'offer_id' => null,
            'quantity' => 1,
            'unit_price' => 180.00,
            'total_product_price' => 180.00,
            'discount' => 0,
            'grand_total' => 180.00,
        ],
        [
            'order_id' => 5,
            'product_id' => 13, // Thukpa
            'offer_id' => null,
            'quantity' => 1,
            'unit_price' => 140.00,
            'total_product_price' => 140.00,
            'discount' => 0,
            'grand_total' => 140.00,
        ],

        // Order 6 (Table 7 - Processing) - Total: 540.00
        [
            'order_id' => 6,
            'product_id' => 15, // Veg Fried Rice
            'offer_id' => 5, // 30% off
            'quantity' => 2,
            'unit_price' => 140.00,
            'total_product_price' => 280.00,
            'discount' => 84.00, // 30% discount
            'grand_total' => 196.00,
        ],
        [
            'order_id' => 6,
            'product_id' => 16, // Chicken Fried Rice
            'offer_id' => null,
            'quantity' => 2,
            'unit_price' => 170.00,
            'total_product_price' => 340.00,
            'discount' => 0,
            'grand_total' => 340.00,
        ],
        [
            'order_id' => 6,
            'product_id' => 19, // Veg Club Sandwich
            'offer_id' => null,
            'quantity' => 1,
            'unit_price' => 180.00,
            'total_product_price' => 180.00,
            'discount' => 0,
            'grand_total' => 180.00,
        ],

        // Order 7 (Table 2 - Ready) - Total: 380.00
        [
            'order_id' => 7,
            'product_id' => 9, // Veg Cheese Burger
            'offer_id' => 8, // Buy 3 Get 1
            'quantity' => 4,
            'unit_price' => 180.00,
            'total_product_price' => 720.00,
            'discount' => 180.00, // Free burger
            'grand_total' => 540.00,
        ],
        [
            'order_id' => 7,
            'product_id' => 11, // Veg Chowmein
            'offer_id' => 4, // 10% off
            'quantity' => 1,
            'unit_price' => 130.00,
            'total_product_price' => 130.00,
            'discount' => 13.00, // 10% discount
            'grand_total' => 117.00,
        ],

        // Order 8 (Table 10 - Queued) - Total: 720.00
        [
            'order_id' => 8,
            'product_id' => 6, // Veggie Supreme Pizza
            'offer_id' => null,
            'quantity' => 1,
            'unit_price' => 480.00,
            'total_product_price' => 480.00,
            'discount' => 0,
            'grand_total' => 480.00,
        ],
        [
            'order_id' => 8,
            'product_id' => 10, // Buff Burger
            'offer_id' => null,
            'quantity' => 1,
            'unit_price' => 250.00,
            'total_product_price' => 250.00,
            'discount' => 0,
            'grand_total' => 250.00,
        ],
        [
            'order_id' => 8,
            'product_id' => 1, // Veg Steam Momo
            'offer_id' => null,
            'quantity' => 1,
            'unit_price' => 120.00,
            'total_product_price' => 120.00,
            'discount' => 0,
            'grand_total' => 120.00,
        ],

        // Order 9 (Table 18 - Completed) - Total: 960.00
        [
            'order_id' => 9,
            'product_id' => 14, // Buff Chowmein
            'offer_id' => null,
            'quantity' => 3,
            'unit_price' => 180.00,
            'total_product_price' => 540.00,
            'discount' => 0,
            'grand_total' => 540.00,
        ],
        [
            'order_id' => 9,
            'product_id' => 4, // Margherita Pizza
            'offer_id' => 2, // 15% off
            'quantity' => 1,
            'unit_price' => 450.00,
            'total_product_price' => 450.00,
            'discount' => 67.50, // 15% discount
            'grand_total' => 382.50,
        ],
        [
            'order_id' => 9,
            'product_id' => 18, // Chicken Sandwich
            'offer_id' => 10, // Buy 1 Get 1
            'quantity' => 2,
            'unit_price' => 150.00,
            'total_product_price' => 300.00,
            'discount' => 150.00, // Free sandwich
            'grand_total' => 150.00,
        ],

        // Order 10 (Table 6 - Processing) - Total: 510.00
        [
            'order_id' => 10,
            'product_id' => 12, // Chicken Chowmein
            'offer_id' => null,
            'quantity' => 2,
            'unit_price' => 160.00,
            'total_product_price' => 320.00,
            'discount' => 0,
            'grand_total' => 320.00,
        ],
        [
            'order_id' => 10,
            'product_id' => 16, // Chicken Fried Rice
            'offer_id' => null,
            'quantity' => 1,
            'unit_price' => 170.00,
            'total_product_price' => 170.00,
            'discount' => 0,
            'grand_total' => 170.00,
        ],
        [
            'order_id' => 10,
            'product_id' => 2, // Chicken Steam Momo
            'offer_id' => null,
            'quantity' => 1,
            'unit_price' => 150.00,
            'total_product_price' => 150.00,
            'discount' => 0,
            'grand_total' => 150.00,
        ],
    ];

    private static $currentIndex = 0;

    public function definition()
    {
        $orderItem = self::$orderItems[self::$currentIndex % count(self::$orderItems)];
        self::$currentIndex++;

        return [
            'order_id' => $orderItem['order_id'],
            'product_id' => $orderItem['product_id'],
            'offer_id' => $orderItem['offer_id'],
            'quantity' => $orderItem['quantity'],
            'unit_price' => $orderItem['unit_price'],
            'total_product_price' => $orderItem['total_product_price'],
            'discount' => $orderItem['discount'],
            'grand_total' => $orderItem['grand_total'],
        ];
    }
}
