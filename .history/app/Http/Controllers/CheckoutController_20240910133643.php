<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        // Ambil ID produk yang dipilih dari URL
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

        // Hitung total harga produk dalam keranjang
        $totalPrice = $carts->sum(fn($cart) => $cart->product->price * $cart->quantity);

        // Buat order
        $order = Order::create([
            'user_id' => auth()->id(), // ID pengguna yang sedang login
            'status_id' => 1,          // Atur status awal (misalnya '1' untuk pending)
            'total_price' => $totalPrice, // Jumlah total dari produk dalam keranjang
        ]);

        // Simpan item dalam order
        foreach ($carts as $cart) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cart->product_id,
                'quantity' => $cart->quantity,
                'price' => $cart->product->price,
            ]);
        }

        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false); 
        Config::$clientKey = config('midtrans.client_key');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        if ($paymentMethod === 'epayment') {
            // Pembayaran dengan Midtrans Snap API
            $params = [
                'transaction_details' => [
                    'order_id' => $order->id,
                    'gross_amount' => $totalPrice,
                ],
                'customer_details' => [
                    'first_name' => auth()->user()->name,
                    'email' => auth()->user()->email,
                ],
            ];

            $snapToken = Snap::getSnapToken($params);

            // Redirect ke halaman Midtrans untuk pembayaran
            return view('midtrans.payment-page', compact('snapToken'));
        } else {
            // Pembayaran cash
            foreach ($carts as $cart) {
                $cart->status = 'unpaid'; // Set status ke unpaid untuk pembayaran cash
                $cart->save();
            }

            return redirect()->route('filament.resources.carts.index')
                ->with('success', 'Pembelian produk berhasil dengan pembayaran cash.');
        }
    }
}
