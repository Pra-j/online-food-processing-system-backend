<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'name', 'description', 'price', 'stock', 'is_active', 'food_type', 'course_type', 'media_id'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function media()
    {
        return $this->belongsTo(\App\Models\Media::class, 'media_id');
    }
}
