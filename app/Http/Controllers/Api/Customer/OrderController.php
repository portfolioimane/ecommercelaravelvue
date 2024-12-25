<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CartItem; // Assuming CartItem exists for referencing cart items
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Add the Auth facade
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class OrderController extends Controller
{
    public function myorders(Request $request)
{
    // Retrieve all orders for the authenticated user along with their order items and associated products
    $orders = Order::where('user_id', $request->user()->id)  // Filter by the authenticated user's ID
        ->with('orderItems.product')  // Include related order items and their products
        ->get();  // Retrieve all matching orders

    return response()->json($orders, 200);  // Return the orders as a JSON response
}


    public function index(Request $request)
    {
        // Retrieve all orders for the authenticated user along with their order items and associated products
        $orders = Order::where('user_id', $request->user()->id)
            ->with('orderItems.product')
            ->get();

        return response()->json($orders, 200);
    }

    public function createStripePayment(Request $request)
    {
        try {
            $totalAmount = $request->input('total');
            $amountInCents = $totalAmount * 100; // Convert to cents

            // Initialize Stripe with your secret key
           Stripe::setApiKey(config('services.stripe.secret'));

            // Create a PaymentIntent without confirming it
            $paymentIntent = PaymentIntent::create([
                'amount' => $amountInCents,
                'currency' => 'usd', // Adjust currency as needed
                'automatic_payment_methods' => ['enabled' => true],
            ]);

            Log::info('Stripe payment intent created', ['payment_intent_id' => $paymentIntent->id, 'status' => $paymentIntent->status]);

            // Return the client secret to the frontend for further processing
            return response()->json([
                'clientSecret' => $paymentIntent->client_secret,
            ], 200);

        } catch (\Exception $e) {
            Log::error('Error creating Stripe payment intent', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to create payment intent'], 500);
        }
    }

    
    public function create(Request $request)
    {
        // Validate the incoming data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'payment_method' => 'required|string',
            'items' => 'required|array',
            'total' => 'required|numeric',
        ]);

        // Check if the cart is empty before proceeding
        if (count($validated['items']) === 0) {
            return response()->json(['message' => 'Your cart is empty. Add items before placing an order.'], 400);
        }

        // Create the order
        $order = Order::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'payment_method' => $validated['payment_method'],
            'total' => $validated['total'],
        ]);

        // Loop through cart items and create a relation with the order
        foreach ($validated['items'] as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }

        // Optional: Empty the user's cart if you're using CartItem model
        // This assumes you have a CartItem model that is linked to the user
        Auth::user()->cart()->delete(); // Ensure cartItems() is defined in the User model

        // Return a response indicating that the order was placed successfully
    return response()->json([
        'message' => 'Order placed successfully!',
        'order' => $order,
    ], 201);    
}

    public function show($id)
    {
        // Retrieve the order by its ID, including its items and associated products
        $order = Order::with('orderItems.product')->find($id);


        if ($order) {
            return response()->json($order, 200);
        }

       
        // Return a 404 response if the order is not found
        return response()->json(['message' => 'Order not found'], 404);
    }
}
