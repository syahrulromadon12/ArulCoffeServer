<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Product';

    protected static ?string $navigationGroup = 'Products';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Produk'),

                Forms\Components\Textarea::make('description')
                    ->columnSpanFull()
                    ->label('Deskripsi Produk'),

                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->prefix('IDR')
                    ->label('Harga'),

                Forms\Components\FileUpload::make('image')
                    ->required()
                    ->disk('public')
                    ->directory('product-images')
                    ->label('Gambar Produk')
                    ->acceptedFileTypes(['image/jpeg', 'image/jpg', 'image/png']) // Hanya menerima jpg, jpeg, dan png
                    ->maxSize(2048) // Batasan ukuran maksimal 2MB (2048 KB)
                    ->image() // Menambahkan validasi bahwa file harus berupa gambar
                    ->enableReordering(), // Jika ingin mengizinkan re-ordering gambar (opsional)

                Forms\Components\Select::make('category_id')
                    ->relationship('category', 'name')
                    ->required()
                    ->label('Kategori'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->searchable(),

                Tables\Columns\ImageColumn::make('image')
                    ->disk('public') // Gunakan disk yang sudah dikonfigurasi
                    ->sortable(),

                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(function ($state) {
                        $locale = 'id_ID'; // Locale Indonesia
                        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
                        return $formatter->formatCurrency($state, 'IDR');
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
            'grid' => Pages\ProductGrid::route('/grid'),
        ];
    }
}
