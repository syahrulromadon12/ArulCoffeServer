<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use App\Filament\Widgets\Revenue;
use App\Filament\Widgets\UserCount;
use App\Filament\Widgets\RecentOrders;
use App\Filament\Widgets\XNewOrders;
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

    public function getWidgets(): array
    {
        return [
            UserCount::class,
            Revenue::class,
            RecentOrders::class,
            XNewOrders::class, // Full-width widget
        ];
    }

    public function render()
    {
        return view('filament.pages.dashboard', [
            'widgets' => [
                'top' => [
                    UserCount::class,
                    Revenue::class,
                    RecentOrders::class,
                ],
                'bottom' => [
                    XNewOrders::class,
                ],
            ],
        ]);
    }
}