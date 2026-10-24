<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
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
        'listings_auto_approve',
    ];

    protected $casts = [
        'opening_hours'           => 'array',
        'is_featured'             => 'boolean',
        'is_verified'             => 'boolean',
        'membership_expires_at'   => 'datetime',
        'listings_auto_approve'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function membershipPlan()
    {
        return $this->belongsTo(MembershipPlan::class);
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

    /** Stats for every store loaded in this request, fetched together on first use (was 3 queries per card). */
    private static array $statsCache = [];
    private static array $seenIds = [];

    private function stats(): array
    {
        if (! isset(self::$statsCache[$this->id])) {
            $ids = array_keys(array_diff_key(self::$seenIds + [$this->id => true], self::$statsCache));
            $listings = Listing::whereIn('store_id', $ids)->groupBy('store_id')
                ->selectRaw('store_id, count(*) as n')->pluck('n', 'store_id');
            $reviews = Review::whereIn('store_id', $ids)->where('status', 'approved')->groupBy('store_id')
                ->selectRaw('store_id, count(*) as n, avg(rating) as avg')->get()->keyBy('store_id');
            foreach ($ids as $id) {
                self::$statsCache[$id] = [
                    'listings' => (int) ($listings[$id] ?? 0),
                    'reviews'  => (int) ($reviews[$id]->n ?? 0),
                    'rating'   => round((float) ($reviews[$id]->avg ?? 0), 1),
                ];
            }
        }
        return self::$statsCache[$this->id];
    }

    public function getAverageRatingAttribute()
    {
        return $this->stats()['rating'];
    }

    public function getRankAttribute()
    {
        $listingsCount = $this->listings_count ?? $this->stats()['listings'];
        $avgRating = $this->average_rating;
        $reviewsCount = $this->approved_reviews_count ?? $this->stats()['reviews'];

        if ($listingsCount >= 20 && $avgRating >= 4.5 && $reviewsCount >= 10) return 'platinum';
        if ($listingsCount >= 10 && $avgRating >= 4.0 && $reviewsCount >= 5)  return 'gold';
        if ($listingsCount >= 3  && $avgRating >= 3.0 && $reviewsCount >= 2)  return 'silver';
        return 'bronze';
    }

    public function getRankLabelAttribute()
    {
        return match($this->rank) {
            'platinum' => '💎 Platinum Seller',
            'gold' => '🥇 Gold Seller',
            'silver' => '🥈 Silver Seller',
            default => null,
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

    /**
     * "kegalle Verified" badge:
     * owner has verified phone + ≥3 approved reviews + store ≥30 days old
     */
    public function getIsKurulaVerifiedAttribute(): bool
    {
        if ($this->is_verified) return true; // admin can force-grant

        $ownerVerified = (bool) optional($this->user)->phone_verified_at;
        $enoughReviews = $this->stats()['reviews'] >= 3;
        $oldEnough     = $this->created_at && $this->created_at->diffInDays(now()) >= 30;

        return $ownerVerified && $enoughReviews && $oldEnough;
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
        static::retrieved(function (Store $store) {
            if (count(self::$seenIds) > 2000) { // long-running workers: don't grow forever or serve stale stats
                self::$seenIds = self::$statsCache = [];
            }
            self::$seenIds[$store->id] = true;
        });

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
