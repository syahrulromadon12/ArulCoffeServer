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
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Retrieve the number of orders per day for the current month
        $ordersPerDay = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Fill the days with no orders with 0
        $daysInMonth = $startOfMonth->daysInMonth;
        $dailyOrders = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = $startOfMonth->copy()->addDays($i - 1)->format('Y-m-d');
            $dailyOrders[] = $ordersPerDay[$date] ?? 0;
        }

        $newOrdersCount = array_sum($dailyOrders);

        return [
            Stat::make('New Orders', $newOrdersCount)
                ->description('This Month')
                ->descriptionIcon('heroicon-s-shopping-cart')
                ->color('primary')
                ->chart($dailyOrders)
        ];
    }
}
