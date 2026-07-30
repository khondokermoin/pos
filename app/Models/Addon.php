<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addon extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'price',
        'version',
        'is_installed',
        'is_active',
    ];

    protected $casts = [
        'is_installed' => 'boolean',
        'is_active'    => 'boolean',
        'price'        => 'decimal:2',
    ];

    // মার্কেটপ্লেসে দেখানোর জন্য (যেগুলো এখনো install করা হয়নি)
    public function scopeNotInstalled($query)
    {
        return $query->where('is_installed', false);
    }
}