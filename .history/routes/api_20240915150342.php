<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\AuthenticationController;

// Rute untuk user yang sedang login
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    // Rute otentikasi
    Route::get('/me', [AuthenticationController::class, 'me']);
    Route::put('user/{id}', [AuthenticationController::class, 'update']);
    Route::delete('user/{id}', [AuthenticationController::class, 'delete']);
    Route::get('/logout', [AuthenticationController::class, 'logout']);

    // Rute produk
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'productDetail']);

    // Rute pesanan
    Route::post('/orders', [OrderController::class, 'createOrder']);
    Route::get('/orders', [OrderController::class, 'getOrders']);
    Route::get('/orders/{id}', [OrderController::class, 'getOrderDetails']);

    // Rute keranjang
    Route::post('/carts', [CartController::class, 'addCart']);
    Route::get('/carts', [CartController::class, 'getCart']);

    // Rute kategori
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/categories/{id}', [CategoryController::class, 'getProductCategories']);
});

// Rute untuk signup dan login
Route::post('/signup', [AuthenticationController::class, 'signup']);
Route::post('/login', [AuthenticationController::class, 'login']);
