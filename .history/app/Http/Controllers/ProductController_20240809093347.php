<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductDetailResource;

class ProductController extends Controller
{
    public function index(){
        $products = Product::with('category')->get();
        return ProductResource::collection($products);
    }

    public function ProductDeatil($id){
        $product = Product::with('category:id,name,description')->findOrFail($id);
        return new ProductDetailResource($product);
    }
}
