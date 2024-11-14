<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Cart;
use App\Models\Product;
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

        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$clientKey = config('midtrans.client_key');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        if ($paymentMethod === 'epayment') {
            // Logika untuk membuat transaksi dengan Midtrans
            // Misalnya, gunakan Snap API untuk membuat pembayaran
            $params = [
                'transaction_details' => [
                    'order_id' => uniqid(),
                    'gross_amount' => $carts->sum('total_amount'),
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
                // Tambahkan detail lain sesuai dengan kebutuhan
            ];

            $snapToken = Snap::getSnapToken($params);

            // Redirect ke halaman Midtrans untuk pembayaran
            return view('midtrans.payment-page', compact('snapToken'));
        } else {
            // Logika untuk pembayaran cash
            foreach ($carts as $cart) {
                $cart->status = 'unpaid'; // Set status pembayaran cash
                $cart->save();
            }

            return redirect()->route('filament.resources.carts.index')
                ->with('success', 'Selected carts purchased with cash.');
        }
    }
}
