<?php
namespace App\Http\Controllers\Api\Backend;

use App\Http\Controllers\Controller;
use App\Models\VariantCombination;  // It's still VariantCombination as per database, but we'll use productvariant in API
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import the Log facade

class ProductVariantController extends Controller
{
    // Retrieve all product variants for a given product
    public function index($productId)
    {
        Log::debug("Fetching product variants for product ID: {$productId}"); // Log product ID

        $productVariants = VariantCombination::where('product_id', $productId)->get();

        Log::debug("Fetched variants: ", $productVariants->toArray()); // Log the fetched variants

        return response()->json($productVariants);
    }

    // Update a product variant
    public function update(Request $request, $id)
    {
        Log::debug("Updating product variant with ID: {$id}"); // Log the variant ID

        // Validate incoming data (optional, but good practice)
        $validated = $request->validate([
            'combination_values' => 'required|json',
            'price' => 'required|numeric',
            'image' => 'nullable|string'
        ]);

        Log::debug("Validated data: ", $validated); // Log the validated input data

        $productVariant = VariantCombination::findOrFail($id);

        // Log current data before update
        Log::debug("Current product variant data: ", $productVariant->toArray());

        $productVariant->combination_values = $validated['combination_values'];
        $productVariant->price = $validated['price'];
        $productVariant->image = $validated['image'];
        $productVariant->save();

        Log::debug("Updated product variant data: ", $productVariant->toArray()); // Log updated data

        return response()->json($productVariant);
    }

    // Delete a product variant
    public function destroy($id)
    {
        Log::debug("Deleting product variant with ID: {$id}"); // Log the variant ID

        $productVariant = VariantCombination::findOrFail($id);
        $productVariant->delete();

        Log::debug("Deleted product variant with ID: {$id}"); // Log that deletion was successful

        return response()->json(['message' => 'Product variant deleted successfully']);
    }
}
