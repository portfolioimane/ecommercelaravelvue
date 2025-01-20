<?php
namespace App\Http\Controllers\Api\Backend;

use App\Http\Controllers\Controller;
use App\Models\VariantCombination;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VariantCombinationController extends Controller
{
public function updateAllCombinations(Request $request)
{
    Log::debug('Received updateAllCombinations request', ['request_data' => $request->all()]);

    // Updated validation rule for combination_values to ensure it's a valid JSON
    $validated = $request->validate([
        'combinations' => 'required|array',
        'combinations.*.product_id' => 'required|exists:products,id',
        'combinations.*.combination_values' => 'required|json',  // Ensure combination_values is a valid JSON
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
            $imagePath = $combinationData['image']->store('variant_images', 'public');
            $imagePath = 'storage/' . basename($imagePath);  // Ensure the path includes 'storage/'
            Log::debug('Image uploaded', ['image_path' => $imagePath]);
        }

        // Update or create the variant combination in the database
        $updatedCombination = VariantCombination::updateOrCreate(
            [
                'product_id' => $combinationData['product_id'],
                'combination_values' => json_decode($combinationData['combination_values'], true), // Decode JSON for storage
            ],
            [
                'price' => $combinationData['price'],
                'image' => $imagePath,
            ]
        );

        Log::debug('Variant combination updated or created', ['updated_combination' => $updatedCombination]);
    }

    Log::debug('Combinations updated successfully.');

    return response()->json(['message' => 'Combinations updated successfully.']);
}


}