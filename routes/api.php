<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('categories', CategoryController::class);
    Route::apiResource('suppliers', SupplierController::class);
    Route::apiResource('products', ProductController::class);
    Route::post('/products/{id}/restore', [ProductController::class, 'restore']);
});
