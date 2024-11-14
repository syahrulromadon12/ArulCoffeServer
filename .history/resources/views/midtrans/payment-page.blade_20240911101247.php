<!-- resources/views/midtrans/payment-page.blade.php -->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    
    <!-- Memuat Snap.js dari Midtrans -->
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}"></script>
</head>
<body>
    <h1>Payment Page</h1>
    <button id="pay-button">Pay Now</button>

    <!-- Menambahkan container untuk Snap Embed -->
    <div id="snap-container"></div>

    <script type="text/javascript">
        // Mendapatkan referensi tombol pembayaran
        var payButton = document.getElementById('pay-button');
        
        // Ketika tombol pembayaran di-klik
        payButton.addEventListener('click', function () {
            // Panggil snap embed popup
            window.snap.embed('{{ $snapToken }}', {
                embedId: 'snap-container',
                onSuccess: function(result) {
                    alert("Payment Success!"); 
                    console.log(result);
                },
                onPending: function(result) {
                    alert("Waiting for Payment!"); 
                    console.log(result);
                },
                onError: function(result) {
                    alert("Payment Failed!"); 
                    console.log(result);
                },
                onClose: function() {
                    alert("You closed the popup without finishing the payment.");
                }
            });
        });
    </script>
</body>
</html>
