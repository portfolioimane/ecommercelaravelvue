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

        $validated = $request->validate([
            'combinations' => 'required|array',
            'combinations.*.product_id' => 'required|exists:products,id',
            'combinations.*.combination_values' => 'required|array',
            'combinations.*.price' => 'nullable|numeric',
            'combinations.*.image' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
        ]);

        Log::debug('Validated data', ['validated_combinations' => $validated['combinations']]);

        foreach ($validated['combinations'] as $index => $combinationData) {
            Log::debug('Processing combination', ['index' => $index, 'combination' => $combinationData]);

            $imagePath = null;
            if (isset($combinationData['image'])) {
                Log::debug('Uploading image for combination', ['image_data' => $combinationData['image']]);
                $imagePath = $combinationData['image']->store('variant_images', 'public');
                Log::debug('Image uploaded', ['image_path' => $imagePath]);
            }

            // Update or create the variant combination
            $updatedCombination = VariantCombination::updateOrCreate(
                [
                    'product_id' => $combinationData['product_id'],
                    'combination_values' => $combinationData['combination_values'],
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
