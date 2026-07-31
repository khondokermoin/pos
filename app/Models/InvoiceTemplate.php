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

        // Auto-generate slug on create only if not already set by the controller.
        // The controller always sets a timestamped slug, so this is a safety fallback.
        static::creating(function (self $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name) . '-' . time();
            }
        });

        // Enforce single-default constraint.
        // IMPORTANT: Use DB::table() instead of Eloquent to avoid triggering
        // this same observer recursively, which would cause an infinite loop.
        static::saved(function (self $model) {
            if ($model->is_default) {
                // Directly update via query builder — bypasses model events
                static::where('id', '!=', $model->id)
                    ->where('is_default', true)
                    ->update(['is_default' => false]);
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
