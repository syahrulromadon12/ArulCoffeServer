{{-- resources/views/filament/pages/dashboard.blade.php --}}
<x-filament::page>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
        {{-- Widget Top Row --}}
        <div class="col-span-1 md:col-span-1">
            @livewire(App\Filament\Widgets\UserCount::class)
        </div>
        <div class="col-span-1 md:col-span-1">
            @livewire(App\Filament\Widgets\Revenue::class)
        </div>
        <div class="col-span-1 md:col-span-1">
            @livewire(App\Filament\Widgets\RecentOrders::class)
        </div>
    </div>

    <div class="mt-6">
        {{-- Full-width widget --}}
        @livewire(App\Filament\Widgets\XNewOrders::class)
    </div>
</x-filament::page>
