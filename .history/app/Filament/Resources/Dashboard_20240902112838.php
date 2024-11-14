<?php

namespace App\Filament\Resources\DasboardResource\Widgets;

use Filament\Widgets\Widget;

class Dashboard extends Widget
{
    protected static string $view = 'filament.widgets.dashboard';

    protected function getWidgets(): array
    {
        return [
            UserCount::class, // Add the UserCount widget
        ];
    }
}
