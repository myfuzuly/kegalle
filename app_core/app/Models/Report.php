<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = ['user_id', 'listing_id', 'reason', 'details', 'status', 'reviewed_by', 'reviewed_at'];

    protected $casts = ['reviewed_at' => 'datetime'];

    public function user()     { return $this->belongsTo(User::class); }
    public function listing()  { return $this->belongsTo(Listing::class); }
    public function reviewer() { return $this->belongsTo(User::class, 'reviewed_by'); }
}
