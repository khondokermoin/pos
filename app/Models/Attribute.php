<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasCompanyScope;

    protected $fillable = ['company_id', 'name'];

    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}
