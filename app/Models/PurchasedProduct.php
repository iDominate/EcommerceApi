<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasedProduct extends Model
{
    use HasFactory;

    public $fillable = ['id', 'product_name', 'product_quantity','total_price', 'order_id'];

    public function orders()
    {
        return $this->belongsTo(Order::class,"order_id");
    }


    public function getProductNameAttribute($value)
    {
        $arr = explode("_", $value);
        
        return join(" ", $arr);
    }

    public function setProductNameAttribute($value)
    {
        $name = $value;
        $arr = explode(" ", $name);
        $this->attributes['product_name'] = join("_", $arr);
    }
}
