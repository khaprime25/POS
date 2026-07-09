<?php

namespace App\Models;

use App\Models\Sales;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'product_variant_id',
        'product_name',
        'variant_name',
        'price',
        'quantity',
        'subtotal',
        'modifiers',
    ];

    protected $casts = [
        'modifiers' => 'array',
    ];

    public function sale()
    {
        return $this->belongsTo(
            Sales::class,
            'sale_id'
        );
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
