<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class CashAccount extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'opening_balance',
        'current_balance',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function transfersFrom()
    {
        return $this->hasMany(CashTransfer::class, 'from_account_id');
    }

    public function transfersTo()
    {
        return $this->hasMany(CashTransfer::class, 'to_account_id');
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'bank'           => 'primary',
            'mobile_banking' => 'info',
            default          => 'success',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'bank'           => 'Bank',
            'mobile_banking' => 'Mobile Banking',
            default          => 'Cash',
        };
    }
}
