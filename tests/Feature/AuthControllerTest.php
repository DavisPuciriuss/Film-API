<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test api/user/register endpoint, with missing data.
     */
    public function test_create_user_missing_data(): void
    {
        $this->json('POST', 'api/user/register', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The name field is required. (and 2 more errors)',
                'errors' => [
                    'name' => ['The name field is required.'],
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ],
            ]);
    }

    /**
     * Test api/user/register endpoint, with valid data.
     */
    public function test_create_user(): void
    {
        $data = [
            'name' => 'Test User',
            'email' => 'test@test.lv',
            'password' => 'password',
        ];

        $this->json('POST', 'api/user/register', $data, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'status',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'token',
            ]);
    }

    /**
     * Test api/user/login endpoint, with missing data.
     */
    public function test_login_user_missing_data(): void
    {
        $this->json('POST', 'api/user/login', ['Accept' => 'application/json'])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The email field is required. (and 1 more error)',
                'errors' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ],
            ]);
    }

    /**
     * Test api/user/login endpoint, with valid data, but nonexistant user record.
     */
    public function test_login_user_nonexistant(): void
    {
        $data = [
            'email' => 'a@a.lv',
            'password' => 'b',
        ];

        $this->json('POST', 'api/user/login', $data, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJson([
                'message' => 'Invalid credentials',
                'status' => 401,
            ]);
    }

    /**
     * Test api/user/login endpoint, with valid data.
     */
    public function test_login_user_existant(): void
    {
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@test.lv',
            'password' => Hash::make('password'),
        ]);

        $data = [
            'email' => 'test@test.lv',
            'password' => 'password',
        ];

        $this->json('POST', 'api/user/login', $data, ['Accept' => 'application/json'])
            ->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'status',
                'user' => [
                    'id',
                    'name',
                    'email',
                    'created_at',
                    'updated_at',
                ],
                'token',
            ]);
    }

    /**
     * Test api/user/delete endpoint, with non-existant authorization.
     */
    public function test_delete_user_nonexistant(): void
    {
        $headers = [
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ',
        ];

        $this->json('DELETE', 'api/user/delete', $headers)
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }

    /**
     * Test api/user/delete endpoint, with valid authorization.
     */
    public function test_delete_user_existing(): void
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

        $this->json('DELETE', 'api/user/delete', $headers)
            ->assertStatus(200)
            ->assertJson([
                'message' => 'User deleted successfully',
                'status' => 200,
            ]);
    }
}
