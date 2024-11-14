<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Pages\Actions\ButtonAction;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ButtonAction::make('create')
                ->label('New Product')
                ->url('/admin/products/create') // Adjust path as necessary
                ->icon('heroicon-o-plus'),

            ButtonAction::make('grid')
                ->label('Product Grid')
                ->url('/admin/products/grid') ,
        ];
    }
}
