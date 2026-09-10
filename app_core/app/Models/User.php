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
        'location_id',
        'account_type',
        'avatar',
        'social_provider',
        'social_provider_id',
        'phone_verified_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at'     => 'datetime',
        'is_verified'       => 'boolean',
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

        $role = $this->roleModel;
        if ($role) {
            return $role->allows($permission);
        }

        // No custom role assigned — plain "admin" gets all sections except role management
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
