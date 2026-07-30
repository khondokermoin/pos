<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class LoanAuthority extends Model
{
    use HasCompanyScope;

    protected $fillable = ['company_id', 'name', 'contact', 'notes'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function getTotalLoanedAttribute(): float
    {
        return $this->loans()->sum('amount');
    }
}
