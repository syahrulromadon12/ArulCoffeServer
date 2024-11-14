<form action="{{ route('checkout.process') }}" method="POST">
    @csrf
    <h3>Keranjang Belanja</h3>
    @foreach($cartItems as $cart)
        <p>{{ $cart->product->name }} - {{ $cart->quantity }} pcs</p>
        <input type="hidden" name="selected_products[]" value="{{ $cart->id }}">
    @endforeach

    <h4>Pilih Metode Pembayaran</h4>
    <select name="payment_method">
        <option value="cash">Cash</option>
        <option value="epayment">E-Payment</option>
    </select>

    <button type="submit">Proses Pembayaran</button>
</form>
