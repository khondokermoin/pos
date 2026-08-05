<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasCompanyScope;

    protected $guarded = [];

    protected $casts = [
        'coverage_areas'        => 'array',
        'is_default'            => 'boolean',
        'accepts_online_orders' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Online orders that were routed to this branch.
     */
    public function onlineOrders()
    {
        return $this->hasMany(Sale::class, 'routed_branch_id')
            ->where('order_type', 'online');
    }
}
