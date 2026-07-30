<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory, HasCompanyScope, LogActivity;

    protected array $logExclude = ['image'];

    protected $fillable = [
        'company_id',
        'category_id',
        'name',
        'description',
        'image',
        'has_variants',
        'is_bulk',
        'is_active',
    ];

    protected $casts = [
        'has_variants' => 'boolean',
        'is_bulk'      => 'boolean',
        'is_active'    => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
