<?php

namespace App\Models;

use App\Models\Category;
use App\Models\ProductVariant;
use App\Models\ProductModifier;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'image',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function modifiers()
    {
        return $this->hasMany(ProductModifier::class);
    }
}
