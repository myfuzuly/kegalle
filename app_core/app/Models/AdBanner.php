<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdBanner extends Model
{
    protected $fillable = [
        'title', 'image', 'link_url', 'location', 'sort_order',
        'is_active', 'starts_at', 'ends_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'starts_at' => 'date',
        'ends_at' => 'date',
    ];

    public const LOCATIONS = [
        'home_top' => 'Homepage — Top Banner (1200×120)',
        'home_sidebar' => 'Homepage — Sidebar Box (300×250)',
        'listings_sidebar' => 'Listings / Classified — Sidebar (300×250)',
        'category_page' => 'Category Page — Sidebar (300×250)',
        'blog_sidebar' => 'Blog — Sidebar (300×250)',
    ];

    public function scopeActiveForLocation($query, string $location)
    {
        return $query->where('location', $location)
            ->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', now());
            })
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }
}
