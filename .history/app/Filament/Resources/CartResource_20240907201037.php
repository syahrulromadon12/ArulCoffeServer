<?php

namespace App\Filament\Resources;

use App\Models\Cart;
use App\Filament\Resources\CartResource\Pages;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;

class CartResource extends Resource
{
    protected static ?string $model = Cart::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'Cart';
    protected static ?string $navigationGroup = 'Products';

    public static function form(Forms\Form $form): Forms\Form // Perbaiki namespace Form di sini
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
    return $table
        ->query(fn ($query) => $query->where('user_id', auth()->id())) // Filter berdasarkan user yang sedang login
        ->columns([
            Tables\Columns\TextColumn::make('user.name')
                ->label('Pengguna'),
            Tables\Columns\ImageColumn::make('product.image')
                ->label('Image')
                ->disk('public'),
            Tables\Columns\TextColumn::make('product.name')
                ->label('Produk')
                ->sortable(),
            Tables\Columns\TextColumn::make('quantity')->label('Quantity'),
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
        ]);
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
                // Misalnya, simpan data transaksi dan redirect ke Midtrans
                $cart->status = 'pending'; // Status sementara sebelum pembayaran selesai
            }

            $cart->save();
        }

        // Redirect atau tampilkan pesan sukses
        return Redirect::route('filament.resources.carts.index')
            ->with('success', 'Selected carts have been purchased.');
    }

}
