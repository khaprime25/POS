<?php

namespace App\Models;

use App\Models\Product;
use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'name',
        'price',
        'cost_price',
        'stock',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }
}
