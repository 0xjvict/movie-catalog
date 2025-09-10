<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\MovieController;
use Illuminate\Support\Facades\Route;

// Rotas públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Rotas protegidas por token Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Dados do usuário e logout
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Movies
    Route::prefix('/movies')->group(function () {
        Route::get('/search', [MovieController::class, 'search']);
        Route::get('/{id}', [MovieController::class, 'show']);
    });

    // Favorites
    Route::prefix('/favorites')->group(function () {
        Route::post('/', [FavoriteController::class, 'favorite']);
        Route::get('/', [FavoriteController::class, 'list']);
        Route::delete('/{tmdbId}', [FavoriteController::class, 'remove']);
    });
});
