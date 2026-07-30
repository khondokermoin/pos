<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ProductVariant extends Model
{
    use HasFactory;

    protected $table = 'product_variants';

    protected $fillable = [
        'product_id',
        'sku',
        'barcode',
        'name',
        'unit_id',
        'tax_id',
        'cost_price',
        'selling_price',
        'reorder_level',
        'attributes',
        'is_active',
    ];

    protected $casts = [
        'cost_price'    => 'decimal:2',
        'selling_price' => 'decimal:2',
        'is_active'     => 'boolean',
        'attributes'    => 'array',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function stock(): HasOne
    {
        return $this->hasOne(Stock::class, 'variant_id', 'id');
    }

    public function stockForBranch(int $branchId): HasOne
    {
        return $this->hasOne(Stock::class, 'variant_id', 'id')
            ->where('branch_id', $branchId);
    }

    /**
     * HasMany relation used for eager-loading branch-specific stock.
     * We use hasMany so it returns a Collection (compatible with ->with() constraints).
     * Usage: ->with(['branchStock' => fn($q) => $q->where('branch_id', $id)])
     *        then: $variant->branchStock->first()
     */
    public function branchStock(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Stock::class, 'variant_id', 'id');
    }
}
