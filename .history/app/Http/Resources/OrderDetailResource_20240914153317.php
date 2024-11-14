<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->orderItems->first()->product->name ?? 'No Products',
            'status' => [
                'id' => $this->status->id ?? null,
                'name' => $this->status->name ?? 'Unknown Status',
                'image' => $this->status->image ?? null,
            ],
            'total_price' => $this->total_price,
            'created_at' => $this->created_at->toIso8601String(),
            'order_items' => OrderItemResource::collection($this->orderItems),
            'payment' => new PaymentResource($this->payment),
        ];
    }
}
