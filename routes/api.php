<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\ReviewController;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:20,1')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

// Public read endpoints.
Route::get('listings', [ListingController::class, 'index']);
Route::get('listings/{listing}', [ListingController::class, 'show']);
Route::get('categories', [ListingController::class, 'categories']);
Route::get('listings/{listing}/reviews', [ReviewController::class, 'index']);

// All write operations require a valid Laravel Sanctum bearer token.
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::get('me', [AuthController::class, 'me']);
    Route::post('logout', [AuthController::class, 'logout']);

    Route::post('listings', [ListingController::class, 'store']);
    Route::put('listings/{listing}', [ListingController::class, 'update']);
    Route::patch('listings/{listing}', [ListingController::class, 'update']);
    Route::delete('listings/{listing}', [ListingController::class, 'destroy']);

    Route::post('listings/{listing}/reviews', [ReviewController::class, 'store']);
    Route::put('reviews/{review}', [ReviewController::class, 'update']);
    Route::patch('reviews/{review}', [ReviewController::class, 'update']);
    Route::delete('reviews/{review}', [ReviewController::class, 'destroy']);

    Route::get('favorites', [AccountController::class, 'favorites']);
    Route::post('favorites/{listing}', [AccountController::class, 'toggleFavorite']);
    Route::post('claims', [AccountController::class, 'claim']);
});
