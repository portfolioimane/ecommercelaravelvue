<?php
namespace App\Http\Controllers\Api\Backend;

use App\Http\Controllers\Controller;
use App\Models\VariantValue;
use Illuminate\Http\Request;

class VariantValueController extends Controller
{
    // Fetch all variant values
    public function index()
    {
        $variantValues = VariantValue::all();
        return response()->json($variantValues);
    }

    // Store a new variant value
    public function store(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:variants,id',
            'value' => 'required|string|max:255',
        ]);

        $variantValue = VariantValue::create($request->all());
        return response()->json($variantValue, 201); // Created response
    }

    /**
     * Delete a variant value by its ID.
     */
    public function destroy($id)
    {
        // Find the variant value by its ID
        $variantValue = VariantValue::find($id);

        // Check if the variant value exists
        if (!$variantValue) {
            return response()->json(['message' => 'Variant Value not found'], 404);
        }

        // Delete the variant value
        $variantValue->delete();

        // Return a success response
        return response()->json(['message' => 'Variant Value deleted successfully'], 200);
    }
}
