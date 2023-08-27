<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\LoginUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * attemptLogin
     * Attempts to login the user, on failure return a 401 response.
     * @param  LoginUserRequest $request
     * @return void
     */
    private function attemptLogin(LoginUserRequest $request)
    {
        if(!Auth::attempt([
            'email' => $request->validated()['email'],
            'password' => Hash::make($request->validated()['password'])
        ])) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
    }

    public function createUser(CreateUserRequest $request)
    {
        $user = User::create([
            'name' => $request->validated()['name'],
            'email' => $request->validated()['email'],
            'password' => Hash::make($request->validated()['password'])
        ]);

        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'token' => $user->createToken('api_token')->plainTextToken
        ], 201);
    }

    public function loginUser(LoginUserRequest $request)
    {
        $this->attemptLogin($request);

        $user = User::where('email', $request->email)->firstOrFail();

        $user->tokens()->delete();

        return response()->json([
            'message' => 'User logged in successfully',
            'user' => $user,
            'token' => $user->createToken('api_token')->plainTextToken
        ]);
    }

    public function deleteUser(Request $request)
    {
        $user = auth('sanctum')->user();

        $user->tokens()->delete();

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ]);
    }
}
