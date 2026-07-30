<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['company_id', 'name', 'short_code', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function company() {
        return $this->belongsTo(Company::class);
    }
}