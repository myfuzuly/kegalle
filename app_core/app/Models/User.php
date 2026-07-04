<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'allow_multiple_stores',
        'store_limit',
        'location_id',
        'account_type',
        'verification_token',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function roleModel()
    {
        return $this->belongsTo(Role::class, 'role', 'key');
    }

    public function isAdminLevel(): bool
    {
        if (in_array($this->role, ['admin', 'super_admin'], true)) {
            return true;
        }

        try {
            return (bool) optional($this->roleModel)->is_admin_level;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'super_admin') {
            return true;
        }

        try {
            $role = $this->roleModel;
            if ($role) {
                return $role->allows($permission);
            }
        } catch (\Throwable $e) {
            // roles table unavailable — fall through to legacy behaviour
        }

        // Legacy: plain "admin" role gets everything except role management
        return $this->role === 'admin' && $permission !== 'roles';
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function listings()
    {
        return $this->hasMany(Listing::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteListings()
    {
        return $this->belongsToMany(Listing::class, 'favorites')->withTimestamps();
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'super_admin'], true);
    }

    protected static function booted()
    {
        static::created(function (User $user) {
            if (! in_array($user->role, ['admin', 'super_admin'], true)) {
                AdminNotification::log(
                    'user_registered',
                    'New user registered',
                    $user->name.' ('.$user->email.')',
                    '/admin/users'
                );
            }
        });
    }
}
