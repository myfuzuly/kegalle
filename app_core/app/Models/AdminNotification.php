<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = ['type', 'title', 'message', 'link', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    public static function log(string $type, string $title, ?string $message = null, ?string $link = null): void
    {
        try {
            static::create([
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'is_read' => false,
            ]);
        } catch (\Throwable $e) {
            // Table may not exist yet on a fresh deploy before migration runs — never break the triggering action.
        }
    }
}
