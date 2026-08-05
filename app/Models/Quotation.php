<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    use HasCompanyScope;

    protected $fillable = [
        'company_id',
        'customer_id',
        'quotation_no',
        'subtotal',
        'discount',
        'total_amount',
        'valid_until',
        'status',
        'converted_to_sale_id',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'subtotal'     => 'decimal:2',
        'discount'     => 'decimal:2',
        'total_amount' => 'decimal:2',
        'valid_until'  => 'date',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function convertedToSale()
    {
        return $this->belongsTo(Sale::class, 'converted_to_sale_id');
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'accepted' => 'success',
            'rejected' => 'danger',
            'expired'  => 'secondary',
            'sent'     => 'info',
            default    => 'warning',
        };
    }
}
