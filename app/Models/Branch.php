<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasCompanyScope;

    // সব কলাম ফিলাবেল রাখার জন্য guarded empty রাখা হয়েছে, যা ঠিক আছে
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * ✅ এই নতুন মেথডটি যুক্ত করুন
     * এটি branches টেবিলের manager_id কলামকে users টেবিলের id এর সাথে ম্যাপ করবে
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
}