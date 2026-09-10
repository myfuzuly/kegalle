<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\AdminNotification;

class Service extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'description',
        'service_type', 'pricing_model', 'price', 'location',
        'areas_covered', 'experience_years', 'phone', 'whatsapp', 'email',
        'image', 'status', 'views',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'experience_years' => 'integer',
        'views' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function ($service) {
            if (empty($service->slug)) {
                $base = Str::slug($service->title);
                $slug = $base;
                $i = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $service->slug = $slug;
            }
        });

        static::created(function (Service $service) {
            AdminNotification::log(
                'service_created',
                'New service submitted',
                $service->title,
                '/admin/services'
            );
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function getPriceLabelAttribute(): string
    {
        return match($this->pricing_model) {
            'hourly'     => 'per hour',
            'fixed'      => 'fixed',
            'free_quote' => 'Free Quote',
            default      => 'negotiable',
        };
    }
}
