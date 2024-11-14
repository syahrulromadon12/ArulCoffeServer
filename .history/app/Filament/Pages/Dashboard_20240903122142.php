<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\Widget;
use App\Filament\Widgets\RecentOrders;
use App\Filament\Widgets\Revenue;
use App\Filament\Widgets\UserCount;

class Dashboard extends BaseDashboard
{
    protected ?string $heading = 'Custom Page Heading';
    protected function getContent(): string
    {
        return view('filament.dashboard', [
            'widgets' => [
                UserCount::class,
                Revenue::class,
                RecentOrders::class,
            ],
        ])->render();
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 3;
    }
}
