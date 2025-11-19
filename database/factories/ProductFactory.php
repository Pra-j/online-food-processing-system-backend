<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    private static $products = [
        // Momo (category_id: 1)
        [
            'category_id' => 1,
            'name' => 'Veg Steam Momo',
            'description' => 'Fresh vegetables wrapped in thin dough and steamed to perfection',
            'price' => 120.00,
            'stock' => 50,
            'food_type' => 'veg',
            'course_type' => 'appetizer',
        ],
        [
            'category_id' => 1,
            'name' => 'Chicken Steam Momo',
            'description' => 'Juicy chicken filling with Nepali spices, served with spicy sauce',
            'price' => 150.00,
            'stock' => 45,
            'food_type' => 'non-veg',
            'course_type' => 'appetizer',
        ],
        [
            'category_id' => 1,
            'name' => 'Buff Fried Momo',
            'description' => 'Crispy fried momos stuffed with spiced buffalo meat',
            'price' => 180.00,
            'stock' => 40,
            'food_type' => 'non-veg',
            'course_type' => 'appetizer',
        ],

        // Pizza (category_id: 2)
        [
            'category_id' => 2,
            'name' => 'Margherita Pizza',
            'description' => 'Classic pizza with tomato sauce, mozzarella cheese, and fresh basil',
            'price' => 450.00,
            'stock' => 30,
            'food_type' => 'veg',
            'course_type' => 'main',
        ],
        [
            'category_id' => 2,
            'name' => 'Chicken BBQ Pizza',
            'description' => 'BBQ chicken, onions, bell peppers with special BBQ sauce',
            'price' => 550.00,
            'stock' => 25,
            'food_type' => 'non-veg',
            'course_type' => 'main',
        ],
        [
            'category_id' => 2,
            'name' => 'Veggie Supreme Pizza',
            'description' => 'Loaded with mushrooms, olives, capsicum, onions, and tomatoes',
            'price' => 480.00,
            'stock' => 28,
            'food_type' => 'veg',
            'course_type' => 'main',
        ],
        [
            'category_id' => 2,
            'name' => 'Pepperoni Pizza',
            'description' => 'Classic pepperoni slices with extra cheese and Italian herbs',
            'price' => 520.00,
            'stock' => 32,
            'food_type' => 'non-veg',
            'course_type' => 'main',
        ],

        // Burger (category_id: 3)
        [
            'category_id' => 3,
            'name' => 'Classic Chicken Burger',
            'description' => 'Grilled chicken patty with lettuce, tomato, and special sauce',
            'price' => 220.00,
            'stock' => 35,
            'food_type' => 'non-veg',
            'course_type' => 'main',
        ],
        [
            'category_id' => 3,
            'name' => 'Veg Cheese Burger',
            'description' => 'Veggie patty with double cheese, onions, and mayo',
            'price' => 180.00,
            'stock' => 40,
            'food_type' => 'veg',
            'course_type' => 'main',
        ],
        [
            'category_id' => 3,
            'name' => 'Buff Burger',
            'description' => 'Juicy buffalo meat patty with caramelized onions and special sauce',
            'price' => 250.00,
            'stock' => 30,
            'food_type' => 'non-veg',
            'course_type' => 'main',
        ],

        // Noodles (category_id: 4)
        [
            'category_id' => 4,
            'name' => 'Veg Chowmein',
            'description' => 'Stir-fried noodles with fresh vegetables and soy sauce',
            'price' => 130.00,
            'stock' => 45,
            'food_type' => 'veg',
            'course_type' => 'main',
        ],
        [
            'category_id' => 4,
            'name' => 'Chicken Chowmein',
            'description' => 'Stir-fried noodles with chicken and mixed vegetables',
            'price' => 160.00,
            'stock' => 42,
            'food_type' => 'non-veg',
            'course_type' => 'main',
        ],
        [
            'category_id' => 4,
            'name' => 'Thukpa',
            'description' => 'Traditional Tibetan noodle soup with vegetables and spices',
            'price' => 140.00,
            'stock' => 38,
            'food_type' => 'veg',
            'course_type' => 'main',
        ],
        [
            'category_id' => 4,
            'name' => 'Buff Chowmein',
            'description' => 'Spicy stir-fried noodles with buffalo meat and vegetables',
            'price' => 180.00,
            'stock' => 35,
            'food_type' => 'non-veg',
            'course_type' => 'main',
        ],

        // Fried Rice (category_id: 5)
        [
            'category_id' => 5,
            'name' => 'Veg Fried Rice',
            'description' => 'Stir-fried rice with mixed vegetables and scrambled eggs',
            'price' => 140.00,
            'stock' => 50,
            'food_type' => 'veg',
            'course_type' => 'main',
        ],
        [
            'category_id' => 5,
            'name' => 'Chicken Fried Rice',
            'description' => 'Fried rice with tender chicken pieces and vegetables',
            'price' => 170.00,
            'stock' => 45,
            'food_type' => 'non-veg',
            'course_type' => 'main',
        ],
        [
            'category_id' => 5,
            'name' => 'Egg Fried Rice',
            'description' => 'Simple fried rice with eggs and spring onions',
            'price' => 120.00,
            'stock' => 48,
            'food_type' => 'veg',
            'course_type' => 'main',
        ],

        // Sandwich (category_id: 6)
        [
            'category_id' => 6,
            'name' => 'Chicken Sandwich',
            'description' => 'Grilled chicken with lettuce, tomato, and mayo in fresh bread',
            'price' => 150.00,
            'stock' => 35,
            'food_type' => 'non-veg',
            'course_type' => 'appetizer',
        ],
        [
            'category_id' => 6,
            'name' => 'Veg Club Sandwich',
            'description' => 'Triple-decker sandwich with vegetables, cheese, and sauces',
            'price' => 180.00,
            'stock' => 32,
            'food_type' => 'veg',
            'course_type' => 'appetizer',
        ],
        [
            'category_id' => 6,
            'name' => 'Cheese Grilled Sandwich',
            'description' => 'Grilled sandwich with melted cheese and vegetables',
            'price' => 130.00,
            'stock' => 40,
            'food_type' => 'veg',
            'course_type' => 'appetizer',
        ],
    ];

    private static $currentIndex = 0;

    public function definition()
    {
        $product = self::$products[self::$currentIndex % count(self::$products)];
        self::$currentIndex++;

        return [
            'category_id' => $product['category_id'],
            'name' => $product['name'],
            'description' => $product['description'],
            'price' => $product['price'],
            'stock' => $product['stock'],
            'food_type' => $product['food_type'],
            'course_type' => $product['course_type'],
            'is_active' => true,
        ];
    }
}
