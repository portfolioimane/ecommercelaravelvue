<?php
namespace App\Http\Controllers\Api\Backend;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrdersController extends Controller
{
    /**
     * Get all orders.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        Log::info('Fetching all orders'); // Log entry

        try {
            $orders = Order::with('orderItems')->get(); // Fetch orders with their items
            Log::info('Orders fetched successfully', ['orders_count' => $orders->count()]); // Log success
        } catch (\Exception $e) {
            Log::error('Error fetching orders: ' . $e->getMessage()); // Log error
            return response()->json(['error' => 'Unable to fetch orders'], 500);
        }

        return response()->json(['orders' => $orders], 200);
    }

    /**
     * Get details of a specific order.
     *
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
public function show($orderId)
{
    Log::info('Fetching order details', ['order_id' => $orderId]); // Log entry

    try {
        // Eager load OrderItems and their associated Product model
        $order = Order::with('orderItems.product')->find($orderId); // Load related product for each order item

        if (!$order) {
            Log::warning('Order not found', ['order_id' => $orderId]); // Log warning
            return response()->json(['error' => 'Order not found'], 404);
        }

        Log::info('Order found', ['order_id' => $orderId, 'order' => $order]); // Log success
    } catch (\Exception $e) {
        Log::error('Error fetching order: ' . $e->getMessage()); // Log error
        return response()->json(['error' => 'Unable to fetch order'], 500);
    }

    return response()->json(['order' => $order], 200);
}


    /**
     * Update the status of an order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $orderId
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateStatus(Request $request, $orderId)
    {
        Log::info('Updating order status', ['order_id' => $orderId, 'status' => $request->status]); // Log entry

        $request->validate([
            'status' => 'required|in:pending,completed,cancelled', // Validate the status value
        ]);

        try {
            $order = Order::find($orderId);

            if (!$order) {
                Log::warning('Order not found for update', ['order_id' => $orderId]); // Log warning
                return response()->json(['error' => 'Order not found'], 404);
            }

            $order->status = $request->status;
            $order->save();

            Log::info('Order status updated successfully', ['order_id' => $orderId, 'new_status' => $request->status]); // Log success
        } catch (\Exception $e) {
            Log::error('Error updating order status: ' . $e->getMessage()); // Log error
            return response()->json(['error' => 'Unable to update order status'], 500);
        }

        return response()->json(['order' => $order], 200);
    }
}
