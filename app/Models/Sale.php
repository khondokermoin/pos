<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasCompanyScope, LogActivity;

    // Only log the most meaningful fields for sales audit
    protected array $logOnly = ['status', 'total_amount', 'payment_method', 'discount', 'received_amount'];

    protected $fillable = ['company_id', 'branch_id', 'customer_id', 'user_id', 'invoice_no', 'subtotal', 'discount', 'total_amount', 'received_amount', 'payment_method', 'status'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
