<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $filable = [
        "user_id",
        "status"
    ];

    // Definisi relasi Many-to-One dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Definisi relasi One-to-Many dengan OrderItem
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Definisi relasi One-to-One dengan Payment
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

     // Definisi relasi Many-to-One dengan Status
     public function status()
     {
         return $this->belongsTo(Status::class);
     }
}
