<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuthenticationController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware(['auth:sanctum'])->group(function () {
    // routs auth
    Route::get('/me', [AuthenticationController::class, 'me']);
    Route::put('user/{id}',[AuthenticationController::class, 'update'])->middleware('update');
    Route::delete('user/{id}', [AuthenticationController::class, 'delete']);
    Route::get('/logout', [AuthenticationController::class, 'logout']);

    // routs products
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{id}', [ProductController::class, 'ProductDeatil']);

    // routs orders
    Route::post('/orders', [OrderController::class, 'createOrder']);
    Route::get('/orders', [OrderController::class, 'getOrders']);
    Route::get('/orders/{id}', [OrderController::class, 'getOrderDetails']);

    // routs cart
    Route::post('/carts', [CartController::class, 'addCart']);
    Route::get('/carts', [CartController::class, 'getCart']);

    //routs 
});

Route::post('/signup', [AuthenticationController::class, 'signup']);
Route::post('/login', [AuthenticationController::class, 'login']);