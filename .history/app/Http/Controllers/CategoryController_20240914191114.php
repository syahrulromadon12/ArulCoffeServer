<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index(){
        // Mengambil semua data kategori
        $categories = Category::all();

        // Mengembalikan data kategori dalam format JSON
        return CategoryResource::collection($categories);
    }

    public function getProductCategories($id)
    {
        // Mengambil kategori berdasarkan ID
        $category = Category::with('products')->find($id);

        // Cek apakah kategori ada
        if (!$category) {
            return response()->json([
                'message' => 'Category not found'
            ], 404);
        }

        // Mengembalikan data produk yang ada dalam kategori
        return response()->json([
            'category' => new CategoryResource($category),
            'products' => ProductResource::collection($category->products)
        ]);
    }
}
