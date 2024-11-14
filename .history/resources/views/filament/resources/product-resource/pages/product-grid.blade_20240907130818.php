{{-- resources/views/filament/resources/product-resource/pages/product-grid.blade.php --}}
<x-filament-panels::page>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($products as $product)
            <div class="border rounded-lg p-4  shadow">
                <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" class="w-full h-48 rounded-lg object-cover mb-4">
                <h2 class="text-lg font-semibold">{{ $product->name }}</h2>
                <p class="text-gray-600">{{ Str::limit($product->description, 50) }}</p>
                <p class="mt-2 font-bold text-lg">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded mt-2">
                        Add to Cart
                    </button>
                </form>

                <form action="{{ route('cart.buy', $product->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded mt-2">
                        Buy Now
                    </button>
                </form>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
