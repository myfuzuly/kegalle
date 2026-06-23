<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['user_id', 'membership_plan_id', 'gateway', 'reference', 'amount', 'currency', 'status', 'meta'];

    protected $casts = ['meta' => 'array'];
}
