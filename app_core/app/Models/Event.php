<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'event_date' => 'date',
        'is_featured' => 'boolean',
        'is_free' => 'boolean',
    ];

    public function scopePublished($q) { return $q->where('status', 'published'); }
    public function scopeUpcoming($q) { return $q->published()->where('event_date', '>=', now()->toDateString()); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }

    public function user() { return $this->belongsTo(User::class); }
    public function category() { return $this->belongsTo(Category::class); }
    public function store() { return $this->belongsTo(Store::class); }
}
