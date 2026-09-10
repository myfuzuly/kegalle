<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserNotification extends Model
{
    protected $fillable = ['user_id', 'type', 'title', 'body', 'url', 'related_id'];

    protected $casts = ['read_at' => 'datetime'];

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public static function send(int $userId, string $type, string $title, string $body = '', string $url = '', ?int $relatedId = null): void
    {
        try {
            static::create([
                'user_id'    => $userId,
                'type'       => $type,
                'title'      => $title,
                'body'       => $body,
                'url'        => $url,
                'related_id' => $relatedId,
            ]);
        } catch (\Throwable) {}
    }
}
