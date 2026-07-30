<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'company_id',
        'cash_account_id',
        'type',
        'amount',
        'reference',
        'description',
        'transaction_date',
    ];

    protected $casts = [
        'amount'           => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function account()
    {
        return $this->belongsTo(CashAccount::class, 'cash_account_id');
    }
}
