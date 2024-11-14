<?php

namespace App\Http\Controllers;

use Midtrans\Snap;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // Menampilkan keranjang user yang sedang login
    public function index(Request $request)
    {
        $user = $request->user();  // Menggunakan dependency injection untuk user

        // Ambil cart untuk user yang sedang login
        $carts = Cart::with('product')->forUser($user->id)->get();

        return view('cart.index', compact('carts'));
    }

    // Tambah produk ke keranjang
    public function addToCart(Request $request, $id)
    {
        $product = Product::find($id);

        // Jika produk tidak ditemukan
        if (!$product) {
            return redirect()->back()->with('error', 'Product not found.');
        }

        $user = $request->user();

        // Cari item di cart, jika ada update quantity, jika tidak buat baru
        $cartItem = Cart::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $product->id],
            ['quantity' => \DB::raw('quantity + ' . $request->quantity)] // Menggunakan DB raw untuk efisiensi
        );

        return redirect()->back()->with('success', 'Product added to cart!');
    }

    // Menampilkan data keranjang berdasarkan user yang sedang login
    public function getCart(Request $request)
    {
        $user = $request->user();

        // Ambil semua item dari keranjang yang dimiliki user tersebut
        $carts = Cart::with('product')
                     ->where('user_id', $user->id)
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

    // Menambahkan produk ke keranjang melalui API
    public function addCart(Request $request)
    {
        // Validasi data input
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $user = $request->user();  // Ambil user yang sedang login

        $productId = $validatedData['product_id'];
        $quantity = $validatedData['quantity'];

        // Cari produk berdasarkan ID
        $product = Product::find($productId);

        // Jika produk tidak ditemukan
        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found.',
            ], 404);
        }
z
        // Update atau buat item baru di keranjang
        $cartItem = Cart::updateOrCreate(
            ['user_id' => $user->id, 'product_id' => $productId],
            ['quantity' => \DB::raw('quantity + ' . $quantity)] // Update quantity secara efisien
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Product added to cart successfully.',
        ], 201);
    }
}
