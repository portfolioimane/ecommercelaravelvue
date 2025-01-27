<?php
// app/Http/Controllers/Admin/ReviewController.php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    // Fetch the latest three featured reviews with the user and product relationships
    public function latestFeaturedReviews()
    {
        // Fetch only featured reviews, ordered by the most recent, with user and product relationships eager-loaded
        $reviews = Review::with(['user', 'product']) // Eager load user and product relationships
                         ->where('is_featured', true) // Only get featured reviews
                         ->latest() // Order by created_at in descending order (latest)
                         ->take(3) // Limit to the latest three reviews
                         ->get();

        // Return the reviews as JSON
        return response()->json($reviews);
    }

    // Fetch reviews for a specific product
    public function index($productId)
    {
        // Fetch only approved reviews for the given productId
        $reviews = Review::with('user', 'product') // Eager load the user and product relationships
                         ->where('product_id', $productId)
                         ->where('status', 'approved') // Filter reviews by approved status
                         ->get();

        // Return reviews as JSON
        return response()->json($reviews);
    }

    // Submit a review for a specific product
    public function store(Request $request, $productId)
    {
        $validatedData = $request->validate([
            'review' => 'required|string',
            'rating' => 'required|integer|between:1,5',
        ]);

        Log::info('Review validation passed.', ['validated_data' => $validatedData]);

        // Find the product
        try {
            $product = Product::findOrFail($productId);
            Log::info('Product found.', ['product_id' => $productId]);
        } catch (\Exception $e) {
            Log::error('Product not found.', ['product_id' => $productId, 'error' => $e->getMessage()]);
            return response()->json(['error' => 'Product not found.'], 404);
        }

        // Create the new review
        $review = new Review([
            'review' => $request->review,
            'rating' => $request->rating,
            'user_id' => auth()->id(), // Assuming the user is authenticated
        ]);

        Log::info('Review created.', [
            'user_id' => auth()->id(),
            'review' => $request->review,
            'rating' => $request->rating
        ]);

        // Save the review and associate it with the product via the relationship
        try {
            $product->reviews()->save($review);
            Log::info('Review saved and associated with the product.', ['product_id' => $productId, 'review_id' => $review->id]);
        } catch (\Exception $e) {
            Log::error('Failed to save review.', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to save review.'], 500);
        }

        // Eager load the user and product relationships
        $review = $review->load('user', 'product');  // Load both 'user' and 'product' relationships

        // Return the review with associated user and product
        return response()->json($review, 201);
    }
}
