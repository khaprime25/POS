<?php

namespace App\Models;

use App\Models\ProductModifier;
use Illuminate\Database\Eloquent\Model;

class ProductModifierOption extends Model
{
    protected $fillable = [
        'product_modifier_id',
        'name',
        'extra_price',
    ];

    public function modifier()
    {
        return $this->belongsTo(ProductModifier::class);
    }
}
