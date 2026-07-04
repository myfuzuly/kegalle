<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deal extends Model
{
    protected $fillable = [
        'listing_id', 'store_id', 'user_id',
        'deal_price', 'original_price', 'discount_percent',
        'starts_at', 'ends_at',
        'status', 'is_flash', 'is_featured',
        'admin_note', 'sold_count', 'stock_qty',
    ];

    protected $casts = [
        'deal_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_flash' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'approved')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFlash($query)
    {
        return $query->where('is_flash', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function isActive(): bool
    {
        return $this->status === 'approved'
            && $this->starts_at <= now()
            && $this->ends_at >= now();
    }

    public function isExpired(): bool
    {
        return $this->ends_at < now();
    }
}
