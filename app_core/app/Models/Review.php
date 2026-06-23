<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['user_id', 'store_id', 'listing_id', 'rating', 'comment', 'status'];
}
