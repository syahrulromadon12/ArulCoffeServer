<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use App\Models\Cart;
use Midtrans\Config;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        // Ambil cart untuk user yang sedang login
        $userId = Auth::id();
        $carts = Cart::forUser($userId)->get();

        return view('cart.index', compact('carts'));
    }
    
    public function addToCart(Request $request, $id)
    {
        // Cari produk berdasarkan ID yang diterima dari URL
        $product = Product::find($id);

        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        // Cek apakah item sudah ada di keranjang
        $cartItem = Cart::where('user_id', Auth::id())
                        ->where('product_id', $product->id)
                        ->first();

        if ($cartItem) {
            // Update quantity jika produk sudah ada di keranjang
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Tambahkan item baru ke keranjang
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    
    
}
