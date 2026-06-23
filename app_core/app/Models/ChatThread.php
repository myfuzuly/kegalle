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

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
