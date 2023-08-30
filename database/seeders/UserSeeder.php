<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::factory([
            'name' => 'Test User',
            'email' => 'test@test.lv',
            'password' => Hash::make('password'),
        ])->create();

    }
}
