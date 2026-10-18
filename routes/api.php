<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ListingController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\AccountController;
Route::middleware('throttle:20,1')->group(function(){Route::post('register',[AuthController::class,'register']);Route::post('login',[AuthController::class,'login']);});
Route::get('listings',[ListingController::class,'index']);
Route::get('categories',[ListingController::class,'categories']);
Route::get('listings/{listing}',[ListingController::class,'show']);
Route::get('listings/{listing}/reviews',[ReviewController::class,'index']);
Route::middleware(['auth:sanctum','throttle:60,1'])->group(function(){
 Route::get('me',[AuthController::class,'me']);Route::post('logout',[AuthController::class,'logout']);
 Route::post('listings/{listing}/reviews',[ReviewController::class,'store']);
 Route::get('favorites',[AccountController::class,'favorites']);Route::post('favorites/{listing}',[AccountController::class,'toggleFavorite']);
 Route::post('claims',[AccountController::class,'claim']);
});
