<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Storage;

class NormalUser extends Authenticatable implements JWTSubject
{
    use HasFactory;
    public $fillable = ['name', 'email', 'password', 'address',"image"];
    public $hidden = ['created_at','updated_at'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function carts()
    {
        return $this->hasOne(Cart::class, "user_id");
    }

    public function orders()
    {
        return $this->hasOne(Order::class);
    }

    public function getImageAttribute($value)
    {
        return asset('images/default_image.jpg');
        

    }
}
