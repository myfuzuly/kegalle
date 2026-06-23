<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatThread extends Model
{
    protected $fillable = ['listing_id', 'buyer_id', 'seller_id', 'status'];

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'thread_id');
    }
}
