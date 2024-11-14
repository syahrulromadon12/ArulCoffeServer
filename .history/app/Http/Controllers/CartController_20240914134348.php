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

    / Menampilkan data keranjang berdasarkan user yang sedang login
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
                'message' => 'Keranjang kosong.',
                'data' => [],
            ], 200);
        }

        // Tampilkan data keranjang
        return response()->json([
            'message' => 'Data keranjang ditemukan.',
            'data' => $carts,
        ], 200);
    }

    // Menambahkan produk ke keranjang
    public function addCart(Request $request)
    {
        // Validasi input
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        // Ambil ID produk dan quantity dari request
        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        // Ambil produk berdasarkan ID
        $product = Product::find($productId);

        // Cek apakah produk ada
        if (!$product) {
            return response()->json([
                'message' => 'Produk tidak ditemukan.',
            ], 404);
        }

        // Ambil user yang sedang login
        $userId = Auth::id();

        // Cek apakah produk sudah ada di keranjang
        $cartItem = Cart::where('user_id', $userId)
                        ->where('product_id', $productId)
                        ->first();

        if ($cartItem) {
            // Jika produk sudah ada di keranjang, tambahkan quantity
            $cartItem->quantity += $quantity;
            $cartItem->save();

            return response()->json([
                'message' => 'Jumlah produk di keranjang berhasil diperbarui.',
                'data' => $cartItem,
            ], 200);
        } else {
            // Jika produk belum ada di keranjang, tambahkan item baru
            $newCartItem = Cart::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
            ]);

            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'data' => $newCartItem,
            ], 201);
        }
    }
    
}
