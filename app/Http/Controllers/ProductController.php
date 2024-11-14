<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductDetailResource;

class ProductController extends Controller
{
    // Get All Products
    public function index()
    {
        $products = Product::with('category')->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Products retrieved successfully',
            'data' => ProductResource::collection($products),
        ], 200);
    }

    // Get Product Details by ID
    public function productDetail($id)
    {
        $product = Product::with('category:id,name,description')->find($id);

        if (!$product) {
            return response()->json([
                'status' => 'error',
                'message' => 'Product not found',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Product detail retrieved successfully',
            'data' => new ProductDetailResource($product),
        ], 200);
    }
}
