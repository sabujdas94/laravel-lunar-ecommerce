<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomePageController;

// Authentication routes (public)
Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);

// Protected authentication routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/me', [\App\Http\Controllers\Api\AuthController::class, 'me']);
    Route::get('/addresses', [\App\Http\Controllers\Api\AddressController::class, 'index']);
    // My orders
    Route::get('/my-orders', [\App\Http\Controllers\Api\OrderController::class, 'index']);
    Route::get('/my-orders/{id}', [\App\Http\Controllers\Api\OrderController::class, 'show']);
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::get('/home-page-collection', [HomePageController::class, 'collection']);
Route::get('/products', [\App\Http\Controllers\ShopPageController::class, 'getProducts']);
Route::get('/product/{slug}', [\App\Http\Controllers\Api\ProductController::class, 'showByUrl']);

// CMS API routes (public)
Route::prefix('cms')->group(function () {
    Route::get('/sliders', [\App\Http\Controllers\Api\CmsController::class, 'sliders']);
    Route::get('/partners', [\App\Http\Controllers\Api\CmsController::class, 'partners']);
    Route::get('/promo-popup', [\App\Http\Controllers\Api\CmsController::class, 'promoPopup']);
    Route::get('/home-page-data', [\App\Http\Controllers\Api\CmsController::class, 'homePageData']);
});

// Cart API routes
Route::prefix('cart')->group(function () {
    Route::post('/', [\App\Http\Controllers\Api\CartController::class, 'store']); // Create cart
    Route::get('/', [\App\Http\Controllers\Api\CartController::class, 'show']); // Get cart
    Route::post('/items', [\App\Http\Controllers\Api\CartController::class, 'addItem']); // Add item
    Route::patch('/items/{lineId}', [\App\Http\Controllers\Api\CartController::class, 'updateItem']); // Update quantity
    Route::delete('/items/{lineId}', [\App\Http\Controllers\Api\CartController::class, 'removeItem']); // Remove item
    Route::delete('/', [\App\Http\Controllers\Api\CartController::class, 'clear']); // Clear cart
});

// Checkout API routes
Route::prefix('checkout')->group(function () {
    Route::post('/shipping-address', [\App\Http\Controllers\Api\CheckoutController::class, 'setShippingAddress']); // Set shipping address
    Route::post('/billing-address', [\App\Http\Controllers\Api\CheckoutController::class, 'setBillingAddress']); // Set billing address
    Route::get('/summary', [\App\Http\Controllers\Api\CheckoutController::class, 'summary']); // Get checkout summary
    Route::post('/complete', [\App\Http\Controllers\Api\CheckoutController::class, 'complete']); // Complete checkout and create order
    Route::get('/order/{reference}', [\App\Http\Controllers\Api\CheckoutController::class, 'getOrder']); // Get order by reference
});
