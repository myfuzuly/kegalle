<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MembershipPlan extends Model
{
    protected $fillable = ['name', 'slug', 'price', 'duration_days', 'ad_limit', 'product_limit', 'store_limit', 'featured_quota', 'allowed_categories', 'features', 'is_active'];

    protected $casts = ['allowed_categories' => 'array', 'features' => 'array', 'is_active' => 'boolean'];
}
