<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $firstProductName = $this->orderItems->first()->product->name ?? 'Your Orders';

        return [
            "id" => $this->id,
            "title" => $firstProductName,
            "status" => $this->status,
            "total_price" => $this->total_price,
            "created_at" => $this->created_at,
        ];
    }
}
