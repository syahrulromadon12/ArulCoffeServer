{{-- resources/views/filament/pages/dashboard.blade.php --}}
<x-filament::page>
    {{-- Top row with widgets --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($widgets['top'] as $widget)
            <div>
                @livewire($widget)
            </div>
        @endforeach
    </div>

    {{-- Full-width widget --}}
    <div class="mt-6">
        @foreach ($widgets['bottom'] as $widget)
            <div class="w-full">
                @livewire($widget)
            </div>
        @endforeach
    </div>
</x-filament::page>
