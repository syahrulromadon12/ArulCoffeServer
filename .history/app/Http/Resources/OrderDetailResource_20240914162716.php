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
            'status' => StatusResource
            'total_price' => $this->total_price,
            'order_items' => OrderItemResource::collection($this->orderItems),
            'payment' => new PaymentResource($this->payment),
        ];
    }
}
