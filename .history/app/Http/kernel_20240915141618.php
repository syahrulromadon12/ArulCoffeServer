<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * @var array
     */
    protected $middleware = [
        \App\Http\Middleware\CorsMiddleware::class,
        // middleware lainnya...
    ];

    /**
     * The application's route middleware.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,  // Middleware auth
        'cors' => \App\Http\Middleware\CorsMiddleware::class, // Middleware cors
        'update.user' => \App\Http\Middleware\UpdateUser::class,
    ];
}
