<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // রিলেশনশিপ: একটি Brand অনেকগুলো Product-এর হতে পারে
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    // রিলেশনশিপ: একটি Brand একটি Company-র অন্তর্গত
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}