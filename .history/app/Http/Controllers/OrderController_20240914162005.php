<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrdersResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\OrderDetailResource;

class OrderController extends Controller
{
    // Create Order
    public function createOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
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
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create a new Order
        $order = new Order();
        $order->user_id = Auth::id();
        $order->status = $request->status;
        $order->total_price = $request->total_price;
        $order->save();

        // Add Order Items
        foreach ($request->order_items as $item) {
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item['product_id'];
            $orderItem->quantity = $item['quantity'];
            $orderItem->price = $item['price'];
            $orderItem->save();
        }

        // Create Payment
        $payment = new Payment();
        $payment->order_id = $order->id;
        $payment->amount = $request->payment['amount'];
        $payment->payment_method = $request->payment['payment_method'];
        $payment->save();

        return response()->json([
            'message' => 'Order created successfully',
            'order' => new OrdersResource($order->load('orderItems', 'status')),
        ], 201);
    }

    // Get Orders
    public function getOrders()
    {
        $orders = Order::with('orderItems.product', 'status')
            ->where('user_id', Auth::id())
            ->get();

        return OrdersResource::collection($orders);
    }

    // Get Order Details
    public function getOrderDetails($id)
    {
        $order = Order::with(['orderItems.product', 'payment', 'status'])
    ->where('user_id', Auth::id())
    ->findOrFail($id);

\Log::info('Order Data:', $order->toArray());
return new OrderDetailResource($order);

    }
}
