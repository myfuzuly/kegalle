<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['key', 'name', 'description', 'is_admin_level', 'is_super', 'is_protected', 'sort_order', 'permissions'];

    protected $casts = [
        'is_admin_level' => 'boolean',
        'is_super' => 'boolean',
        'is_protected' => 'boolean',
        'permissions' => 'array',
    ];

    /**
     * Every admin-panel permission, keyed by the URL segment it protects.
     */
    public const PERMISSIONS = [
        'dashboard' => ['label' => 'Dashboard', 'group' => 'Overview'],
        'notifications' => ['label' => 'Notification Center', 'group' => 'Overview'],
        'approvals' => ['label' => 'Approvals', 'group' => 'Overview'],
        'inactive' => ['label' => 'Danger Zone', 'group' => 'Overview'],
        'users' => ['label' => 'Users', 'group' => 'Marketplace'],
        'stores' => ['label' => 'Stores', 'group' => 'Marketplace'],
        'listings' => ['label' => 'Products / Ads', 'group' => 'Marketplace'],
        'classifieds' => ['label' => 'Classifieds', 'group' => 'Marketplace'],
        'deals' => ['label' => 'Deals', 'group' => 'Marketplace'],
        'events' => ['label' => 'Events', 'group' => 'Marketplace'],
        'categories' => ['label' => 'Categories', 'group' => 'Marketplace'],
        'brands' => ['label' => 'Brands', 'group' => 'Marketplace'],
        'listing-fields' => ['label' => 'Listing Fields', 'group' => 'Marketplace'],
        'locations' => ['label' => 'Locations', 'group' => 'Marketplace'],
        'posts' => ['label' => 'Blog', 'group' => 'Content'],
        'explore-items' => ['label' => 'Explore Kegalle', 'group' => 'Content'],
        'government-services' => ['label' => 'Gov Services', 'group' => 'Content'],
        'memberships' => ['label' => 'Memberships', 'group' => 'Monetization'],
        'payments' => ['label' => 'Payments', 'group' => 'Monetization'],
        'ad-banners' => ['label' => 'Ad Spaces', 'group' => 'Monetization'],
        'reviews' => ['label' => 'Reviews', 'group' => 'Operations'],
        'chats' => ['label' => 'Chats', 'group' => 'Operations'],
        'settings' => ['label' => 'Settings', 'group' => 'Operations'],
        'roles' => ['label' => 'Role Management', 'group' => 'Operations'],
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'role', 'key');
    }

    public function allows(string $permission): bool
    {
        if ($this->is_super) {
            return true;
        }

        return in_array($permission, $this->permissions ?? [], true);
    }
}
