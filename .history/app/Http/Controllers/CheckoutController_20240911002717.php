<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cartIds = $request->input('selected_products');
        $cartItems = Cart::whereIn('id', $cartIds)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'No products selected.');
        }

        return view('components.checkout', compact('cartItems'));
    }

    public function processPayment(Request $request)
    {
        $cartIds = $request->input('selected_products');
        $carts = Cart::whereIn('id', $cartIds)->get();

        if ($carts->isEmpty()) {
            return redirect()->back()->with('error', 'No products selected.');
        }

        $paymentMethod = $request->input('payment_method');

        // Konfigurasi Midtrans secara statis
        Config::$serverKey = 'SB-Mid-server-5M-WqNj38I6dFrECu1K_00x8';  // Ganti dengan Server Key Anda
        Config::$isProduction = false; // Set ke true jika ingin menggunakan environment production
        Config::$clientKey = 'YOUR_STATIC_CLIENT_KEY';  // Ganti dengan Client Key Anda
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Buat Order
        $order = Order::create([
            'user_id' => auth()->id(),
            'status_id' => 1,  // Set status awal (misalnya '1' untuk pending)
            'total_price' => $carts->sum(fn($cart) => $cart->product->price * $cart->quantity),
        ]);

        // Buat Order Items
        foreach ($carts as $cart) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'price' => $cart->product->price,
            ]);
        }

        if ($paymentMethod === 'epayment') {
            // Pembayaran dengan Midtrans Snap API
            $params = [
                'transaction_details' => [
                    'order_id' => $order->id,
                    'gross_amount' => intval($order->total_price),  // Pastikan total_price diubah menjadi integer
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
            ];

            // Mendapatkan Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            // Simpan informasi pembayaran
            Payment::create([
                'order_id' => $order->id,
                'amount' => intval($order->total_price),  // Pastikan total_price adalah integer
                'payment_method' => 'epayment',
                'payment_status' => 'pending',
            ]);

            // Redirect ke halaman Midtrans untuk pembayaran
            return view('midtrans.payment-page', compact('snapToken'));
        } else {
            // Pembayaran cash
            Payment::create([
                'order_id' => $order->id,
                'amount' => intval($order->total_price),  // Pastikan total_price adalah integer
                'payment_method' => 'cash',
                'payment_status' => 'paid',
            ]);

            return redirect()->route('filament.admin.resources.carts.index')
                ->with('success', 'Pembelian produk berhasil dengan pembayaran cash.');
        }
    }
}
