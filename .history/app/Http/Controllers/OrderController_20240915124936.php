<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrdersResource;
use App\Http\Resources\OrderDetailResource;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // Create Order
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,completed,cancelled',
            'total_price' => 'required|numeric',
            'order_items' => 'required|array',
            'order_items.*.product_id' => 'required|exists:products,id',
            'order_items.*.quantity' => 'required|integer|min:1',
            'order_items.*.price' => 'required|numeric',
            'payment.payment_method' => 'required|string',
            'payment.amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'data' => $validator->errors(),
            ], 422);
        }

        // Create a new Order
        $order = Order::create([
            'user_id' => Auth::id(),
            'status' => $request->status,
            'total_price' => $request->total_price,
        ]);

        // Add Order Items
        foreach ($request->order_items as $item) {
            $order->orderItems()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        // Create Payment
        $order->payment()->create([
            'amount' => $request->payment['amount'],
            'payment_method' => $request->payment['payment_method'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Order created successfully',
            'data' => new OrdersResource($order->load('orderItems', 'payment')),
        ], 201);
    }

    // Get Orders
    public function getOrders()
    {
        $orders = Order::with('orderItems.product', 'status')
            ->where('user_id', Auth::id())
            ->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Orders retrieved successfully',
            'data' => OrdersResource::collection($orders),
        ], 200);
    }

    // Get Order Details
    public function getOrderDetails($id)
    {
        $order = Order::with(['orderItems.product', 'payment', 'status'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Order details retrieved successfully',
            'data' => new OrderDetailResource($order),
        ], 200);
    }
}
