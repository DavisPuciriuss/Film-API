<?php

namespace Tests\Feature;

use App\Models\Broadcast;
use App\Models\Movie;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MovieControllerTest extends TestCase
{
    /**
     * /movies GET route.
     * Returns a list of movies paginated by 10, sorted by movie added datetime.
     */
    public function test_get_movies(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.lv',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        Movie::factory()->count(11)->create();

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$user->createToken('api_token')->plainTextToken,
        ];

        $page1 = $this->json('GET', '/api/movies', [], $headers);
        $page2 = $this->json('GET', '/api/movies?page=2', [], $headers);

        $paginated_structure = [
            'current_page',
            'data',
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links',
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total',
        ];

        $page1->assertStatus(200)
            ->assertJsonStructure($paginated_structure);

        $page2->assertStatus(200)
            ->assertJsonStructure($paginated_structure);

        $this->assertCount(10, json_decode($page1->getContent())->data);
        $this->assertCount(1, json_decode($page2->getContent())->data);
    }

    /**
     * /movies GET route.
     * Returns a list of movies paginated by 10, sorted by movie added datetime, searched by title.
     */
    public function test_get_movies_by_title(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.lv',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $movie = Movie::factory()->count(50)->create()->first();

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$user->createToken('api_token')->plainTextToken,
        ];

        $data = [
            'title' => $movie->title,
        ];

        $response = $this->json('GET', '/api/movies', $data, $headers);

        $this->assertCount(1, json_decode($response->getContent())->data);
    }

    /**
     * /top-upcoming-movies endpoint test.
     * Returns a list of top upcoming movies (Rating > 7.0) paginated by 10, sorted by closest upcoming broadcast.
     */
    public function test_get_top_upcoming_movies(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.lv',
            'password' => Hash::make('password'),
        ]);

        $this->actingAs($user);

        $request = 'api/top-upcoming-movies';

        $movies = Movie::factory()->count(20)->create();

        $broadcasts = Broadcast::factory()->count(50)->create();

        $top_movies_count = $movies->filter(function ($movie) {
            return $movie->rating > 7.0 && $movie->released_at > now();
        })->count();

        if ($top_movies_count > 10) {
            $top_movies_count = $top_movies_count - 10;
            $request = $request.'?page=2';
        }

        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer '.$user->createToken('api_token')->plainTextToken,
        ];

        $response = $this->json('GET', $request, $headers);

        $this->assertCount($top_movies_count, json_decode($response->getContent())->data);
    }

    /**
     * /movies/create POST route.
     * Send a POST request with missing data.
     */
    public function test_create_movie_missing_data(): void
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

        $this->json('POST', 'api/movies/create', [], $headers)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The title field is required. (and 4 more errors)',
                'errors' => [
                    'title' => ['The title field is required.'],
                    'rating' => ['The rating field is required.'],
                    'age_restriction' => ['The age restriction field is required.'],
                    'description' => ['The description field is required.'],
                    'released_at' => ['The released at field is required.'],
                ],
            ]);
    }

    /**
     * /movies/create POST endpoint test.
     * Send a POST request with valid data.
     */
    public function test_create_movie(): void
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

        $data = [
            'title' => 'Test',
            'rating' => 5.0,
            'age_restriction' => '16+',
            'description' => 'Test',
            'released_at' => '2023-09-01 12:00:00',
        ];

        $this->json('POST', 'api/movies/create', $data, $headers)
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'status',
                'movie' => [

                ],
            ]);
    }

    /**
     * /movies/delete/{id} DELETE route.
     * Send a DELETE request with a nonexistant movie ID.
     */
    public function test_delete_movie_nonexistant(): void
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

        $this->json('DELETE', 'api/movies/delete/'.$user->id, $headers)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Movie not found !',
                'status' => 404,
            ]);
    }

    /**
     * /movies/delete/{id} DELETE route.
     * Send a DELETE request with a valid movie ID.
     */
    public function test_delete_movie(): void
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

        $this->json('DELETE', 'api/movies/delete/'.$movie->id, $headers)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Movie deleted succesfully !',
                'status' => 200,
            ]);
    }
}
