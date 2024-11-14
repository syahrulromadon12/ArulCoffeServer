

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        // Fetch user cart items and display the checkout page
        $cartItems = Auth::user()->cart;
        return view('checkout', compact('cartItems'));
    }
}
