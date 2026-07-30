<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory, HasCompanyScope, LogActivity;

    protected $fillable = [
        'company_id',
        'name',
        'phone',
        'email',
        'address',
        'is_walk_in',
    ];

    protected $casts = [
        'is_walk_in' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
