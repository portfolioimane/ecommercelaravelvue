<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CartController extends Controller
{

public function viewAuthenticatedCart(Request $request)
{
    // Fetch the cart for the authenticated user
    $cart = $this->getCart($request, 'authenticated');

    // Log the response for debugging
    Log::info('Authenticated Cart Response: ', ['cart' => $cart]);

    return response()->json($cart, 200);
}

public function viewGuestCart(Request $request)
{
    // Fetch the cart for the guest user
    $cart = $this->getCart($request, 'guest');

    // Log the response for debugging
    Log::info('Guest Cart Response: ', ['cart' => $cart]);

    return response()->json($cart, 200);
}

private function getCart(Request $request, $type)
{
    // Log when the method is called
    Log::info('Cart view method called', ['type' => $type]);

    // Log the Authorization header to check if token/session is passed
    Log::info('Authorization header: ' . $request->header('Authorization'));

    $cart = null;

    if ($type === 'authenticated' && auth()->check()) {
        // Log user ID when authenticated
        Log::info('Authenticated User ID: ' . auth()->user()->id);

        // Fetch the cart for the authenticated user
        $cart = Cart::where('user_id', auth()->user()->id)
                    ->with('cartItems.product')
                    ->first(); // A user should only have one cart

        // Log if a cart is found or not
        if ($cart) {
            Log::info('Cart found for authenticated user: ' . auth()->user()->id, ['cart' => $cart]);
        } else {
            Log::info('No cart found for authenticated user: ' . auth()->user()->id);
        }
    } elseif ($type === 'guest') {
        // If not authenticated, log that the user is not authenticated
        Log::info('User is not authenticated');

        // Fetch the session_id for guest users from headers
        $sessionId = $request->header('X-Session-ID'); // Get session_id from headers

        if ($sessionId) {
            // Retrieve the cart for the guest using session_id
            $cart = Cart::where('session_id', $sessionId)
                        ->with('cartItems.product')
                        ->first(); // A guest should also have one cart

            // Log if a cart is found or not
            if ($cart) {
                Log::info('Cart found for guest with session_id: ' . $sessionId, ['cart' => $cart]);
            } else {
                Log::info('No cart found for guest with session_id: ' . $sessionId);
            }
        } else {
            // Log an error if no session_id is provided for a guest
            Log::warning('No session_id provided for guest.');
            return response()->json(['message' => 'Session ID is required for guest users.'], 400);
        }
    }

    // Log the final cart object before returning
    Log::info('Returning cart: ', ['cart' => $cart]);

    return $cart;
}



public function addToAuthenticatedCart(Request $request)
{
    // Validate the input data
    $data = $request->validate([
        'product_id' => 'required|integer',
        'quantity' => 'required|integer|min:1',
    ]);

    // Retrieve the cart for the authenticated user
    $cart = Cart::firstOrCreate(
        ['user_id' => auth()->user()->id]
    );

    return $this->addToCart($cart, $data);
}

public function addToGuestCart(Request $request)
{
    // Log the incoming headers to check what is being sent
    \Log::debug('Incoming headers: ', $request->headers->all());

    // Retrieve session_id from the request headers
    $sessionId = $request->header('X-Session-ID');

    // Validate the input data
    $data = $request->validate([
        'product_id' => 'required|integer',
        'quantity' => 'required|integer|min:1',
    ]);

    // Ensure session_id is provided, if not, return an error
    if (!$sessionId) {
        return response()->json(['error' => 'Session ID is required for guest users'], 400);
    }

    // Retrieve the cart for the guest user using session_id
    $cart = Cart::firstOrCreate(
        ['session_id' => $sessionId]
    );

    // Add the product to the cart (custom logic in the addToCart method)
    return $this->addToCart($cart, $data);
}


private function addToCart($cart, $data)
{
    // Find existing cart item or create a new one
    $cartItem = CartItem::where('cart_id', $cart->id)
                        ->where('product_id', $data['product_id'])
                        ->first();

    if ($cartItem) {
        // If cart item exists, update the quantity
        $cartItem->update([
            'quantity' => $cartItem->quantity + $data['quantity']
        ]);
    } else {
        // Otherwise, create a new cart item
        $cartItem = CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity']
        ]);
    }

    // Return the response with the added product details
    return response()->json([
        'message' => 'Product added to cart',
        'cartItem' => $cartItem
    ]);
}



    public function remove(Request $request)
    {
        $item = CartItem::find($request->item_id);
        if ($item) {
            $item->delete();
            return response()->json(['message' => 'Item removed from cart']);
        }

        return response()->json(['message' => 'Item not found'], 404);
    }

    public function update(Request $request)
    {
        $item = CartItem::find($request->item_id);
        if ($item) {
            $item->update(['quantity' => $request->quantity]);
            return response()->json(['message' => 'Cart item updated']);
        }

        return response()->json(['message' => 'Cart item not found'], 404);
    }
}
