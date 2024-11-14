<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->orderItems->first()->product->name ?? 'No Products',
            'status' => new StatusResource($this->status),
            'total_price' => $this->total_price,
            'order_items' => OrderItemResource::collection($this->orderItems),
            'payment' => $this->payment ? new PaymentResource($this->payment) : null,
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}
