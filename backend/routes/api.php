<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\MovieController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/movies')->group(function () {
        Route::get('/search', [MovieController::class, 'search']);
        Route::get('/{id}', [MovieController::class, 'show']);
    });

    Route::prefix('/favorites')->group(function () {
        Route::post('/', [FavoriteController::class, 'favorite']);
        Route::get('/', [FavoriteController::class, 'list']);
        Route::delete('/{tmdbId}', [FavoriteController::class, 'remove']);
    });
});
