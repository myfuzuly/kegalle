<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingVariant extends Model
{
    protected $fillable = ['listing_id', 'name', 'price', 'sort_order'];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
