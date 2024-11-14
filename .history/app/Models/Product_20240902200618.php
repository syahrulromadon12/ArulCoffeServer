<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'email',
        'password',
        'role_id',
    ];
    
    // Definisi relasi Many-to-One dengan Category
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Definisi relasi Many-to-Many dengan Order melalui OrderItem
    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items')->withPivot('quantity', 'price');
    }
}
