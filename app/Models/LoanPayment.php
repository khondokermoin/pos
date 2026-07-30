<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    use HasCompanyScope;

    protected $fillable = ['company_id', 'loan_id', 'amount', 'payment_date', 'notes'];

    protected $casts = [
        'amount'       => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
