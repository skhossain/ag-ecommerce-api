<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;

Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/register', [RegisteredUserController::class, 'store']);

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('products', ProductController::class)->only(['index', 'show']);
Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
// Protected Routes for Authenticated Users
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);
    Route::apiResource('cart', CartController::class);
    Route::apiResource('orders', OrderController::class)->only(['show']);
    Route::post('/clear-cart', [CartController::class, 'clearCart']);

    // Protected User Routes
    Route::middleware('user')->group(function () {
        Route::get('/user/dashboard', [UserController::class, 'dashboard']);
        Route::apiResource('orders', OrderController::class)->only(['store']);
        Route::get('/user/orders', [OrderController::class, 'userOrders']);
    });

    // Protected Admin Routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard']);
        Route::apiResource('products', ProductController::class)->only(['store','update','destroy']);
        Route::get('/product-list', [ProductController::class, 'productList']);
        Route::apiResource('categories', CategoryController::class)->only(['store','update','destroy']);
        Route::get('/category-list', [CategoryController::class, 'categoryList']);
        Route::apiResource('orders', OrderController::class)->only(['index']);
    });
});

// // Category Routes


