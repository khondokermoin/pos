<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use App\Traits\LogActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory, HasCompanyScope, LogActivity;

    protected array $logOnly = ['status', 'total_amount'];

    protected $fillable = [
        'company_id',
        'branch_id',
        'supplier_id',
        'user_id',
        'purchase_date',
        'total_amount',
        'status'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}
