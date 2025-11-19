<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    private static $employees = [
        [
            'user_id' => 3, // Michael Chen
            'position' => 'cook',
        ],
        [
            'user_id' => 4, // Emily Rodriguez
            'position' => 'waiter',
        ],
        [
            'user_id' => 6, // Jessica Williams
            'position' => 'waiter',
        ],
        [
            'user_id' => 7, // Robert Martinez
            'position' => 'cashier',
        ],
        [
            'user_id' => 9, // James Parker
            'position' => 'cook',
        ],
    ];

    private static $currentIndex = 0;

    public function definition()
    {
        $employee = self::$employees[self::$currentIndex % count(self::$employees)];
        self::$currentIndex++;

        return [
            'user_id' => $employee['user_id'],
            'position' => $employee['position'],
        ];
    }
}
