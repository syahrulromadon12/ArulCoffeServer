<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use Carbon\Carbon;

class UserCount extends BaseWidget
{
    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Mengambil jumlah pengguna baru per hari untuk bulan ini
        $usersPerDay = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->selectRaw('DATE(created_at) as date, count(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Isi hari tanpa pengguna baru dengan 0
        $dailyUsers = [];
        for ($i = 0; $i < $startOfMonth->daysInMonth; $i++) {
            $date = $startOfMonth->copy()->addDays($i)->format('Y-m-d');
            $dailyUsers[] = $usersPerDay[$date] ?? 0;
        }

        $totalUsers = User::count();

        return [
            Stat::make('Total Users', $totalUsers)
                ->description('Total number of users')
                ->descriptionIcon('heroicon-s-user')
                ->color('success')
                ->chart($dailyUsers),
        ];
    }
}
