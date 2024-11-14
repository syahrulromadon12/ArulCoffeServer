<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
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


    // Buy now functionality
    public function buyNow(Request $request, Product $product)
    {
        // Similar to addToCart but redirect to checkout page after adding to cart
        $cartItem = Cart::where('user_id', Auth::id())
                        ->where('product_id', $product->id)
                        ->first();

        if ($cartItem) {
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $product->id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('checkout');
    }
}
