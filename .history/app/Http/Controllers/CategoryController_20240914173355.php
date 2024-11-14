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
}
