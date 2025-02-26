<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price', 'stock', 'sku' ,'image', 'status'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_category');
    }
}
