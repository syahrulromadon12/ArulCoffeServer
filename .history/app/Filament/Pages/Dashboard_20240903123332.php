<?php

namespace App\Filament\Pages;

use Filament\Widgets\Widget;
use App\Filament\Widgets\Revenue;
use App\Filament\Widgets\UserCount;
use App\Filament\Widgets\RecentOrders;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;

class Dashboard extends BaseDashboard
{
    use HasFiltersForm;
    
    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate'),
                        DatePicker::make('endDate'),
                        // ...
                    ])
                    ->columns(3),
            ]);
    }

    protected function getContent(): string
    {
        return view('filament.dashboard', [
            'widgets' => [
                UserCount::class,
                Revenue::class,
                RecentOrders::class,
                Late
            ],
        ])->render();
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 3;
    }
}
