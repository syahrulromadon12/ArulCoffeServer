{{-- resources/views/filament/resources/product-resource/pages/product-grid.blade.php --}}
<x-filament-panels::page>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
        @foreach ($products as $product)
            <div class="border rounded-lg p-4 bg-white shadow">
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-48 object-cover mb-4">
                <h2 class="text-lg font-semibold">{{ $product->name }}</h2>
                <p class="text-gray-600">{{ Str::limit($product->description, 50) }}</p>
                <p class="mt-2 font-bold text-lg">IDR {{ number_format($product->price, 0, ',', '.') }}</p>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
