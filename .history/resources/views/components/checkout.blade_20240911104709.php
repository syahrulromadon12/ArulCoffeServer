<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Modal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Tambahkan custom CSS untuk modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 50;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
        }
    </style>
</head>
<body>
    <!-- Button to Open Modal -->
    <button id="openModal" class="bg-blue-500 text-white py-2 px-4 rounded-lg">Checkout</button>

    <!-- Modal -->
    <div id="checkoutModal" class="modal">
        <div class="modal-content">
            <span id="closeModal" class="float-right text-gray-500 cursor-pointer">&times;</span>
            <form action="{{ route('checkout.process') }}" method="POST">
                @csrf
                <h3 class="text-lg font-semibold mb-4">Keranjang Belanja</h3>
                @foreach($cartItems as $cart)
                    <div class="flex justify-between mb-2">
                        <p>{{ $cart->product->name }} - {{ $cart->quantity }} pcs</p>
                        <input type="hidden" name="selected_products[]" value="{{ $cart->id }}">
                    </div>
                @endforeach

                <h4 class="text-md font-medium mt-4 mb-2">Pilih Metode Pembayaran</h4>
                <select name="payment_method" class="mb-4 p-2 border rounded-lg">
                    <option value="cash">Cash</option>
                    <option value="epayment">E-Payment</option>
                </select>

                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Proses Pembayaran</button>
            </form>
        </div>
    </div>

    <script>
        // JavaScript to handle modal behavior
        const modal = document.getElementById('checkoutModal');
        const openModalButton = document.getElementById('openModal');
        const closeModalButton = document.getElementById('closeModal');

        openModalButton.addEventListener('click', () => {
            modal.style.display = 'block';
        });

        closeModalButton.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
