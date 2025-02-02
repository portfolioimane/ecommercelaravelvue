<?php
namespace App\Http\Controllers\Api\Backend;

use App\Http\Controllers\Controller;
use App\Models\VariantCombination;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Import the Log facade
use Illuminate\Support\Facades\Storage;


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

    // Validate incoming data
    $validatedData = $request->validate([
        'price' => 'required|numeric', // Ensure price is required and numeric
    ]);

    // Find the product variant by ID
    $productVariant = VariantCombination::findOrFail($id);

    // If there's an image, handle the file upload
    if ($request->hasFile('image')) {
        // Store the new image in the 'variant_images' folder in the public disk
        $imagePath = $request->file('image')->store('variant_images', 'public');
        
        // Add the 'storage/' prefix to the path
        $imagePath = 'storage/' . $imagePath; 

        Log::debug('Image uploaded', ['image_path' => $imagePath]); // Log the image path

        // Remove the old image if exists
        if ($productVariant->image) {
            Storage::disk('public')->delete($productVariant->image);
        }

        // Add the new image path to the validated data
        $validatedData['image'] = $imagePath;
    }

    // Update the product variant with the validated data
    $productVariant->update($validatedData);

    Log::debug("Updated product variant with ID: {$id}"); // Log that update was successful

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

    public function deleteAllVariants($productId)
    {
        // Find the product by ID
        $product = Product::find($productId);

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }



        try {
            // Delete all product variants associated with the product
            $product->variants()->delete();

      

            return response()->json(['message' => 'All variants have been deleted.'], 200);
        } catch (\Exception $e) {
            // If an error occurs, rollback the transaction
         
            
            return response()->json(['message' => 'An error occurred while deleting variants.', 'error' => $e->getMessage()], 500);
        }
    }
}
