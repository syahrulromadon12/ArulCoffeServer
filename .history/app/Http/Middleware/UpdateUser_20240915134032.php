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
        // Get the currently logged-in user's ID
        $loggedInUserId = Auth::id();

        // Get the user ID from the route parameters
        $userIdToUpdate = $request->route('id');

        // Check if the logged-in user is authorized to update the given user
        if ($loggedInUserId != $userIdToUpdate) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
