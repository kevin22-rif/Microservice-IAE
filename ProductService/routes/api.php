<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route; // Pastikan ini di-import juga

Route::apiResource('products', ProductController::class);

// Ini adalah satu-satunya definisi POST untuk /products/order
Route::post('/products/order', [ProductController::class, 'processOrder']);

