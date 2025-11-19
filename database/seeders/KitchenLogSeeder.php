<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KitchenLog;

class KitchenLogSeeder extends Seeder
{
    public function run(): void
    {
        KitchenLog::factory()->count(24)->create();
    }
}
