<?php

namespace App\Models;

use App\Models\User;
use App\Models\SaleItem;
use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'subtotal',
        'discount_amount',
        'tax_amount',
        'grand_total',
        'payment_status',
        'order_status',
        'payment_method',
        'cash_received',
        'change_given',
        'sale_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
