<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Movie>
 */
class MovieFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(3),
            'rating' => fake()->randomFloat(1, 0, 10),
            'age_restriction' => fake()->randomElement(['None', '7+', '12+', '16+']),
            'description' => fake()->paragraph(3),
            'released_at' => fake()->dateTimeBetween('-1 year', '+1 year'),
        ];
    }
}
