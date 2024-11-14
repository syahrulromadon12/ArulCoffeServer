<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CorsMiddleware;

// Menambahkan middleware CORS ke semua rute, termasuk rute Filament
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

        Route::post('/cart/add/{product}', [CartController::class, 'addToCart'])->name('cart.add');
        Route::post('/cart/buy/{product}', [CartController::class, 'buyNow'])->name('cart.buy');

        // Contoh rute lainnya yang menggunakan CORS middleware
        // Route::get('/products', function () {
        //     $products = Product::with('category')->get();
        //     return response()->json($products);
        // });
    });
