<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'store_id', 'listing_id', 'rating', 'comment', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
