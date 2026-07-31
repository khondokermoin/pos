<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'subject',
        'body',
        'variables',
        'is_active',
    ];

    protected $casts = [
        'variables' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->name);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Render the email body by replacing {{variable}} placeholders with actual values.
     *
     * Supports three placeholder styles:
     *   {{key}}   — double-brace no-space (used by seeder & service)
     *   {{ key }} — double-brace with spaces (Blade-style)
     *   {key}     — single-brace (legacy / custom templates)
     *
     * All values are cast to string to prevent str_replace TypeError when
     * a caller accidentally passes an array or object as a data value.
     */
    public function render(array $data): string
    {
        $body = (string) ($this->body ?? '');

        foreach ($data as $key => $value) {
            // Cast to string — prevents TypeError if value is array/object/null
            $safeValue = is_array($value) || is_object($value)
                ? json_encode($value)
                : (string) ($value ?? '');

            $body = str_replace('{{' . $key . '}}',   $safeValue, $body);
            $body = str_replace('{{ ' . $key . ' }}', $safeValue, $body);
            $body = str_replace('{' . $key . '}',     $safeValue, $body);
        }

        return $body;
    }

    /**
     * Render the subject line with variable substitution.
     *
     * Same placeholder styles as render(). Values are cast to string for safety.
     */
    public function renderSubject(array $data): string
    {
        $subject = (string) ($this->subject ?? '');

        foreach ($data as $key => $value) {
            $safeValue = is_array($value) || is_object($value)
                ? json_encode($value)
                : (string) ($value ?? '');

            $subject = str_replace('{{' . $key . '}}',   $safeValue, $subject);
            $subject = str_replace('{{ ' . $key . ' }}', $safeValue, $subject);
            $subject = str_replace('{' . $key . '}',     $safeValue, $subject);
        }

        return $subject;
    }

    public static function findBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->where('is_active', true)->first();
    }
}
