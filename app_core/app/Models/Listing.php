<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{
    use HasFactory;

    const LIVE_STATUSES = ['approved', 'active', 'available'];
    const STATUS_ACTIVE = 'active';

    protected $fillable = [
        'user_id', 'created_by_admin_id', 'store_id', 'category_id', 'location_id',
        'ad_type', 'type', 'title', 'slug', 'description', 'description_si', 'description_ta', 'price', 'currency',
        'condition', 'location', 'status',
        'bumped_at', 'expires_at', 'views', 'payment_methods',
        'poster_name', 'poster_phone', 'poster_whatsapp',
        'stock',
    ];

    protected $casts = [
        'is_featured'     => 'boolean',
        'is_top'          => 'boolean',
        'is_urgent'       => 'boolean',
        'payment_methods' => 'array',
        'expires_at'      => 'datetime',
        'bumped_at'       => 'datetime',
    ];

    public function offers()
    {
        return $this->hasMany(\App\Models\Offer::class);
    }

    public function images()
    {
        return $this->hasMany(ListingImage::class, 'listing_id')->orderByDesc('is_primary')->orderBy('sort_order');
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
        return $query->whereIn('status', self::LIVE_STATUSES);
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('status', self::LIVE_STATUSES);
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
