<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasCompanyScope, LogActivity;

    protected array $logOnly = ['status', 'total_amount', 'payment_method', 'discount', 'received_amount'];

    protected $fillable = [
        'company_id',
        'branch_id',
        'customer_id',
        'user_id',
        'invoice_no',
        'subtotal',
        'discount',
        'total_amount',
        'received_amount',
        'payment_method',
        'status',
        // Online order fields
        'order_type',
        'delivery_city',
        'delivery_district',
        'delivery_area',
        'delivery_address',
        'customer_lat',
        'customer_lng',
        'routed_branch_id',
        'routing_method',
        'routing_distance_km',
        'customer_name',
        'customer_phone',
        'customer_email',
        'notes',
    ];

    protected $casts = [
        'customer_lat'        => 'float',
        'customer_lng'        => 'float',
        'routing_distance_km' => 'float',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function routedBranch()
    {
        return $this->belongsTo(Branch::class, 'routed_branch_id');
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

    public function scopeOnlineOrders($query)
    {
        return $query->where('order_type', 'online');
    }

    public function scopePosOrders($query)
    {
        return $query->where('order_type', 'pos');
    }
}
