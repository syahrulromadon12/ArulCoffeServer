<?php

// app/Http/Middleware/UpdateUser.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateUser
{
    public function handle(Request $request, Closure $next): Response
    {
        $loggedInUserId = Auth::id();
        $userIdToUpdate = $request->route('id');

        if ($loggedInUserId != $userIdToUpdate) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
