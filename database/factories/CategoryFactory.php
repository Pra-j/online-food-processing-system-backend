<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    private static $categories = [
        [
            'name' => 'Momo',
            'description' => 'Steamed or fried dumplings filled with vegetables, chicken, or buff',
        ],
        [
            'name' => 'Pizza',
            'description' => 'Italian flatbread topped with cheese, vegetables, and various toppings',
        ],
        [
            'name' => 'Burger',
            'description' => 'Grilled patties served in a bun with lettuce, tomatoes, and sauces',
        ],
        [
            'name' => 'Noodles',
            'description' => 'Chowmein, thukpa, and other noodle-based dishes',
        ],
        [
            'name' => 'Fried Rice',
            'description' => 'Stir-fried rice with vegetables, eggs, and choice of meat',
        ],
        [
            'name' => 'Sandwich',
            'description' => 'Fresh bread filled with vegetables, cheese, and various fillings',
        ],
    ];

    private static $currentIndex = 0;

    public function definition()
    {
        $category = self::$categories[self::$currentIndex % count(self::$categories)];
        self::$currentIndex++;

        return [
            'name' => $category['name'],
            'description' => $category['description'],
        ];
    }
}
