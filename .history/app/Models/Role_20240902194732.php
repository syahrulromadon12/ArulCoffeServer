<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
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

    // Relasi One-to-Many dengan User
    public function users()
    {
        return $this->hasMany(User::class);
    }
}
