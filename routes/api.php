<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/products', [ProductController::class, 'index']);

Route::get('/categories', [ProductCategoryController::class, 'index']);

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'fetch']);
    Route::post('/user', [UserController::class, 'update']);
    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'update']);
    Route::delete('/cart', [CartController::class, 'remove']);

    Route::get('/wishlist', [WishlistController::class, 'index']);
    Route::post('/wishlist/{productId}', [WishlistController::class, 'add']);
    Route::delete('/wishlist/{productId}', [WishlistController::class, 'remove']);

    Route::get('/transaction', [TransactionController::class, 'index']);
    Route::post('/checkout', [TransactionController::class, 'checkout']);
});
