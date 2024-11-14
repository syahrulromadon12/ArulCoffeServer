<?php

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    public function index()
    {
        // Ambil item keranjang pengguna yang sedang login
        $cartItems = Auth::user()->cart;
        return view('checkout', compact('cartItems'));
    }

    public function processPayment(Request $request)
    {
        // Ambil ID produk yang dipilih dari input form
        $cartIds = $request->input('selected_products');
        $carts = Cart::whereIn('id', $cartIds)->get();

        if ($carts->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada produk yang dipilih.');
        }

        $paymentMethod = $request->input('payment_method');

        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false); // Gunakan false untuk sandbox
        Config::$clientKey = config('midtrans.client_key');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        if ($paymentMethod === 'epayment') {
            // Logika pembayaran dengan Midtrans menggunakan Snap API
            $params = [
                'transaction_details' => [
                    'order_id' => uniqid(),
                    'gross_amount' => $carts->sum('total_amount'),
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
            // Logika pembayaran secara cash
            foreach ($carts as $cart) {
                $cart->status = 'unpaid'; // Set status ke unpaid untuk cash
                $cart->save();
            }

            return redirect()->route('filament.resources.carts.index')
                ->with('success', 'Pembelian produk berhasil dengan pembayaran cash.');
        }
    }
}
