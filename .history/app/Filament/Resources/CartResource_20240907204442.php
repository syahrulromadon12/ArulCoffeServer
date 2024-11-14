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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna'),
                Tables\Columns\ImageColumn::make('product.image')
                    ->label('Image')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Produk')
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Quantity'),
                Tables\Columns\TextColumn::make('product.price')
                    ->label('Harga Satuan')
                    ->money('idr', true), // Format harga satuan dalam Rupiah (IDR)

                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total Harga')
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
                    ->action('processBulkPurchase')
                    ->color('success'),
            ])
            ->query(Cart::forUser($userId)); // Filter berdasarkan user_id
    }

    public static function getTableQuery(): \Illuminate\Database\Eloquent\Builder
    {
        // Ambil user_id dari pengguna yang sedang login
        $userId = auth()->id();

        // Filter keranjang berdasarkan user_id
        return parent::getTableQuery()->where('user_id', $userId);
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

    public static function processBulkPurchase(array $data)
    {
        $cartIds = $data['records']; // Ambil ID produk yang dipilih

        $carts = Cart::whereIn('id', $cartIds)->get();

        // Jika tidak ada produk yang dipilih, kembalikan pesan kesalahan
        if ($carts->isEmpty()) {
            return Redirect::back()->with('error', 'No products selected.');
        }

        // Proses pembayaran
        foreach ($carts as $cart) {
            // Tentukan metode pembayaran
            $paymentMethod = 'cash'; // Gantilah dengan input dari pengguna

            if ($paymentMethod === 'cash') {
                $cart->status = 'unpaid'; // Atur status pembayaran untuk cash
            } else {
                // Implementasi e-payment menggunakan Midtrans
                $cart->status = 'pending'; // Status sementara sebelum pembayaran selesai
            }

            $cart->save();
        }

        // Redirect atau tampilkan pesan sukses
        return Redirect::route('filament.resources.carts.index')
            ->with('success', 'Selected carts have been purchased.');
    }
}
