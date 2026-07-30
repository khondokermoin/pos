<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarcodeSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'barcode_type',
        'width',
        'height',
        'show_text',
        'show_price',
        'show_product_name',
        'show_company_name',
        'labels_per_row',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'show_text'          => 'boolean',
        'show_price'         => 'boolean',
        'show_product_name'  => 'boolean',
        'show_company_name'  => 'boolean',
        'is_default'         => 'boolean',
        'is_active'          => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (self $model) {
            if ($model->is_default) {
                static::where('id', '!=', $model->id)->update(['is_default' => false]);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getDefault(): ?self
    {
        return static::where('is_default', true)->first()
            ?? static::where('is_active', true)->first();
    }

    public function getBarcodeTypeOptions(): array
    {
        return ['CODE128', 'CODE39', 'EAN13', 'QR'];
    }
}
