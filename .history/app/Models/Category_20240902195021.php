<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'avatar_id',
        'name',
        'username',
        'email',
        'password',
        'role_id',
    ];

    // Definisi relasi One-to-Many dengan Product
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
