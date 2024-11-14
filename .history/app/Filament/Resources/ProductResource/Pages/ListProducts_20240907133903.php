

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
                ->url(static::getResource()::getPages()['create'])
                ->icon('heroicon-o-plus'),

            ButtonAction::make('grid')
                ->label('Product Grid')
                ->url(static::getResource()::getPages()['grid'])
                ->icon('heroicon-o-view-grid'),
        ];
    }
}
