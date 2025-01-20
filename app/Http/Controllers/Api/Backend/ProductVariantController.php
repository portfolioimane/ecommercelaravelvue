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
