<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class SalaryIncrement extends Model
{
    use HasCompanyScope;

    protected $fillable = ['company_id', 'employee_id', 'amount', 'effective_date', 'reason'];

    protected $casts = [
        'amount'         => 'decimal:2',
        'effective_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
