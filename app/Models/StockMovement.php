<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = ['company_id', 'branch_id', 'variant_id', 'type', 'quantity', 'reference_type', 'reference_id', 'user_id'];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function reference()
    {
        return $this->morphTo();
    }
}
