<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Display cart for the logged-in user
    public function index(Request $request)
    {
        $user = $request->user();  // Using dependency injection to get the user

        // Get cart for the logged-in user
        $carts = Cart::with('product')->forUser($user->id)->get();

        return view('cart.index', compact('carts'));
    }

    // Add product to cart
    public function addToCart(Request $request, $id)
    {
        $product = Product::find($id);

        // If product not found
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $user = $request->user();

        // Find cart item, if exists update quantity, otherwise create a new one
        $cartItem = Cart::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $product->id],
            ['quantity' => \DB::raw('quantity + ' . $request->quantity)] // Using DB raw for efficiency
        );

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    // Display cart data for the logged-in user
    public function getCart(Request $request)
    {
        $user = $request->user();

        // Get all cart items for the user
        $carts = Cart::with('product')
                     ->where('user_id', $user->id)
                     ->get();

        // If cart is empty
        if ($carts->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Your cart is empty.',
                'data' => [],
            ], 200);
        }

        // Return cart data
        return response()->json([
            'status' => 'success',
            'message' => 'Cart data retrieved successfully.',
            'data' => $carts,
        ], 200);
    }

    // Add product to cart via API
    public function addCart(Request $request)
{
    $request->validate([
        'product_id' => 'required|exists:products,id',
    ]);

    $userId = Auth::id();
    $productId = $request->product_id;
    $quantity = $request->quantity ?? 1; // Default quantity to 1 if not provided

    // Check if product exists
    $product = Product::find($productId);
    if (!$product) {
        return response()->json([
            'status' => 'error',
            'message' => 'Product not found.',
        ], 404);
    }

    // Check if product is already in the cart
    $cartItem = Cart::where('user_id', $userId)
        ->where('product_id', $productId)
        ->first();

    if ($cartItem) {
        // Update quantity if item already exists in the cart
        $cartItem->quantity += $quantity;
        $cartItem->save();
    } else {
        // Create new cart item
        Cart::create([
            'user_id' => $userId,
            'product_id' => $productId,
            'quantity' => $quantity,
        ]);
    }

    return response()->json([
        'status' => 'success',
        'message' => 'Product added to cart successfully.',
    ], 201);
}

}
