<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HeldOrder extends Model
{
    protected $fillable = [
        'company_id',
        'branch_id',
        'user_id',
        'customer_id',
        'label',
        'items',
        'discount',
    ];

    protected $casts = [
        'items'    => 'array',
        'discount' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
