<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class HeroSlide extends Model
{
    protected $fillable = ['image', 'title', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function getImageUrlAttribute(): string
    {
        $img = $this->image;
        if (str_starts_with($img, '/') || str_starts_with($img, 'http')) {
            return $img;
        }
        return Storage::disk('public')->url($img);
    }

    public static function active()
    {
        return static::where('is_active', true)->orderBy('sort_order')->get();
    }
}
