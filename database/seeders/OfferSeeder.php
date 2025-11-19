<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Offer;
use App\Models\Product;

class OfferSeeder extends Seeder
{
    public function run(): void
    {
        Offer::factory()->count(10)->create();
    }
}
