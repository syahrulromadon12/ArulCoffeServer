<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Cart;
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

    // Konfigurasi Midtrans
    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = config('midtrans.is_production', false); 
    Config::$clientKey = config('midtrans.client_key');
    Config::$isSanitized = true;
    Config::$is3ds = true;

    // Buat Order Baru
    $order = Order::create([
        'user_id' => auth()->id(), // Mendapatkan ID pengguna yang sedang login
        'status_id' => 1, // Atur status awal (misalnya '1' untuk pending)
    ]);

    // Simpan Order Items
    foreach ($carts as $cart) {
        OrderItem::create([
            'order_id' => $order->id, // ID order yang baru dibuat
            'product_id' => $cart->product_id, // Produk yang terkait dengan keranjang
            'quantity' => $cart->quantity, // Jumlah item
            'price' => $cart->product->price, // Harga per item
        ]);

        // Hapus item dari keranjang
        $cart->delete();
    }

    if ($paymentMethod === 'epayment') {
        // Pembayaran dengan Midtrans Snap API
        $params = [
            'transaction_details' => [
                'order_id' => uniqid(),
                'gross_amount' => $carts->sum(fn($cart) => $cart->product->price * $cart->quantity),
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

        // Update status cart items menjadi unpaid
        foreach ($carts as $cart) {
            $cart->status = 'unpaid'; // Set status ke unpaid untuk pembayaran cash
            $cart->save();
        }

        // Simpan data pembayaran cash ke dalam tabel payments
        Payment::create([
            'order_id' => $order->id,
            'amount' => $carts->sum(fn($cart) => $cart->product->price * $cart->quantity),
            'payment_method' => 'cash',
            'payment_status' => 'unpaid', // Set status awal sebagai unpaid untuk pembayaran cash
        ]);

        return redirect()->route('filament.resources.carts.index')
            ->with('success', 'Pembelian produk berhasil dengan pembayaran cash.');
    }
}
}