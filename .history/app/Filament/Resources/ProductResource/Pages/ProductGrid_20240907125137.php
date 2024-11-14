<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Resources\Pages\Page;

class ProductGrid extends Page
{
    protected static string $resource = ProductResource::class;

    protected static string $view = 'filament.resources.product-resource.pages.product-grid';

    public function getViewData(): array
    {
        // Ambil semua produk dari database dan oper ke view
        return [
            'products' => Product::all(),
        ];
    }
}
