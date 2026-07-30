<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class InvoiceTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'html_content',
        'settings',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'settings'   => 'array',
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });

        // Only one default at a time
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

    public function getTypeLabel(): string
    {
        return match ($this->type) {
            'pos'     => 'POS / Thermal',
            'a4'      => 'A4 Paper',
            'thermal' => 'Thermal 80mm',
            default   => ucfirst($this->type),
        };
    }
}
