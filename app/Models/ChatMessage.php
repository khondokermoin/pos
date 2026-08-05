<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'chat_conversation_id',
        'sender_type',
        'sender_user_id',
        'message',
    ];

    public function chatConversation()
    {
        return $this->belongsTo(ChatConversation::class);
    }

    public function senderUser()
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}
