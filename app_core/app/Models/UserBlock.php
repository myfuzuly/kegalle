<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBlock extends Model
{
    protected $fillable = ['blocker_id', 'blocked_id'];

    public function blocker() { return $this->belongsTo(User::class, 'blocker_id'); }
    public function blocked() { return $this->belongsTo(User::class, 'blocked_id'); }

    /** True when either user has blocked the other: no messages or offers in either direction. */
    public static function between(int $a, int $b): bool
    {
        return static::where(fn ($q) => $q->where('blocker_id', $a)->where('blocked_id', $b))
            ->orWhere(fn ($q) => $q->where('blocker_id', $b)->where('blocked_id', $a))
            ->exists();
    }
}
