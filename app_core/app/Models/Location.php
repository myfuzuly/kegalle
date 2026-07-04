<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['parent_id', 'name', 'slug', 'type', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(Location::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Location::class, 'parent_id')->orderBy('sort_order')->orderBy('name');
    }

    public function listings()
    {
        return $this->hasMany(Listing::class, 'location_id');
    }
}
