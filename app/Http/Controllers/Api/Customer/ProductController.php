<?php
namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Fetch all products with their associated category
    public function index()
    {
        // Eager load the category relationship
        $products = Product::with('category')->get();

        return response()->json($products, 200);
    }

    // Fetch a single product by ID with its associated category
    public function show($id)
    {
        $product = Product::with('category')->find($id);

        if ($product) {
            return response()->json($product, 200);
        }

        return response()->json(['message' => 'Product not found'], 404);
    }
}
