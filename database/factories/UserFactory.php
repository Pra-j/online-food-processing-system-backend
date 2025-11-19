<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    private static $users = [
        [
            'name' => 'Prajwal Khatri',
            'email' => 'admin@admin.com',
            'role' => 'admin',
        ],
        [
            'name' => 'Saransh koirala',
            'email' => 'kitchen@kitchen.com',
            'role' => 'employee',
        ],
        [
            'name' => 'Michael Chen',
            'email' => 'michael.chen@company.com',
            'role' => 'employee',
        ],
        [
            'name' => 'Emily Rodriguez',
            'email' => 'emily.rodriguez@company.com',
            'role' => 'employee',
        ],
        [
            'name' => 'David Thompson',
            'email' => 'david.thompson@company.com',
            'role' => 'employee',
        ],
        [
            'name' => 'Jessica Williams',
            'email' => 'jessica.williams@company.com',
            'role' => 'employee',
        ],
        [
            'name' => 'Robert Martinez',
            'email' => 'robert.martinez@company.com',
            'role' => 'employee',
        ],
        [
            'name' => 'Amanda Foster',
            'email' => 'amanda.foster@company.com',
            'role' => 'employee',
        ],
        [
            'name' => 'James Parker',
            'email' => 'james.parker@company.com',
            'role' => 'employee',
        ],
        [
            'name' => 'Lisa Nguyen',
            'email' => 'lisa.nguyen@company.com',
            'role' => 'employee',
        ],
    ];

    private static $currentIndex = 0;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = self::$users[self::$currentIndex % count(self::$users)];
        self::$currentIndex++;

        return [
            'name' => $user['name'],
            'email' => $user['email'],
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => $user['role'],
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn(array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
