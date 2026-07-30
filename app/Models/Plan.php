<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
        'price',
        'trial_days',
        'user_limit',
        'branch_limit',
        'features',
        'status',
        'billing_cycle', // ✅ Added: ফর্ম এবং কন্ট্রোলারে এটি ব্যবহার করা হয়েছে
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'features' => 'array',      // ✅ পারফেক্ট: এটি অটোমেটিক JSON কে Array তে কনভার্ট করবে
        'price' => 'decimal:2',     // ✅ পারফেক্ট: দামের ডেসিমাল ফরম্যাট ঠিক রাখবে
        // Note: 'is_active' বাদ দেওয়া হয়েছে যাতে 'status' কলামের সাথে কনফ্লিক্ট না হয়।
    ];

    // ==========================================
    // Scopes
    // ==========================================

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active');
        // অথবা: return $query->where('is_active', true); (যদি আপনি is_active কলাম রাখতেই চান)
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', 'inactive');
    }

    // ==========================================
    // Helper Methods
    // ==========================================

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isInactive(): bool
    {
        return $this->status === 'inactive';
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    // ==========================================
    // Relationships
    // ==========================================

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }
}