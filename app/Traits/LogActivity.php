<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

/**
 * LogActivity Trait
 *
 * Drop this trait onto any Eloquent model to get automatic audit logging
 * for created / updated / deleted events.
 *
 * Usage:
 *   use App\Traits\LogActivity;
 *   class Sale extends Model { use LogActivity; }
 *
 * Opt-out specific attributes from diff logging:
 *   protected array $logExclude = ['updated_at', 'remember_token'];
 *
 * Opt-in only specific attributes:
 *   protected array $logOnly = ['status', 'total_amount'];
 */
trait LogActivity
{
    /**
     * Boot the trait — register Eloquent event listeners.
     */
    public static function bootLogActivity(): void
    {
        static::created(function (Model $model) {
            static::writeLog('created', $model, [], $model->getAttributes());
        });

        static::updated(function (Model $model) {
            $dirty = $model->getDirty();
            $old   = [];
            $new   = [];

            foreach ($dirty as $key => $newValue) {
                if (static::shouldLogAttribute($model, $key)) {
                    $old[$key] = $model->getOriginal($key);
                    $new[$key] = $newValue;
                }
            }

            if (! empty($new)) {
                static::writeLog('updated', $model, $old, $new);
            }
        });

        static::deleted(function (Model $model) {
            static::writeLog('deleted', $model, $model->getAttributes(), []);
        });
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private static function writeLog(string $action, Model $model, array $old, array $new): void
    {
        // Never log inside a seeder / artisan migrate to avoid noise
        if (app()->runningInConsole()) {
            return;
        }

        try {
            $shortName   = class_basename($model);
            $description = match ($action) {
                'created' => "{$shortName} #{$model->getKey()} created.",
                'updated' => "{$shortName} #{$model->getKey()} updated.",
                'deleted' => "{$shortName} #{$model->getKey()} deleted.",
                default   => "{$shortName} #{$model->getKey()} {$action}.",
            };

            ActivityLog::record($action, $description, $model, $old, $new);
        } catch (\Throwable) {
            // Never let audit logging break the main request
        }
    }

    private static function shouldLogAttribute(Model $model, string $key): bool
    {
        // Always skip these noisy / sensitive columns
        $globalExclude = ['updated_at', 'created_at', 'remember_token', 'password', 'two_factor_secret'];

        if (in_array($key, $globalExclude, true)) {
            return false;
        }

        // Model-level opt-in list
        if (! empty($model->logOnly)) {
            return in_array($key, $model->logOnly, true);
        }

        // Model-level opt-out list
        if (! empty($model->logExclude)) {
            return ! in_array($key, $model->logExclude, true);
        }

        return true;
    }
}
