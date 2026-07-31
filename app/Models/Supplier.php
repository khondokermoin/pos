<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory, HasCompanyScope, LogActivity;

    protected $fillable = [
        'company_id',
        'name',
        'phone',
        'email',
        'address',
        'contact_person',
        'notes',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}
