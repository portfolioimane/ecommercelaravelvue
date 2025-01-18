<?php
namespace App\Http\Controllers\Api\Backend;

use App\Http\Controllers\Controller;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VariantController extends Controller
{
    /**
     * Fetch a single variant by its ID.
     */
    public function show($id)
    {
        // Log the received variant ID for debugging
        Log::info('Fetching variant with ID: ' . $id);

        // Fetch the variant by its ID
        $variant = Variant::find($id);

        // Check if the variant exists
        if (!$variant) {
            // Log the warning if the variant is not found
            Log::warning('Variant not found with ID: ' . $id);
            return response()->json(['message' => 'Variant not found'], 404);
        }

        // Log the retrieved variant data for debugging
        Log::info('Variant found: ', ['variant' => $variant]);

        // Return the variant data as a JSON response
        return response()->json($variant);
    }

    /**
     * Fetch all variants with their related variant values.
     */
    public function index()
    {
        // Eager load the variantValues relationship
        $variants = Variant::with('variantValues')->get();

        // Return the variants as a JSON response
        return response()->json($variants);
    }

    /**
     * Store a new variant.
     */
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:color,label', // Ensure 'type' is either 'color' or 'label'
        ]);

        // Create the variant using validated data
        $variant = Variant::create($validatedData);

        // Log the creation of the new variant for debugging
        Log::info('Variant created successfully: ', ['variant' => $variant]);

        // Return the created variant as a JSON response with status code 201 (Created)
        return response()->json($variant, 201);
    }

    public function destroy($id)
    {
        // Log the received variant ID for debugging
        Log::info('Attempting to delete variant with ID: ' . $id);

        // Find the variant by its ID
        $variant = Variant::find($id);

        // Check if the variant exists
        if (!$variant) {
            // Log the warning if the variant is not found
            Log::warning('Variant not found with ID: ' . $id);
            return response()->json(['message' => 'Variant not found'], 404);
        }

        // Delete the variant
        $variant->delete();

        // Log the successful deletion for debugging
        Log::info('Variant deleted successfully: ', ['variant_id' => $id]);

        // Return a success response
        return response()->json(['message' => 'Variant deleted successfully'], 200);
    }
}
