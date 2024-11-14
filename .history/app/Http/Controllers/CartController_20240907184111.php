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

    public function processBulkPayment(Request $request)
    {
        $cartIds = $request->input('selected_products');
        $carts = Cart::whereIn('id', $cartIds)->get();

        if ($carts->isEmpty()) {
            return redirect()->back()->with('error', 'No products selected.');
        }

        $paymentMethod = $request->input('payment_method');

        if ($paymentMethod === 'epayment') {
            // Konfigurasi Midtrans dan buat token pembayaran
            Midtrans::setClientKey(env('MIDTRANS_CLIENT_KEY'));
            Midtrans::setServerKey(env('MIDTRANS_SERVER_KEY'));
            Midtrans::setIsProduction(env('MIDTRANS_IS_PRODUCTION') === 'true');
            Midtrans::setDefaultOptions();

            // Logika untuk memproses pembayaran dengan Midtrans
            // Simpan transaksi dan arahkan pengguna ke Midtrans

            return redirect('url_midtrans'); // Ganti dengan URL Midtrans
        } else {
            // Pembayaran dengan cash
            foreach ($carts as $cart) {
                $cart->status = 'unpaid';
                $cart->save();
            }

            return redirect()->route('filament.resources.carts.index')
                ->with('success', 'Selected carts purchased with cash.');
        }
    }
}
