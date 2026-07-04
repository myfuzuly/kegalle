<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    protected $fillable = [
        'user_id', 'created_by_admin_id', 'store_id', 'category_id', 'location_id',
        'ad_type', 'type', 'title', 'slug', 'description', 'price', 'currency',
        'condition', 'location', 'status', 'is_featured', 'is_top', 'is_urgent',
        'bumped_at', 'expires_at', 'views',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_top' => 'boolean',
    ];

    public function images()
    {
        return $this->hasMany(ListingImage::class, 'listing_id');
    }

    public function variants()
    {
        return $this->hasMany(ListingVariant::class, 'listing_id')->orderBy('sort_order');
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

    public function reviews()
    {
        return $this->hasMany(Review::class);
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

    protected static function booted()
    {
        static::created(function (Listing $listing) {
            $isClassified = strtolower((string) ($listing->type ?? '')) === 'classified';
            AdminNotification::log(
                $isClassified ? 'classified_created' : 'listing_created',
                $isClassified ? 'New classified ad posted' : 'New listing posted',
                $listing->title,
                '/admin/listings/'.$listing->id.'/edit'
            );
        });
    }
}
