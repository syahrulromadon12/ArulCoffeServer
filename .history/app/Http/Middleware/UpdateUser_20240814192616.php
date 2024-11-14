<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ambil ID dari user yang sedang login
        $loggedInUserId = Auth::id();

        // Ambil ID dari request (misalnya dari route parameter)
        $userIdToUpdate = $request->route('id'); // assuming 'id' is the route parameter name

        // Cek apakah ID user yang sedang login sama dengan ID yang akan di-update
        if ($loggedInUserId != $userIdToUpdate) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        return $next($request);
    }
}
