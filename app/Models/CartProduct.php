<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartProduct extends Model
{
    use HasFactory;
    public $fillable = ['id', 'product_name', 'product_unit_count','unit_price','total_price', 'cart_id'];
    public $hidden = ['created_at', 'updated_at'];

    public function carts()
    {
        return $this->belongsTo(Cart::class,"cart_id");
    }

    public function settotalPriceAttribute($value)
    {
        $this->attributes['total_price'] = $this->attributes["unit_price"]*$this->attributes["product_unit_count"];
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
