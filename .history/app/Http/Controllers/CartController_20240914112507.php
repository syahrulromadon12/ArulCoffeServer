<?`

namespace App\Http\Controllers;

use Midtrans\Config;
use Midtrans\Snap;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
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
                    'phone' => auth()->user()->phone_number,
                ],
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
