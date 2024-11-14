<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

Route::middleware(['web', CorsMiddleware::class])
    ->group(function () {
        Route::get('/', function () {
            return view('about', ['title' => 'About API']);
        });

        Route::get('documentation', function () {
            return view('documentation', ['title' => 'Documentation API']);
        });

        Route::get('/login', function () {
            return view('login', ['title' => 'Login']);
        });

        // Menambahkan middleware auth untuk rute yang memerlukan autentikasi
        Route::middleware(['auth'])->group(function () {
            Route::post('/cart/add/{id}', [CartController::class, 'addToCart'])->name('cart.add');
            Route::post('/cart/buy/{id}', [CartController::class, 'buyNow'])->name('cart.buy');
            Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
            Route::post('/checkout/process', [CheckoutController::class, 'processPayment'])->name('checkout.process');        
            Route::post('/cart/process-bulk-payment', [CartController::class, 'processBulkPayment'])->name('cart.processBulkPayment');
        });
    });
//baru sampai ke pembayaran