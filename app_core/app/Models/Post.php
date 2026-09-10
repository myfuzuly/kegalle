<?php

namespace App\Models;

use App\Support\HtmlSanitizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $fillable = [
        'title', 'meta_title', 'slug', 'image', 'excerpt',
        'meta_description', 'body', 'is_published', 'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished($query)
    {
        return $query->where('is_published', 1);
    }

    // Always sanitize body on read — protects against any pre-sanitizer DB content
    public function getBodyAttribute(?string $value): ?string
    {
        return HtmlSanitizer::clean($value);
    }
}
