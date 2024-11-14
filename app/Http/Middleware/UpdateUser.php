<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil ID user yang sedang login
        $loggedInUserId = Auth::id();
        
        // Ambil ID user dari parameter route
        $userIdToUpdate = $request->route('id');

        // Periksa apakah ID user yang login sama dengan ID yang akan di-update
        if ($loggedInUserId != $userIdToUpdate) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
