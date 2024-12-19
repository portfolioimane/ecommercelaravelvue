<?php

namespace App\Http\Controllers\Api\Customer;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::where('user_id', $request->user()->id)->with('orderItems.product')->get();
        return response()->json($orders, 200);
    }

    public function create(Request $request)
    {
        $order = Order::create([
            'user_id' => $request->user()->id,
            'status' => 'pending',
            'total' => $request->total,
        ]);

        foreach ($request->cart_items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price']
            ]);
        }

        return response()->json(['message' => 'Order created successfully', 'order' => $order]);
    }

    public function show($order_id)
    {
        $order = Order::with('orderItems.product')->find($order_id);

        if ($order) {
            return response()->json($order, 200);
        }

        return response()->json(['message' => 'Order not found'], 404);
    }
}
