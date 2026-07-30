<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id',
        'variant_id',
        'product_name',
        'quantity',
        'unit_price',
        'subtotal',
        'tax_rate',
        'tax_amount',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
