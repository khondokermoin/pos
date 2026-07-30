<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'company_id',
        'employee_id',
        'month',
        'basic_salary',
        'bonus',
        'deduction',
        'net_salary',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'basic_salary' => 'decimal:2',
        'bonus'        => 'decimal:2',
        'deduction'    => 'decimal:2',
        'net_salary'   => 'decimal:2',
        'paid_at'      => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status === 'paid' ? 'success' : 'warning';
    }
}
