<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GovernmentService extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'icon', 'icon_bg_start', 'icon_bg_end',
        'image', 'content', 'phone', 'email', 'address', 'map_url',
        'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function booted()
    {
        static::creating(function ($service) {
            if (empty($service->slug)) {
                $service->slug = Str::slug($service->title);
            }
        });
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function items()
    {
        return $this->hasMany(GovernmentServiceItem::class)->orderBy('sort_order');
    }

    public function activeItems()
    {
        return $this->items()->where('is_active', true);
    }
}
