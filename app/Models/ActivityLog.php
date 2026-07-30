<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'user_role',
        'company_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Record an activity log entry.
     */
    public static function record(
        string $action,
        string $description,
        ?Model $model = null,
        array $oldValues = [],
        array $newValues = []
    ): self {
        $user = Auth::user();

        return static::create([
            'user_id'    => $user?->id,
            'user_name'  => $user?->name,
            'user_role'  => $user?->getRoleNames()->first(),
            'company_id' => $user?->company_id,
            'action'     => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id'   => $model?->getKey(),
            'description' => $description,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }

    public function scopeForCompany($query, int $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    public function getActionBadgeAttribute(): string
    {
        return match ($this->action) {
            'created'  => 'success',
            'updated'  => 'warning',
            'deleted'  => 'danger',
            'login'    => 'info',
            'logout'   => 'secondary',
            default    => 'primary',
        };
    }

    public function getModelShortNameAttribute(): string
    {
        if (!$this->model_type) {
            return 'System';
        }
        $parts = explode('\\', $this->model_type);
        return end($parts);
    }
}
