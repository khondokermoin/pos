<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'body',
        'audience',
        'is_active',
        'published_at',
        'expires_at',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'published_at' => 'datetime',
        'expires_at'   => 'datetime',
    ];

    public function reads()
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    public function readByUser(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }
}
