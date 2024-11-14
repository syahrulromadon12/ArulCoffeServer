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
                    ])
                    ->columns(3),
            ]);
    }

    protected function getHeaderWidgetsColumns(): int | array
    {
        return 1; // Ensure full-width widgets for header
    }

    public function getWidgets(): array
    {
        return [
            'widgets' => [
                UserCount::class,
                Revenue::class,
                RecentOrders::class,
                XNewOrders::class, // Full-width widget
            ],
        ];
    }

    public function getWidgetsLayout(): array
    {
        return [
            'columns' => 3, // Ensure widgets are aligned in 3 columns
            'fullWidthWidgets' => [
                XNewOrders::class, // Specify the full-width widget
            ],
        ];
    }
}
