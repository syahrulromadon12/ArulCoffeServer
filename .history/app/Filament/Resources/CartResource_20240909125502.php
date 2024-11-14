<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Cart;
use Filament\Tables;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\CartResource\Pages;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Cart';
    protected static ?string $navigationGroup = 'Products';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        $userId = Auth::id();

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('product.image')
                    ->label('Image')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity'),
                Tables\Columns\TextColumn::make('product.price')
                    ->label('Price')
                    ->money('idr', true), // Format harga satuan dalam Rupiah (IDR)

                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total Price')
                    ->getStateUsing(function ($record) {
                        return $record->product->price * $record->quantity;
                    })
                    ->money('idr', true), // Format total harga dalam Rupiah (IDR)
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\BulkAction::make('buy')
                    ->icon('heroicon-o-banknotes')
                    ->label('Buy Selected')
                    ->action(fn (array $records) => $this->redirectToCheckout($records)) // Redirect to Checkout
                    ->color('success'),
            ])
            ->query(Cart::forUser($userId)); // Filter berdasarkan user_id
    }

    public function redirectToCheckout(array $records)
    {
        return redirect()->route('checkout', ['selected_products' => $records]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarts::route('/'),
            'create' => Pages\CreateCart::route('/create'),
            'edit' => Pages\EditCart::route('/{record}/edit'),
        ];
    }
}
