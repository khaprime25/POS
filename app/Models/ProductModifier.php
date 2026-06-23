<?php

namespace App\Models;

use App\Models\Product;
use App\Models\ProductModifierOption;
use Illuminate\Database\Eloquent\Model;

class ProductModifier extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'type',
        'is_required',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function options()
    {
        return $this->hasMany(ProductModifierOption::class);
    }
}
