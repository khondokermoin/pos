<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalAttribute extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'values',
        'is_active',
    ];

    protected $casts = [
        'values'    => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getValuesListAttribute(): string
    {
        return implode(', ', $this->values ?? []);
    }
}
