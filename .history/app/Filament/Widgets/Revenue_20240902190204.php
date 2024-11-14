<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Order;
use Carbon\Carbon;

class Revenue extends BaseWidget
{
    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Mengambil pendapatan per hari untuk bulan ini
        $revenuePerDay = Order::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('revenue', 'date')
            ->toArray();

        // Isi hari tanpa transaksi dengan 0
        $daysInMonth = $startOfMonth->daysInMonth;
        $dailyRevenue = [];
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $date = $startOfMonth->copy()->addDays($i - 1)->format('Y-m-d');
            $dailyRevenue[] = $revenuePerDay[$date] ?? 0;
        }

        $totalRevenue = array_sum($dailyRevenue);

        return [
            Stat::make('Total Revenue', '$' . number_format($totalRevenue, 2))
                ->description('This Month')
                ->descriptionIcon('heroicon-s-currency-dollar')
                ->color('success')
                ->chart($dailyRevenue),
        ];
    }
}
