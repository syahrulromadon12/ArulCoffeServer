<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'avatar_id',
        'name',
        'username',
        'email',
        'password',
        'role_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi One-to-Many dengan Order
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Relasi Many-to-One dengan Role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    // Relasi Many-to-One dengan Avatar
    public function avatar()
    {
        return $this->belongsTo(Avatar::class);
    }

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->username)) {
                $user->username = self::generateDefaultUsername($user->name);
            }
        });
    }

    private static function generateDefaultUsername($name)
    {
        $username = strtolower(Str::slug($name . Str::random(4)));

        while (User::where('username', $username)->exists()) {
            $username = strtolower(Str::slug($name . Str::random(4)));
        }

        return $username;
    }
}
