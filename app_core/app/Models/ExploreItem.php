<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExploreItem extends Model
{
    protected $fillable = [
        'title', 'slug', 'description', 'icon', 'image', 'gradient_start', 'gradient_end',
        'items', 'content', 'link_url', 'link_label', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getItemListAttribute(): array
    {
        return array_values(array_filter(array_map('trim', explode("\n", $this->items ?? ''))));
    }

    public function subItems()
    {
        return $this->hasMany(ExploreSubItem::class)->orderBy('sort_order');
    }

    public function activeSubItems()
    {
        return $this->subItems()->where('is_active', true);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
