<?php

namespace Database\Seeders;

use App\Models\Broadcast;
use Illuminate\Database\Seeder;

class BroadcastSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Broadcast::factory()
            ->count(1000)
            ->create();
    }
}
