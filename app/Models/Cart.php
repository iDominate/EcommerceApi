<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;
    public $fillable = ['name_of_owner','email_of_owner','number_of_products','number_of_items','user_id'];
    public $hidden = ['created_at','updated_at'];

    public function normalUsers()
    {
        return $this->belongsTo(NormalUser::class, "user_id");
    }

    public function cartProducts()
    {
        return $this->hasMany(CartProduct::class,'cart_id');
    }


    
}
