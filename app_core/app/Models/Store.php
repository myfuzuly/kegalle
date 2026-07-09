<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'logo',
        'banner',
        'description',
        'email',
        'phone',
        'whatsapp',
        'address',
        'city',
        'latitude',
        'longitude',
        'opening_hours',
        'status',
        'is_featured',
        'is_verified',
        'membership_plan_id',
        'membership_expires_at',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'membership_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_store');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    public function getAverageRatingAttribute()
    {
        return round($this->approvedReviews()->avg('rating') ?? 0, 1);
    }

    public function getRankAttribute()
    {
        $listingsCount = $this->listings_count ?? $this->listings()->count();
        $avgRating = $this->average_rating;
        $reviewsCount = $this->approved_reviews_count ?? $this->approvedReviews()->count();

        if ($listingsCount >= 100 && $avgRating >= 4.5 && $reviewsCount >= 20) return 'platinum';
        if ($listingsCount >= 50 && $avgRating >= 4.0 && $reviewsCount >= 10) return 'gold';
        if ($listingsCount >= 10 && $avgRating >= 3.0 && $reviewsCount >= 3) return 'silver';
        return 'bronze';
    }

    public function getRankLabelAttribute()
    {
        return match($this->rank) {
            'platinum' => '💎 Platinum Seller',
            'gold' => '🥇 Gold Seller',
            'silver' => '🥈 Silver Seller',
            default => '🥉 New Seller',
        };
    }

    public function getRankColorAttribute()
    {
        return match($this->rank) {
            'platinum' => '#7c3aed',
            'gold' => '#d97706',
            'silver' => '#6b7280',
            default => '#b45309',
        };
    }

    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'active', 'published']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    protected static function booted()
    {
        static::created(function (Store $store) {
            AdminNotification::log(
                'store_created',
                'New store registered',
                $store->name,
                '/admin/stores'
            );
        });
    }
}
