<?php

namespace App\Models;

use Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $fillable = ['id','name','rate','unit_price', 'in_stock','sold','total_profit', 'remaining_inventory_in_aggergate', 'category_id'];

    public $hidden = ['created_at', 'updated_at'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function getNameAttribute($value)
    {
        $arr = explode("_", $value);
        
        return join(" ", $arr);
    }

    public function setNameAttribute($value)
    {
        $name = $value;
        $arr = explode(" ", $name);
        $this->attributes['name'] = join("_", $arr);
    }
}
