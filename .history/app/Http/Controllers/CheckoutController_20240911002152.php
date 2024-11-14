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

        // Konfigurasi Midtrans
        Config::$serverKey = "SB-Mid-server-5M-WqNj38I6dFrECu1K_00x8";
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$clientKey = config('midtrans.client_key');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        // Buat otorisasi secara manual jika API memerlukan
        $auth_string = base64_encode('midtrans.server_key');

        // Anda bisa menggunakan cURL atau framework lain untuk menambahkan header secara manual
        $headers = [
            "Authorization: Basic $auth_string",
            "Accept: application/json",
            "Content-Type: application/json"
        ];

        // Buat Order
        $order = Order::create([
            'user_id' => auth()->id(),
            'status_id' => 1, // Atur status awal (misalnya '1' untuk pending)
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
                    'gross_amount' => $order->total_price,
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // Simpan informasi pembayaran
            Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_price,
                'payment_method' => 'epayment',
                'payment_status' => 'pending',
            ]);

            // Redirect ke halaman Midtrans untuk pembayaran
            return view('midtrans.payment-page', compact('snapToken'));
        } else {
            // Pembayaran cash
            Payment::create([
                'order_id' => $order->id,
                'amount' => $order->total_price,
                'payment_method' => 'cash',
                'payment_status' => 'paid',
            ]);

            return redirect()->route('filament.admin.resources.carts.index')
                ->with('success', 'Pembelian produk berhasil dengan pembayaran cash.');
        }
    }
}
