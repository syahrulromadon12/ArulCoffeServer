<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\Widget;

class Dashboard extends BaseDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\UserCount::class,
            \App\Filament\Widgets\RevenueOverview::class,
            \App\Filament\Widgets\RecentOrders::class,
        ];
    }
}
