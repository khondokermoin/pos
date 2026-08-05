<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'email',
        'phone',
        'contact_person',
        'website',
        'address',
        'city',
        'country',
        'zip_code',
        'logo',
        'favicon',
        'theme_settings',
        'social_links',
        'contact_info',
        'subdomain',
        'custom_domain',
        'currency',
        'timezone',
        'settings',
        'status',
        'trial_ends_at',
        'plan_id',
        'user_id',
        'business_type_id'
    ];

    protected $casts = [
        'settings' => 'array',
        'theme_settings' => 'array',
        'social_links' => 'array',
        'contact_info' => 'array',
        'trial_ends_at' => 'datetime',
    ];

    // ==========================================
    // Relationships (রিলেশনশিপ সমূহ)
    // ==========================================

    // ১. কোম্পানির মালিক (Owner)
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ২. কোম্পানির অ্যাডমিন (Alias)
    public function admin()
    {
        return $this->owner();
    }

    // ৩. কোম্পানির অধীনে থাকা সব ইউজার (users relationship)
    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    // ৪. কোম্পানির বর্তমান সাবস্ক্রিপশন প্ল্যান
    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    // ৫. কোম্পানির সাবস্ক্রিপশন হিস্টরি (সর্বশেষ সাবস্ক্রিপশন)
    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    // ৬. কোম্পানির সব পেমেন্ট ট্রানজেকশন হিস্টরি
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // ৭. কোম্পানির অধীনে থাকা সব ব্রাঞ্চ
    public function branches()
    {
        return $this->hasMany(Branch::class);
    }
    public function businessType()
    {
        return $this->belongsTo(BusinessType::class);
    }

    // ৮. কোম্পানির সব প্রোডাক্ট
    public function products()
    {
        return $this->hasMany(\App\Models\Product::class);
    }

    // ৯. কোম্পানির সব ক্যাটাগরি
    public function categories()
    {
        return $this->hasMany(\App\Models\Category::class);
    }

    // ১০. কোম্পানির সব সেল
    public function sales()
    {
        return $this->hasMany(\App\Models\Sale::class);
    }

    // ১১. কোম্পানির এনাবেলড মডিউলস (Module Mapping)
    public function modules()
    {
        return $this->belongsToMany(BusinessModule::class, 'company_module')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }

    public function enabledModules()
    {
        return $this->modules()->wherePivot('is_enabled', true);
    }

    public function hasModule(string $slug): bool
    {
        // Core modules are always accessible
        if (BusinessModule::where('slug', $slug)->where('is_core', true)->exists()) {
            return true;
        }
        return $this->enabledModules()->where('slug', $slug)->exists();
    }
}
