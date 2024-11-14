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
        Config::$serverKey = 'SB-Mid-server-5M-WqNj38I6dFrECu1K_00x8';  // Ganti dengan Server Key Anda
        Config::$isProduction = false; // Set ke true jika ingin menggunakan environment production
        Config::$clientKey = 'SB-Mid-client-eN2uENOl7wpUuF_r';  // Ganti dengan Client Key Anda
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
            // Buat item_details
            $itemDetails = $carts->map(function ($cart) {
                return [
                    'id' => $cart->product_id,
                    'price' => intval($cart->product->price),
                    'quantity' => $cart->quantity,
                    'name' => $cart->product->name,
                ];
            })->toArray();

            $transactionData = [
                'transaction_details' => $transaction_details,
                'item_details'        => $item_details,
                'customer_details'    => $customer_details
            ];

            // Buat parameter untuk Midtrans Snap API
            $params = [
                'transaction_details' => [
                    'order_id' => $order->id,
                    'gross_amount' => intval($order->total_price),
                ],
                'item_details' => $itemDetails,  // Menambahkan item_details
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
                'transaction_data' => $transactionData, // Menambahkan transaction_data
            ];

            // Mendapatkan Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            // Simpan informasi pembayaran
            Payment::create([
                'order_id' => $order->id,
                'amount' => intval($order->total_price),
                'payment_method' => 'epayment',
                'payment_status' => 'pending',
            ]);

            // Redirect ke halaman Midtrans untuk pembayaran
            return view('midtrans.payment-page', [
                'snapToken' => $snapToken,
                'cartItems' => $carts,
            ]);
        } else {
            // Pembayaran cash
            Payment::create([
                'order_id' => $order->id,
                'amount' => intval($order->total_price),
                'payment_method' => 'cash',
                'payment_status' => 'paid',
            ]);

            return redirect()->route('filament.admin.resources.carts.index')
                ->with('success', 'Pembelian produk berhasil dengan pembayaran cash.');
        }
    }
}
