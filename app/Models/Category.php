<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public $fillable = ['name'];
    public $hidden = ['created_by', 'number_of_products', 'created_at','updated_at'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}
