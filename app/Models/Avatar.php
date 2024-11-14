<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avatar extends Model
{
    use HasFactory;

    // Relasi One-to-Many dengan User
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
