<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(){
        // Mengambil semua data kategori
        $categories = Product::with(Category::all();

        // Mengembalikan data kategori dalam format JSON
        return response()->json([
            'data' => $categories
        ], 200);
    }
}