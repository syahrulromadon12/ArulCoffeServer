<?php

use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', CorsMiddleware::class])
    ->group(function () {
        Filament::routes();
    });

    
Route::get('/', function () {
    return view('welcome', ['title' => 'Dashboard']);
});

Route::get('/about', function () {
    return view('about', ['title' => 'About API']);
});

Route::get('/documentation', function () {
    return view('documentation', ['title' => 'Documentation API']);
});

Route::get('/login', function () {
    return view('login', ['title' => 'Login']);
});

// Route::get('/products', function () {
//     $products = Product::with('category')->get();
//     return response()->json($products);
// });




