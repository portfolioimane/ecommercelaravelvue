<?php

namespace App\Http\Controllers\Api\Backend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoriesController extends Controller
{
    public function index()
    {
        // Fetch all categories
        return response()->json(Category::all());
    }

    public function store(Request $request)
    {
        // Validate and create a new category
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:categories,name', // Ensure name is unique
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $category = Category::create($request->all());
        return response()->json($category, 201);
    }

    public function show($id)
    {
        // Fetch a single category by ID
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function destroy($id)
    {
        // Delete a category
        $category = Category::findOrFail($id);
        $category->delete();
        return response()->json(['message' => 'Category deleted successfully']);
    }
}
