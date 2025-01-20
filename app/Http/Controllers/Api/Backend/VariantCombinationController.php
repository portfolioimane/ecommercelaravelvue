<?php
namespace App\Http\Controllers\Api\Backend;

use App\Http\Controllers\Controller;
use App\Models\VariantCombination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VariantCombinationController extends Controller
{

public function getExistingCombinationValues($productId)
{
    // Fetch combinations for the specific product
    $combinations = VariantCombination::where('product_id', $productId)->get();

    // Debugging: log the raw combinations fetched from the database
    \Log::debug('Fetched Combinations for Product ID ' . $productId, ['combinations' => $combinations]);

    // Return only the combination_values as an array
    $combinationValues = $combinations->pluck('combination_values')->toArray();

    // Debugging: log the plucked combination values
    \Log::debug('Plucked Combination Values for Product ID ' . $productId, ['combination_values' => $combinationValues]);

    return $combinationValues;
}



public function updateAllCombinations(Request $request)
{
    Log::debug('Received updateAllCombinations request', ['request_data' => $request->all()]);

    // Updated validation rule for combination_values to ensure it's a valid JSON string
    $validated = $request->validate([
        'combinations' => 'required|array',
        'combinations.*.product_id' => 'required|exists:products,id',
        'combinations.*.combination_values' => 'required|string',  // Expecting a JSON string here
        'combinations.*.price' => 'nullable|numeric',
        'combinations.*.image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
    ]);

    Log::debug('Validated data', ['validated_combinations' => $validated['combinations']]);

    foreach ($validated['combinations'] as $index => $combinationData) {
        Log::debug('Processing combination', ['index' => $index, 'combination' => $combinationData]);

        // Initialize imagePath to null
        $imagePath = null;

        // If an image was uploaded, store it and include /storage in the path
        if (isset($combinationData['image'])) {
            Log::debug('Uploading image for combination', ['image_data' => $combinationData['image']]);
            // Store the image in the 'variant_images' folder within the 'public' disk
            $imagePath = $combinationData['image']->store('variant_images', 'public');
            $imagePath = 'storage/' . $imagePath;  // Add the 'storage/' prefix to the path
            Log::debug('Image uploaded', ['image_path' => $imagePath]); // This now includes 'storage/'
        }

        // Decode the JSON string into an array
        $combinationValues = json_decode($combinationData['combination_values'], true);

        // Update or create the variant combination in the database
        $updatedCombination = VariantCombination::updateOrCreate(
            [
                'product_id' => $combinationData['product_id'],
                'combination_values' => $combinationValues, // Store the decoded combination values as JSON
            ],
            [
                'price' => $combinationData['price'],
                'image' => $imagePath,  // Store the full image path with 'storage/' prefix
            ]
        );

        Log::debug('Variant combination updated or created', ['updated_combination' => $updatedCombination]);
    }

    Log::debug('Combinations updated successfully.');

    return response()->json(['message' => 'Combinations updated successfully.']);
}




}