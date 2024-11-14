<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use Carbon\Carbon;

class RecentOrders extends BaseWidget
{
    protected function getStats(): array
    {
        $newOrdersCount = Order::where('created_at', '>=', Carbon::now()->startOfMonth())->count();

        return [
            Stat::make('New Orders', $newOrdersCount)
                ->description('This Month')
                ->descriptionIcon('heroicon-s-shopping-cart')
                ->color('primary')
                ->chart([5, 10, 8, 15, 20, 10]),
        ];
    }

    protected function re(): array
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
