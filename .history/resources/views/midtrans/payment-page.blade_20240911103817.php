<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
    <!-- Include Tailwind CSS -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="flex justify-center items-center min-h-screen">
        <div class="w-full max-w-md bg-white shadow-lg rounded-lg p-6">
            <h1 class="text-2xl font-semibold text-center text-gray-800 mb-4">Order Summary</h1>
            
            <!-- Loop through cart items -->
            @foreach ($cartItems as $item)
            <div class="border-b border-gray-200 pb-4 mb-4">
                <div class="flex justify-between items-center">
                    <div>
                        <img src="" alt="">
                        <h2 class="text-lg font-medium text-gray-700">{{ $item->product->name }}</h2>
                        <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-gray-700">Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
            @endforeach

            <!-- Total Price -->
            <div class="flex justify-between items-center border-t border-gray-200 pt-4">
                <span class="text-lg font-medium text-gray-800">Total</span>
                <span class="text-xl font-bold text-gray-900">Rp {{ number_format($cartItems->sum(fn($item) => $item->product->price * $item->quantity), 0, ',', '.') }}</span>
            </div>

            <!-- Pay Button -->
            <div class="mt-6">
                <button id="pay-button" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition">
                    Pay Now
                </button>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            window.snap.pay('{{ $snapToken }}', {
                onSuccess: function(result) {
                    alert("Payment success!"); console.log(result);
                },
                onPending: function(result) {
                    alert("Waiting for your payment!"); console.log(result);
                },
                onError: function(result) {
                    alert("Payment failed!"); console.log(result);
                },
                onClose: function() {
                    alert('You closed the popup without finishing the payment.');
                }
            });
        });
    </script>
</body>
</html>
