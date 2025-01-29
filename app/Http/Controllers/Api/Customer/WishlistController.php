<?php

namespace App\Http\Controllers\Api\Customer;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Product;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; // Add the Auth facade


class WishlistController extends Controller
{
    public function index()
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->with('product')->get();
        return response()->json($wishlist);
    }

public function store(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'product_id' => 'required|exists:products,id',
    ]);
            Log::info('Authenticated User ID this is: ' .auth()->user()->id);



    // Add to the wishlist (create or retrieve)
    $wishlist = Wishlist::firstOrCreate([
        'user_id' => Auth::id(),
        'product_id' => $request->product_id,
    ]);

    return response()->json(['message' => 'Added to wishlist', 'wishlist' => $wishlist]);
}

    public function destroy($id)
    {
        $wishlist = Wishlist::where('user_id', Auth::id())->where('product_id', $id)->first();
        if ($wishlist) {
            $wishlist->delete();
            return response()->json(['message' => 'Removed from wishlist']);
        }
        return response()->json(['message' => 'Not found'], 404);
    }
}
