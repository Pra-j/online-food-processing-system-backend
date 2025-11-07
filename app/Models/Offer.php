<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'product_id',  //Will be nullable for global offers.
        'offer_kind',
        'value',
        'buy_quantity',
        'get_quantity',
        'start_date',
        'end_date',
        'is_active'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
