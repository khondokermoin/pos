<?php

namespace App\Models;

use App\Models\Traits\HasCompanyScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatConversation extends Model
{
    use HasFactory, HasCompanyScope;

    protected $fillable = [
        'company_id',
        'uuid',
        'visitor_name',
        'visitor_phone',
        'status',
        'assigned_user_id',
        'assigned_at',
        'unread_count',
        'last_message_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'last_message_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
