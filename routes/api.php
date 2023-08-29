<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\BroadcastController;

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

/*
    API Token auth routes.
*/
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/top-upcoming-movies', [MovieController::class, 'topUpcoming']);

    Route::prefix('/movies')->group(function () {
        Route::get('', [MovieController::class, 'index']);
        Route::post('/create', [MovieController::class, 'store']);
        Route::delete('/delete/{id}', [MovieController::class, 'destroy']);
    });

    Route::prefix('/broadcast')->group(function () {
        Route::post('/create', [BroadcastController::class, 'store']);
    });
});
