<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatMessage extends Model
{
    protected $fillable = ['thread_id', 'sender_id', 'message', 'attachment_path', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
}
