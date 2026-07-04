<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExploreSubItem extends Model
{
    protected $fillable = [
        'explore_item_id', 'name', 'description', 'phone', 'email',
        'address', 'map_url', 'image', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function exploreItem()
    {
        return $this->belongsTo(ExploreItem::class, 'explore_item_id');
    }
}
