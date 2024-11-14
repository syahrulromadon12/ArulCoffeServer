<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Filament\Widgets\Widget;
use App\Filament\Widgets\Revenue;
use App\Filament\Widgets\UserCount;
use App\Filament\Widgets\LatestOrders;
use App\Filament\Widgets\RecentOrders;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\DatePicker;

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
                        // Add more filter fields as needed
                    ])
                    ->columns(3), // Adjust columns as needed
            ]);
    }

    protected function getContent(): string
    {
        return view('filament.dashboard', [
            'widgets' => [
                UserCount::class,
                Revenue::class,
                RecentOrders::class,
                LatestOrders::class,
            ],
        ])->render();
    }

    public function getHeaderWidgetsColumns(): int | array
    {
        return 1; // Set to 1 to ensure full-width widgets for header
    }
    
    public function getWidgetsLayout(): array
    {
        return [
            'widgets' => [
                UserCount::class,
                Revenue::class,
                RecentOrders::class,
                LatestOrders::class,
            ],
            'columns' => 1, // Set columns to 1 to ensure full-width layout
        ];
    }
}
