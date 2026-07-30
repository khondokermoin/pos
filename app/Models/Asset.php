<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'company_id',
        'asset_type_id',
        'name',
        'purchase_date',
        'purchase_price',
        'current_value',
        'status',
        'notes',
    ];

    protected $casts = [
        'purchase_date'  => 'date',
        'purchase_price' => 'decimal:2',
        'current_value'  => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function type()
    {
        return $this->belongsTo(AssetType::class, 'asset_type_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'disposed'          => 'danger',
            'under_maintenance' => 'warning',
            default             => 'success',
        };
    }
}
