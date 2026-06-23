<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_top' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(ListingImage::class, 'listing_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function locationModel()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', 1);
    }

    public function scopeTop($query)
    {
        return $query->where('is_top', 1);
    }
    public function values()
{
    return $this->hasMany(ListingFieldValue::class, 'listing_id');
}
}
