<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Definisi relasi Many-to-One dengan Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Definisi relasi Many-to-One dengan Product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}