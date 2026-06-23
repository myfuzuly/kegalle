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
        'opening_hours',
        'status',
        'is_featured',
        'membership_plan_id',
        'membership_expires_at',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_featured' => 'boolean',
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

    public function scopeApproved($query)
    {
        return $query->whereIn('status', ['approved', 'active', 'published']);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
