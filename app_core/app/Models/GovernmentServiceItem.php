<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GovernmentServiceItem extends Model
{
    protected $fillable = [
        'government_service_id', 'name', 'description', 'phone', 'email',
        'address', 'map_url', 'image', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(GovernmentService::class, 'government_service_id');
    }
}
