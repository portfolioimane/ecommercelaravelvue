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
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\PaymentSetting; // Import the PaymentSetting model


class OrderController extends Controller
{
        public function __construct()
    {
        // Initialize the PayPal client with API credentials
        $this->paypal = new PayPalClient;
        $this->paypal->setApiCredentials(config('paypal'));
        $this->paypal->setAccessToken($this->paypal->getAccessToken());
    }
public function myorders(Request $request)
{
    $perPage = $request->input('per_page', 5);
    $ordersQuery = Order::where('user_id', $request->user()->id)
        ->with(['orderItems.product', 'orderItems.variant']);
    
    $orders = $ordersQuery->paginate($perPage);

    return response()->json([
        'orders' => $orders->items(),
        'total' => $orders->total(),
    ], 200);
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

        // Retrieve the Stripe secret key from the database
        $stripeSettings = PaymentSetting::where('provider', 'stripe')->where('enabled', true)->first();

        if (!$stripeSettings || !$stripeSettings->secret_key) {
            return response()->json(['error' => 'Stripe is not configured'], 500);
        }

        // Initialize Stripe with the secret key from the database
        Stripe::setApiKey($stripeSettings->secret_key);

        // Create a PaymentIntent
        $paymentIntent = PaymentIntent::create([
            'amount' => $amountInCents,
            'currency' => 'usd',
            'automatic_payment_methods' => ['enabled' => true],
        ]);

        Log::info('Stripe payment intent created', [
            'payment_intent_id' => $paymentIntent->id,
            'status' => $paymentIntent->status,
        ]);

        // Return client secret
        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ], 200);

    } catch (\Exception $e) {
        Log::error('Error creating Stripe payment intent', ['error' => $e->getMessage()]);
        return response()->json(['error' => 'Failed to create payment intent'], 500);
    }
}

   
public function confirmPaypalPayment(Request $request)
{
    Log::info('Confirm PayPal Payment API called', ['request' => $request->all()]);

    $paypalOrderId = $request->paypalOrderId;

    if (!$paypalOrderId) {
        Log::error('PayPal Order ID is missing in the request.');
        return response()->json(['error' => 'PayPal Order ID is required'], 400);
    }

    try {
        // Fetch PayPal order details
        $orderDetails = $this->paypal->showOrderDetails($paypalOrderId);
        Log::info('Order details fetched from PayPal.', ['orderDetails' => $orderDetails]);

        if (isset($orderDetails['status']) && $orderDetails['status'] === 'COMPLETED') {
            Log::info('PayPal order completed successfully.', ['paypalOrderId' => $paypalOrderId]);
            return response()->json(['status' => 'COMPLETED']);
        }

        Log::warning('PayPal order not completed.', ['orderDetails' => $orderDetails]);
        return response()->json(['status' => 'NOT_COMPLETED'], 400);
    } catch (\Exception $e) {
        Log::error('Error while confirming PayPal payment.', [
            'paypalOrderId' => $paypalOrderId,
            'exception' => $e->getMessage(),
        ]);
        return response()->json(['error' => 'Failed to confirm PayPal payment'], 500);
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
                'variant_id' => $item['variant_id'],
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
        $order = Order::with([
        'orderItems.product',          // Load the related product for each cart item
        'orderItems.variant'           // Load the associated variant for each cart item
         ])->find($id);


        if ($order) {
            return response()->json($order, 200);
        }

       
        // Return a 404 response if the order is not found
        return response()->json(['message' => 'Order not found'], 404);
    }
}
