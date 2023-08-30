<?php

namespace Tests\Feature;

use App\Models\Movie;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class BroadcastControllerTest extends TestCase
{
    /**
     * /broadcast/create POST request.
     * POST request with missing data.
     */
    public function test_create_broadcast_missing_data(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.lv',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$user->createToken('api_token')->plainTextToken,
        ];

        $this->json('POST', 'api/broadcast/create', [], $headers)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The movie id field is required. (and 2 more errors)',
                'errors' => [
                    'movie_id' => [
                        'The movie id field is required.',
                    ],
                    'broadcasted_at' => [
                        'The broadcasted at field is required.',
                    ],
                    'channel' => [
                        'The channel field is required.',
                    ],
                ],
            ]);
    }

    /**
     * /broadcast/create POST request.
     * POST request with valida data.
     * Send a second POST request afterwards to make sure, you can't create two identical broadcasts on the same day.
     */
    public function test_create_broadcast(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.lv',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$user->createToken('api_token')->plainTextToken,
        ];

        $movie = Movie::factory()->create();

        $data = [
            'movie_id' => $movie->id,
            'channel' => 'test',
            'broadcasted_at' => '2023-09-01 12:00:00',
        ];

        $broadcast = $this->json('POST', 'api/broadcast/create', $data, $headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'status',
                'broadcast' => [

                ],
            ]);

        $this->json('POST', 'api/broadcast/create', $data, $headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'status',
                'broadcast' => [

                ],
            ]);
    }
}
