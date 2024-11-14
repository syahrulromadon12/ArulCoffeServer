<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order; // Assuming revenue is derived from orders
use Illuminate\Support\Facades\DB;

class Revenue extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRevenue = Order::sum('total_price'); // Calculate total revenue

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('This Month')
                ->descriptionIcon('heroicon-s-currency-dollar')
                ->color('success')
                ->chart([1000, 2000, 1500, 3000, 4000, 2500]), // Example data
        ];
    }
}
