<?php
namespace App\Http\Controllers\Api\Backend;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ProductsController extends Controller
{
    public function index()
    {
        // Fetch all products along with their category
        $products = Product::with('category')->get();
        Log::info('Fetched all products', ['products' => $products]);
        return response()->json($products);
    }

public function store(Request $request)
{
    // Validate and create a new product
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric',
        'stock' => 'required|integer',
        'category_id' => 'required|exists:categories,id',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($validator->fails()) {
        Log::error('Validation failed', ['errors' => $validator->errors()]);
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $data = $request->all();

    // Store image if uploaded
    if ($request->hasFile('image')) {
        $path = $request->file('image')->store('images/products', 'public');
        // Prepend 'storage/' to the image path
        $data['image'] = 'storage/' . $path;
        Log::info('Image uploaded', ['path' => $path]);
    }

    $product = Product::create($data);
    Log::info('New product created', ['product' => $product]);
    return response()->json($product, 201);
}


    public function show($id)
    {
        // Fetch a single product by ID along with its category
        $product = Product::with('category')->findOrFail($id);
        Log::info('Fetched product by ID', ['product' => $product]);
        return response()->json($product);
    }

 public function update(Request $request, $id)
{
    // Validate and update a product
    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'sometimes|required|numeric',
        'stock' => 'sometimes|required|integer',
        'category_id' => 'sometimes|required|exists:categories,id',
        'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    if ($validator->fails()) {
        Log::error('Validation failed on update', ['errors' => $validator->errors()]);
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $product = Product::findOrFail($id);
    $data = $request->all();

    // Store new image if uploaded, and delete the old one
    if ($request->hasFile('image')) {
        if ($product->image) {
            Storage::delete('public/' . $product->image);
        }
        $path = $request->file('image')->store('images/products', 'public');
        // Prepend 'storage/' to the new image path
        $data['image'] = 'storage/' . $path;
    }

    $product->update($data);
    Log::info('Product updated', ['product' => $product]);
    return response()->json($product);
}


    public function destroy($id)
    {
        // Delete product and image if exists
        $product = Product::findOrFail($id);
        if ($product->image) {
            Storage::delete('public/' . $product->image);
        }
        $product->delete();
        Log::info('Product deleted', ['product_id' => $id]);
        return response()->json(null, 204);
    }

public function toggleFeatured($productId)
    {
        // Find the product by ID
        $product = Product::findOrFail($productId);

        // Toggle the featured status
        $product->featured = !$product->featured;

        // Save the product with the updated featured status
        $product->save();

        // Return the updated product as a JSON response
        return response()->json($product);
    }
}
