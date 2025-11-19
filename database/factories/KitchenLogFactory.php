<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class KitchenLogFactory extends Factory
{
    private static $kitchenLogs = [
        // Order 1 (Table 5 - Completed)
        [
            'order_id' => 1,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'queued',
            'updated_at' => '2025-11-19 10:00:00',
        ],
        [
            'order_id' => 1,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'processing',
            'updated_at' => '2025-11-19 10:05:00',
        ],
        [
            'order_id' => 1,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'ready',
            'updated_at' => '2025-11-19 10:20:00',
        ],

        // Order 2 (Table 12 - Ready)
        [
            'order_id' => 2,
            'employee_id' => 5, // James Parker (cook)
            'status' => 'queued',
            'updated_at' => '2025-11-19 11:00:00',
        ],
        [
            'order_id' => 2,
            'employee_id' => 5, // James Parker (cook)
            'status' => 'processing',
            'updated_at' => '2025-11-19 11:10:00',
        ],
        [
            'order_id' => 2,
            'employee_id' => 5, // James Parker (cook)
            'status' => 'ready',
            'updated_at' => '2025-11-19 11:35:00',
        ],

        // Order 3 (Table 3 - Processing)
        [
            'order_id' => 3,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'queued',
            'updated_at' => '2025-11-19 12:00:00',
        ],
        [
            'order_id' => 3,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'processing',
            'updated_at' => '2025-11-19 12:08:00',
        ],

        // Order 4 (Table 8 - Queued)
        [
            'order_id' => 4,
            'employee_id' => 5, // James Parker (cook)
            'status' => 'queued',
            'updated_at' => '2025-11-19 12:30:00',
        ],

        // Order 5 (Table 15 - Completed)
        [
            'order_id' => 5,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'queued',
            'updated_at' => '2025-11-19 09:00:00',
        ],
        [
            'order_id' => 5,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'processing',
            'updated_at' => '2025-11-19 09:15:00',
        ],
        [
            'order_id' => 5,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'ready',
            'updated_at' => '2025-11-19 09:45:00',
        ],

        // Order 6 (Table 7 - Processing)
        [
            'order_id' => 6,
            'employee_id' => 5, // James Parker (cook)
            'status' => 'queued',
            'updated_at' => '2025-11-19 11:45:00',
        ],
        [
            'order_id' => 6,
            'employee_id' => 5, // James Parker (cook)
            'status' => 'processing',
            'updated_at' => '2025-11-19 11:55:00',
        ],

        // Order 7 (Table 2 - Ready)
        [
            'order_id' => 7,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'queued',
            'updated_at' => '2025-11-19 10:30:00',
        ],
        [
            'order_id' => 7,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'processing',
            'updated_at' => '2025-11-19 10:40:00',
        ],
        [
            'order_id' => 7,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'ready',
            'updated_at' => '2025-11-19 11:05:00',
        ],

        // Order 8 (Table 10 - Queued)
        [
            'order_id' => 8,
            'employee_id' => 5, // James Parker (cook)
            'status' => 'queued',
            'updated_at' => '2025-11-19 12:45:00',
        ],

        // Order 9 (Table 18 - Completed)
        [
            'order_id' => 9,
            'employee_id' => 5, // James Parker (cook)
            'status' => 'queued',
            'updated_at' => '2025-11-19 08:30:00',
        ],
        [
            'order_id' => 9,
            'employee_id' => 5, // James Parker (cook)
            'status' => 'processing',
            'updated_at' => '2025-11-19 08:45:00',
        ],
        [
            'order_id' => 9,
            'employee_id' => 5, // James Parker (cook)
            'status' => 'ready',
            'updated_at' => '2025-11-19 09:15:00',
        ],

        // Order 10 (Table 6 - Processing)
        [
            'order_id' => 10,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'queued',
            'updated_at' => '2025-11-19 12:15:00',
        ],
        [
            'order_id' => 10,
            'employee_id' => 1, // Michael Chen (cook)
            'status' => 'processing',
            'updated_at' => '2025-11-19 12:25:00',
        ],
    ];

    private static $currentIndex = 0;

    public function definition()
    {
        $log = self::$kitchenLogs[self::$currentIndex % count(self::$kitchenLogs)];
        self::$currentIndex++;

        return [
            'order_id' => $log['order_id'],
            'employee_id' => $log['employee_id'],
            'status' => $log['status'],
            'updated_at' => $log['updated_at'],
        ];
    }
}
