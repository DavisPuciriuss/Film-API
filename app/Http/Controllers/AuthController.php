<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * /user/register POST route.
     * Creates a new user, with the passed request data.
     * Returns the created user, and a API token.
     */
    public function createUser(CreateUserRequest $request): string
    {
        if (! is_array($request->validated()) || ! isset($request->validated()['name']) || ! isset($request->validated()['email']) || ! isset($request->validated()['password'])) {
            return response()->json([
                'message' => 'Invalid request data',
            ], 400);
        }

        $user = User::create([
            'name' => $request->validated()['name'],
            'email' => $request->validated()['email'],
            'password' => Hash::make($request->validated()['password']),
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'token' => $user->createToken('api_token')->plainTextToken,
        ], 201);
    }

    /**
     * /user/login POST route.
     * Log-in a user, with the passed request data.
     * Returns the logged in user, and a API token.
     * If the user is already logged in, the old token will be deleted.
     * If the user credentials don't match, a 401 response will be returned.
     */
    public function loginUser(LoginUserRequest $request): string
    {
        if (! is_array($request->validated()) || ! isset($request->validated()['email']) || ! isset($request->validated()['password'])) {
            return response()->json([
                'message' => 'Invalid request data',
            ], 400);
        }

        if (! Auth::attempt([
            'email' => $request->validated()['email'],
            'password' => Hash::make($request->validated()['password']),
        ])) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $user->tokens()->delete();

        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $user,
            'token' => $user->createToken('api_token')->plainTextToken,
        ]);
    }

    /**
     * /user/delete DELETE route.
     * Deletes the logged in user, and all of his tokens.
     * Fetches the user from the auth middleware, via the requests Bearer token.
     */
    public function deleteUser(Request $request): string
    {
        $user = auth('sanctum')->user();

        if ($user === null) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }

        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }
}
