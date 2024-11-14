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

    //Menampilkan data keranjang berdasarkan user yang sedang login
    public function getCart()
    {
        // Ambil ID user yang sedang login
        $userId = Auth::id();

        // Ambil semua item dari keranjang yang dimiliki user tersebut
        $carts = Cart::with('product') // Include relasi product
                     ->where('user_id', $userId)
                     ->get();

        // Jika keranjang kosong
        if ($carts->isEmpty()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Keranjang kosong.',
                'data' => [],
            ], 200);
        }

        // Tampilkan data keranjang
        return response()->json([
            'status' => 'success',
            'message' => 'Data keranjang ditemukan.',
            'data' => $carts,
        ], 200);
    }

    public function addCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $userId = Auth::id();
        $productId = $request->product_id;
        $quantity = $request->quantity;

        // Check if product exists
        $product = Product::find($productId);
        if (!$product) {
            return response()->json([
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
