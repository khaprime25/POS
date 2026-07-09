<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductModifierOption;
use Illuminate\Database\Eloquent\Model;

class ProductModifier extends Model
{
    protected $fillable = [
        'product_id',
        'title',
        'option',
        'extra_charge',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
