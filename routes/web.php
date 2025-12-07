<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\PageController;

Route::get('/terms', [PageController::class, 'terms'])->name('terms');
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

