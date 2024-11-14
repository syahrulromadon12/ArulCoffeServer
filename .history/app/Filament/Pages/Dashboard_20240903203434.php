<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\Revenue;
use App\Filament\Widgets\UserCount;
use App\Filament\Widgets\RecentOrders;
use App\Filament\Widgets\XNewOrders;

class Dashboard extends BaseDashboard
{
    protected function getWidgets(): array
    {
        return [
            'column-1' => [
                UserCount::class,
            ],
            'column-2' => [
                Revenue::class,
            ],
            'column-3' => [
                RecentOrders::class,
            ],
            'column-full' => [
                XNewOrders::class,
            ],
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'sm' => 1,
            'md' => 3, // Set 3 columns for medium screens and above
            'full' => 1, // Use 1 column for full-width widget
        ];
    }
}
