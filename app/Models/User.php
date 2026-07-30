<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'company_id', // Tenant ID
        'branch_id',  // Branch ID
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ==========================================
    // 1. Relationships (SaaS Architecture)
    // ==========================================

    /**
     * Get the company that owns the user.
     * (Super Admin will have null here, which is expected)
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Get the branch that owns the user.
     * (Super Admin & Company Admin might have null here)
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // ==========================================
    // 2. Query Scopes (For cleaner controllers)
    // ==========================================

    /**
     * Scope a query to only include users of a specific company.
     */
    public function scopeOfCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope a query to only include users of a specific branch.
     */
    public function scopeOfBranch($query, int $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    // ==========================================
    // 3. Helper Methods (For Blade & Logic)
    // ==========================================

    /**
     * Check if the user is a Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('Super Admin');
    }

    /**
     * Check if the user is a Company Admin
     */
    public function isCompanyAdmin(): bool
    {
        return $this->hasRole('Company Admin');
    }

    /**
     * Check if the user belongs to any company (Not a Super Admin)
     */
    public function isTenantUser(): bool
    {
        return !is_null($this->company_id);
    }
}