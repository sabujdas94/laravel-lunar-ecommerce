<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomePageController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/home-page-collection', [HomePageController::class, 'collection']);
Route::get('/products', [\App\Http\Controllers\ShopPageController::class, 'getProducts']);
Route::get('/product/{slug}', [\App\Http\Controllers\Api\ProductController::class, 'showByUrl']);

// Cart API routes
Route::prefix('cart')->group(function () {
    Route::post('/', [\App\Http\Controllers\Api\CartController::class, 'store']); // Create cart
    Route::get('/', [\App\Http\Controllers\Api\CartController::class, 'show']); // Get cart
    Route::post('/items', [\App\Http\Controllers\Api\CartController::class, 'addItem']); // Add item
    Route::patch('/items/{lineId}', [\App\Http\Controllers\Api\CartController::class, 'updateItem']); // Update quantity
    Route::delete('/items/{lineId}', [\App\Http\Controllers\Api\CartController::class, 'removeItem']); // Remove item
    Route::delete('/', [\App\Http\Controllers\Api\CartController::class, 'clear']); // Clear cart
});