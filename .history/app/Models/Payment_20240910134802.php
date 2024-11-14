<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    'order_id',
    'amount',
    'payment_method',
    'payment_status',
];

    // Definisi relasi Many-to-One dengan Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
