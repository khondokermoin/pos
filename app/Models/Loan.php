<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'company_id',
        'loan_authority_id',
        'amount',
        'interest_rate',
        'loan_date',
        'due_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'amount'        => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'loan_date'     => 'date',
        'due_date'      => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function authority()
    {
        return $this->belongsTo(LoanAuthority::class, 'loan_authority_id');
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }

    public function getTotalPaidAttribute(): float
    {
        return $this->payments()->sum('amount');
    }

    public function getBalanceAttribute(): float
    {
        return $this->amount - $this->total_paid;
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid'    => 'success',
            'overdue' => 'danger',
            default   => 'warning',
        };
    }
}
