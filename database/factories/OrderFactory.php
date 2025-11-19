<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    private static $orders = [
        [
            'table_number' => 5,
            'status' => 'completed',
            'total_amount' => 670.00,
        ],
        [
            'table_number' => 12,
            'status' => 'ready',
            'total_amount' => 890.00,
        ],
        [
            'table_number' => 3,
            'status' => 'processing',
            'total_amount' => 450.00,
        ],
        [
            'table_number' => 8,
            'status' => 'queued',
            'total_amount' => 320.00,
        ],
        [
            'table_number' => 15,
            'status' => 'completed',
            'total_amount' => 1250.00,
        ],
        [
            'table_number' => 7,
            'status' => 'processing',
            'total_amount' => 540.00,
        ],
        [
            'table_number' => 2,
            'status' => 'ready',
            'total_amount' => 380.00,
        ],
        [
            'table_number' => 10,
            'status' => 'queued',
            'total_amount' => 720.00,
        ],
        [
            'table_number' => 18,
            'status' => 'completed',
            'total_amount' => 960.00,
        ],
        [
            'table_number' => 6,
            'status' => 'processing',
            'total_amount' => 510.00,
        ],
    ];

    private static $currentIndex = 0;

    public function definition()
    {
        $order = self::$orders[self::$currentIndex % count(self::$orders)];
        self::$currentIndex++;

        return [
            'table_number' => $order['table_number'],
            'status' => $order['status'],
            'total_amount' => $order['total_amount'],
        ];
    }
}
