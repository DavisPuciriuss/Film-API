<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/*
Route for creating a new user, logging into a existing user.
*/
Route::prefix('/user')->group(function () {
    Route::post('/register', [AuthController::class, 'createUser']);
    Route::post('/login', [AuthController::class, 'loginUser']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::delete('/delete', [AuthController::class, 'deleteUser']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
});
