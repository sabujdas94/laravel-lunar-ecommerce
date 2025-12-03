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