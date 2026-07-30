<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'company_id',
        'department_id',
        'name',
        'email',
        'phone',
        'designation',
        'join_date',
        'salary',
        'status',
    ];

    protected $casts = [
        'join_date' => 'date',
        'salary'    => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function increments()
    {
        return $this->hasMany(SalaryIncrement::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'inactive'   => 'warning',
            'terminated' => 'danger',
            default      => 'success',
        };
    }
}
