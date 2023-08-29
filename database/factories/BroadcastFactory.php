<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Movie;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Broadcast>
 */
class BroadcastFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'movie_id' => Movie::all()->random()->id,
            'broadcasted_at' => fake()->dateTimeBetween('-1 year', '+1 year'),
            'channel' => fake()->randomElement(['HBO', 'Netflix', 'Amazon Prime', 'Disney+']),
        ];
    }
}
