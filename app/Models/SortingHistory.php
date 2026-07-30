<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SortingHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'bulk_product_id',
        'bulk_product_name',
        'bulk_quantity_received',
        'sorted_items',
        'user_id',
        'sorted_at',
        'reference_number',
        'notes',
    ];

    protected $casts = [
        'sorted_items' => 'array',
        'sorted_at' => 'datetime',
    ];

    // Relationships
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function bulkProduct()
    {
        return $this->belongsTo(Product::class, 'bulk_product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}