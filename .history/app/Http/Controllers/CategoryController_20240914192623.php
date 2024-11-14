<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Resources\CategoryResource;

class CategoryController extends Controller
{
    public function index()
    {
        // Mengambil semua data kategori
        $categories = Category::all();

        // Mengembalikan data kategori dalam format JSON dengan standar API
        return response()->json([
            'status' => 'success',
            'message' => 'Categories retrieved successfully',
            'data' => CategoryResource::collection($categories)
        ], 200);
    }

    public function getProductCategories($id)
    {
        // Mengambil kategori berdasarkan ID beserta produk
        $category = Category::with('products')->find($id);

        // Cek apakah kategori ada
        if (!$category) {
            return response()->json([
                'status' => 'error',
                'message' => 'Category not found',
                'data' => null
            ], 404);
        }

        // Mengembalikan data kategori dan produk dengan format JSON standar
        return response()->json([
            'status' => 'success',
            'message' => 'Category and products retrieved successfully',
            'data' => [
                'category' => new CategoryResource($category),
                'products' => ProductResource::collection($category->products)
            ]
        ], 200);
    }
}
