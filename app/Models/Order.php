<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    public $fillable = ['id', "name_of_owner", "email_of_owner","user_id","number_of_products","number_of_items","total_price", "status", "order_date"];

    public function purchased_products()
    {
        return $this->hasMany(PurchasedProduct::class, "order_id");
    }

    public function normal_users()
    {
        return $this->belongsTo(NormalUser::class, "user_id");
    }

    
    
}
